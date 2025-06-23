<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load the 'user' relationship to include user data in the response
        $transactions = TransactionResource::collection(Transaction::with('user')->get());
        return response()->json([
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric',
                'type' => ['required', Rule::in(['income', 'expense'])],
                'category' => 'nullable|string|max:255',
                'date' => 'required|date',
                'description' => 'nullable|string',
                'ticket_number' => 'nullable|string|max:255',
                'user_id' => 'required|exists:users,id',
            ]);

            $transaction = Transaction::create($validated);

            return response()->json([
                'message' => 'Transaction created successfully',
                'data' => $transaction
            ], 201);

        } catch (ValidationException $e) {
            // Tangkap validasi dan balikan errornya
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Tangkap error umum lainnya
            return response()->json([
                'message' => 'Terjadi kesalahan internal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json([
            'message' => 'Transaction retrieved successfully',
            'data' => $transaction
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric',
            'type' => ['sometimes', 'required', Rule::in(['income', 'expense'])],
            'category' => 'nullable|string|max:255',
            'date' => 'sometimes|required|date',
            'description' => 'nullable|string',
            'ticket_number' => 'nullable|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        $transaction->update($request->all());

        return response()->json([
            'message' => 'Transaction updated successfully',
            'data' => $transaction
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ], 200);
    }
}
