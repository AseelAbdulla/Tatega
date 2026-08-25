<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of wallets.
     */
   public function index()
{

    $wallets = Wallet::latest()->get();

    return response()->json([
        'success' => true,
        'data' => $wallets,
    ]);
}

    /**
     * Store a newly created wallet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:255',
        ]);

        $wallet = Wallet::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة المحفظة بنجاح',
            'data' => $wallet,
        ], 201);
    }

    /**
     * Display the specified wallet.
     */
    public function show(string $id)
    {
        $wallet = Wallet::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $wallet,
        ]);
    }

    /**
     * Update the specified wallet.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:255',
        ]);

        $wallet = Wallet::findOrFail($id);

        $wallet->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل المحفظة بنجاح',
            'data' => $wallet,
        ]);
    }

    /**
     * Remove the specified wallet.
     */
    public function destroy(string $id)
    {
        $wallet = Wallet::findOrFail($id);

        $wallet->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المحفظة بنجاح',
        ]);
    }
}