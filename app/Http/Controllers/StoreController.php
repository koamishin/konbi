<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        // Strict Data Isolation: Only show stores the user is allowed to see.
        // Super Admins can see all. Regular users see only their assigned store.
        
        $user = $request->user();
        
        if ($user->role === UserRole::SUPER_ADMIN) {
            $stores = Store::all();
        } else {
            // For regular users, only show their own store (if assigned)
            $stores = $user->store_id ? Store::where('id', $user->store_id)->get() : [];
        }

        return Inertia::render('Stores/Index', [
            'stores' => $stores
        ]);
    }

    public function create()
    {
        return Inertia::render('Stores/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:stores,code',
            'email' => 'nullable|email',
        ]);

        $store = Store::create($validated);
        $user = $request->user();

        // If user has no store (new user), assign this store to them and make them Manager
        if (!$user->store_id && $user->role !== UserRole::SUPER_ADMIN) {
            $user->update([
                'store_id' => $store->id,
                'role' => UserRole::STORE_MANAGER,
            ]);
        }

        return redirect()->route('stores.index');
    }

    public function edit(Store $store)
    {
        return Inertia::render('Stores/Edit', ['store' => $store]);
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:stores,code,' . $store->id,
            'email' => 'nullable|email',
        ]);

        $store->update($validated);

        return redirect()->route('stores.index');
    }
}
