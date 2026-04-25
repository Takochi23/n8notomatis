<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * GET /api/transactions?user_id=xxx
     */
    public function index(Request $request)
    {
        $query = Transaction::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $transactions = $query->orderBy('tanggal', 'desc')->get();

        return response()->json($transactions);
    }

    /**
     * POST /api/transactions
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'user_id' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create($validated);

        return response()->json($transaction, 201);
    }

    /**
     * DELETE /api/transactions/{id}
     */
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }
}
