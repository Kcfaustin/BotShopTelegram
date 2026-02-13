<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\TelegramSession;
use App\Services\FedaPayClient;
use App\Services\TelegramBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class TelegramController extends Controller
{
    public function __construct(
        private readonly TelegramBot $bot,
        private readonly FedaPayClient $fedapay
    ) {
    }

    public function handle(Request $request)
    {
        $update = $request->all();
        Log::info('telegram.update', $update);

        $message = trim((string) data_get($update, 'message.text', ''));
        $chatId = (string) data_get($update, 'message.chat.id');
        $username = data_get($update, 'message.chat.username');

        if (!$chatId) {
            Log::warning('telegram.missing_chat_id');
            return response()->json(['status' => 'ignored']);
        }

        $session = TelegramSession::firstOrCreate(
            ['chat_id' => $chatId],
            ['username' => $username, 'locale' => 'fr']
        );

        $session->fill([
            'username' => $username ?: $session->username,
            'last_message_at' => now(),
        ])->save();

        if ($message === '') {
            $this->bot->sendMessage($chatId, "Je n'ai pas compris ton message. Tape /shop pour commencer.");
            return response()->json(['status' => 'empty']);
        }

        [$command, $argument] = $this->splitCommand($message);

        if ($command === '/start') {
            $this->handleStart($session, $chatId);
        } elseif ($command === '/shop') {
            $this->handleShop($session, $chatId);
        } elseif ($command === '/buy') {
            $this->handlePurchaseCommand($chatId, $argument, $session, $username);
        } elseif ($command === '/status') {
            $this->sendLatestOrderStatus($chatId);
        } elseif ($session->state === TelegramSession::STATE_AWAITING_PRODUCT) {
            $this->handlePurchaseCommand($chatId, $message, $session, $username);
        } else {
            $this->bot->sendMessage($chatId, 'Commande inconnue. Utilise /shop pour voir la boutique.');
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleStart(TelegramSession $session, string $chatId): void
    {
        $session->update(['state' => null, 'payload' => null]);

        $text = implode("\n", [
            'Bienvenue dans la boutique 🚀',
            'Envie de produits digitaux prêts à livrer ? Utilise les commandes ci-dessous :',
            '/shop — Voir les produits',
            '/status — Voir ta dernière commande',
        ]);

        $this->bot->sendMessage($chatId, $text);
    }

    private function handleShop(TelegramSession $session, string $chatId): void
    {
        $session->update(['state' => TelegramSession::STATE_AWAITING_PRODUCT]);
        $this->sendProductList($chatId);
    }

    private function splitCommand(string $message): array
    {
        $parts = preg_split('/\s+/', $message, 2);
        $command = strtolower($parts[0]);
        $argument = $parts[1] ?? null;

        return [$command, $argument];
    }

    private function sendProductList(string $chatId): void
    {
        $products = Product::active()->orderByDesc('is_active')->orderBy('price')->get();

        if ($products->isEmpty()) {
            $this->bot->sendMessage($chatId, 'Aucun produit n\'est disponible pour le moment.');
            return;
        }

        $lines = $products->map(function (Product $product, int $index) {
            return sprintf(
                "%d. *%s* — %s\n/buy %s",
                $index + 1,
                $product->name,
                $product->price_label,
                $product->slug
            );
        });

        $text = "Voici les produits disponibles :\n\n".implode("\n\n", $lines->toArray());
        $this->bot->sendMessage($chatId, $text, ['parse_mode' => 'Markdown']);
    }

    private function handlePurchaseCommand(string $chatId, ?string $argument, TelegramSession $session, ?string $username): void
    {
        if (!$argument) {
            $this->bot->sendMessage($chatId, 'Indique le code du produit. Exemple : /buy pack-premium');
            return;
        }

        $product = $this->resolveProduct($argument);
        if (!$product) {
            $this->bot->sendMessage($chatId, 'Produit introuvable. Tape /shop pour voir la liste.');
            return;
        }

        $order = Order::create([
            'product_id' => $product->id,
            'chat_id' => $chatId,
            'telegram_username' => $username,
            'reference' => Order::generateReference(),
            'status' => Order::STATUS_PENDING,
            'total_amount' => $product->price,
            'currency' => $product->currency,
        ]);

        try {
            $payment = $this->fedapay->createTransaction($order);
            Log::info('fedapay.transaction_created', [
                'order_id' => $order->id,
                'reference' => $order->reference,
                'response' => $payment,
            ]);
        } catch (Throwable $exception) {
            Log::error('telegram.fedapay_failed', [
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);

            $this->bot->sendMessage($chatId, 'Impossible de générer le lien de paiement. Réessaie dans quelques minutes.');
            return;
        }

        $order->update([
            'payment_url' => $payment['payment_url'] ?? null,
            'fedapay_transaction_id' => $payment['transaction_id'] ?? null,
            'fedapay_reference' => $payment['reference'] ?? null,
            'payment_payload' => $payment['raw'] ?? $payment,
        ]);

        Log::info('order.payment_synced', [
            'order_id' => $order->id,
            'reference' => $order->reference,
            'fedapay_reference' => $order->fresh()->fedapay_reference,
            'fedapay_transaction_id' => $order->fedapay_transaction_id,
        ]);

        $session->update(['state' => null]);

        $text = sprintf(
            "Commande *%s* créée ✅\nMontant : %s\n\nPayer maintenant 👉 %s\n\nTu recevras automatiquement ton fichier après validation.",
            $product->name,
            $order->amount_label,
            $order->payment_url ?? 'Lien non disponible'
        );

        $this->bot->sendMessage($chatId, $text, ['parse_mode' => 'Markdown']);
    }

    private function resolveProduct(string $input): ?Product
    {
        $slug = Str::slug($input);
        $id = filter_var($input, FILTER_VALIDATE_INT);

        return Product::active()
            ->where(function ($query) use ($slug, $id, $input) {
                $query->where('slug', $slug);

                if ($id) {
                    $query->orWhere('id', $id);
                }

                $query->orWhere('name', 'like', '%'.$input.'%');
            })
            ->first();
    }

    private function sendLatestOrderStatus(string $chatId): void
    {
        $order = Order::where('chat_id', $chatId)->latest()->first();

        if (!$order) {
            $this->bot->sendMessage($chatId, 'Aucune commande trouvée. Lance /shop pour acheter un produit.');
            return;
        }

        $statusText = match ($order->status) {
            Order::STATUS_PENDING => 'en attente de paiement',
            Order::STATUS_PAID => 'payée ✅',
            Order::STATUS_FAILED => 'échouée ❌',
            Order::STATUS_CANCELED => 'annulée',
            default => $order->status,
        };

        $message = sprintf(
            "Commande %s — %s\nMontant : %s",
            $order->reference,
            $statusText,
            $order->amount_label
        );

        if ($order->status === Order::STATUS_PENDING && $order->payment_url) {
            $message .= "\nLien de paiement : {$order->payment_url}";
        }

        $this->bot->sendMessage($chatId, $message);
    }
}
