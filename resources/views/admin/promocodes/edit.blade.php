@extends('admin.layout')

@section('title', 'Modifier Code Promo')

@section('content')
<div class="max-w-xl mx-auto">
    <form action="{{ route('admin.promocodes.update', $promocode) }}" method="POST" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
        @csrf
        @method('PUT')
        <div class="px-4 py-6 sm:p-8">
            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                
                <div class="sm:col-span-4">
                    <label for="code" class="block text-sm font-medium leading-6 text-gray-900">Code (ex: PROMO10)</label>
                    <div class="mt-2">
                        <input type="text" name="code" id="code" value="{{ old('code', $promocode->code) }}" required style="text-transform: uppercase" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Type</label>
                    <div class="mt-2">
                        <select id="type" name="type" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            <option value="percent" {{ $promocode->type == 'percent' ? 'selected' : '' }}>Pourcentage (%)</option>
                            <option value="fixed" {{ $promocode->type == 'fixed' ? 'selected' : '' }}>Montant fixe (XOF)</option>
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="value" class="block text-sm font-medium leading-6 text-gray-900">Valeur</label>
                    <div class="mt-2">
                        <input type="number" name="value" id="value" value="{{ old('value', $promocode->value) }}" required min="1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="usage_limit" class="block text-sm font-medium leading-6 text-gray-900">Limite d'usage (Optionnel)</label>
                    <div class="mt-2">
                        <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $promocode->usage_limit) }}" min="1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="expires_at" class="block text-sm font-medium leading-6 text-gray-900">Expiration (Optionnel)</label>
                    <div class="mt-2">
                        <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', $promocode->expires_at ? $promocode->expires_at->format('Y-m-d') : '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                 <div class="col-span-full">
                    <div class="relative flex gap-x-3">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ $promocode->is_active ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <div class="text-sm leading-6">
                            <label for="is_active" class="font-medium text-gray-900">Actif</label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
            <a href="{{ route('admin.promocodes.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Annuler</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
