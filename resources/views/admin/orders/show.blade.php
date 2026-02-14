@extends('admin.layout')

@section('title', 'Détails Commande #' . $order->reference)

@section('content')
<div class="px-4 sm:px-0 mb-6">
    <h3 class="text-base font-semibold leading-7 text-gray-900">Information Commande</h3>
    <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Détails de la transaction et du client.</p>
</div>
<div class="mt-6 border-t border-gray-100">
    <dl class="divide-y divide-gray-100">
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Référence</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $order->reference }}</dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Statut</dt>
            <dd class="mt-1 text-sm leading-6 sm:col-span-2 sm:mt-0">
                 @php
                    $statusClass = match($order->status) {
                        'paid' => 'bg-green-50 text-green-700 ring-green-600/20',
                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                        'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
                        'canceled' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                        default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                    };
                @endphp
                <span class="inline-flex items-center rounded-md {{ $statusClass }} px-2 py-1 text-xs font-medium ring-1 ring-inset">
                    {{ $order->status }}
                </span>
            </dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Produit</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $order->product->name ?? 'Produit Inconnu' }}</dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Montant</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $order->amount_label }}</dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Client Telegram</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                Username: {{ $order->telegram_username ? '@'.$order->telegram_username : '-' }}<br>
                Chat ID: {{ $order->chat_id }}
            </dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Transaction FedaPay</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                ID: {{ $order->fedapay_transaction_id ?? '-' }}<br>
                Réf: {{ $order->fedapay_reference ?? '-' }}
            </dd>
        </div>
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Dates</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                Créé le: {{ $order->created_at->format('d/m/Y H:i:s') }}<br>
                Payé le: {{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i:s') : '-' }}<br>
                Livré le: {{ $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i:s') : '-' }}
            </dd>
        </div>
         <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Actions</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                 @if($order->status === 'paid')
                    <form action="{{ route('admin.orders.resend', $order) }}" method="POST" class="inline-block" onsubmit="return confirm('Renvoyer le fichier au client ?');">
                        @csrf
                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Renvoyer le fichier sur Telegram</button>
                    </form>
                @else
                    <span class="text-gray-500 italic">Aucune action disponible pour ce statut.</span>
                @endif
            </dd>
        </div>
    </dl>
</div>
<div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
     <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Retour à la liste</a>
</div>
@endsection
