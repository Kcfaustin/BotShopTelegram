<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::latest()->paginate(10);
        return view('admin.promocodes.index', compact('promoCodes'));
    }

    public function create()
    {
        return view('admin.promocodes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:promo_codes,code|max:20',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|integer|min:1',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        PromoCode::create([
            'code' => Str::upper($validated['code']),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'usage_limit' => $validated['usage_limit'],
            'expires_at' => $validated['expires_at'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.promocodes.index')->with('success', 'Code promo créé !');
    }

    public function edit(PromoCode $promocode)
    {
        return view('admin.promocodes.edit', compact('promocode'));
    }

    public function update(Request $request, PromoCode $promocode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:promo_codes,code,' . $promocode->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|integer|min:1',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $promocode->update([
            'code' => Str::upper($validated['code']),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'usage_limit' => $validated['usage_limit'],
            'expires_at' => $validated['expires_at'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.promocodes.index')->with('success', 'Code promo mis à jour !');
    }

    public function destroy(PromoCode $promocode)
    {
        $promocode->delete();
        return redirect()->route('admin.promocodes.index')->with('success', 'Code promo supprimé !');
    }
}
