@extends('admin.layout')

@section('title', 'Commandes')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">Commandes</h1>
        <p class="mt-2 text-sm text-gray-700">Dernières commandes reçues.</p>
    </div>
</div>
<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Référence</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Produit</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Client (Telegram)</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Montant</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($orders as $order)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $order->reference }}<br>
                                    <span class="text-xs text-gray-400">{{ $order->fedapay_transaction_id }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $order->product->name ?? 'Produit supprimé' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $order->telegram_username ? '@'.$order->telegram_username : 'Anonyme' }}<br>
                                    <span class="text-xs text-gray-400">ID: {{ $order->chat_id }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $order->amount_label }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @php
                                        $statusClass = match($order->status) {
                                            'paid' => 'bg-green-50 text-green-700 ring-green-600/20',
                                            'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                            'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
                                            'canceled' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                            default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                        };
                                        $statusLabel = match($order->status) {
                                            'paid' => 'Payé',
                                            'pending' => 'En attente',
                                            'failed' => 'Échoué',
                                            'canceled' => 'Annulé',
                                            default => $order->status,
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md {{ $statusClass }} px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    @if($order->status === 'paid')
                                        <form action="{{ route('admin.orders.resend', $order) }}" method="POST" class="inline-block" onsubmit="return confirm('Renvoyer le fichier au client ?');">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900">Renvoyer</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Aucune commande trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
