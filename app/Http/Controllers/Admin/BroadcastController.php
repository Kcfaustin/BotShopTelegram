<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\TelegramBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BroadcastController extends Controller
{
    public function create()
    {
        return view('admin.broadcast.create');
    }

    public function send(Request $request, TelegramBot $bot)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5',
        ]);

        // Get unique chat IDs from orders (active users)
        // In a real app, you should have a User model or TelegramUser model.
        // We'll use Order's chat_id as a proxy for users.
        $chatIds = Order::distinct()->pluck('chat_id');

        $count = 0;
        foreach ($chatIds as $chatId) {
            try {
                $bot->sendMessage($chatId, $validated['message']);
                $count++;
                // Add a small delay to respect Telegram rate limits (30 msg/sec max globally, but safer to go slow)
                usleep(50000); // 0.05s
            } catch (\Throwable $e) {
                Log::warning("Broadcast failed for $chatId: " . $e->getMessage());
            }
        }

        return back()->with('success', "Message envoyé à $count utilisateurs !");
    }
}
