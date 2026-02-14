@extends('admin.layout')

@section('title', 'Diffusion Message')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white px-4 py-5 shadow sm:max-w-3xl sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Message à tous</h3>
                <p class="mt-1 text-sm text-gray-500">Envoyez une annonce à tous les utilisateurs qui ont déjà interagi avec le bot.</p>
            </div>
            <div class="mt-5 md:col-span-2 md:mt-0">
                <form action="{{ route('admin.broadcast.send') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="message" class="block text-sm font-medium text-gray-700">Votre message</label>
                            <div class="mt-1">
                                <textarea id="message" name="message" rows="5" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" placeholder="Bonjour à tous ! Nouvelle promotion..." required></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Le message sera envoyé immédiatement.</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" onclick="return confirm('Confirmer l\'envoi à tous les utilisateurs ?')">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
