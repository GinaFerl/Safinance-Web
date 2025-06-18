<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $monthlyReports = MonthlyReport::all();
        return response()->json([
            'message' => 'Monthly reports retrieved successfully',
            'data' => $monthlyReports
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string|max:255', // Anda bisa menggunakan format 'YYYY-MM' untuk bulan
            'total_income' => 'required|numeric',
            'total_expense' => 'required|numeric',
            'cash_balance' => 'required|numeric',
        ]);

        $monthlyReport = MonthlyReport::create($request->all());

        return response()->json([
            'message' => 'Monthly report created successfully',
            'data' => $monthlyReport
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $monthlyReport = MonthlyReport::find($id);

        if (!$monthlyReport) {
            return response()->json(['message' => 'Monthly report not found'], 404);
        }

        return response()->json([
            'message' => 'Monthly report retrieved successfully',
            'data' => $monthlyReport
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $monthlyReport = MonthlyReport::find($id);

        if (!$monthlyReport) {
            return response()->json(['message' => 'Monthly report not found'], 404);
        }

        $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'month' => 'sometimes|required|string|max:255',
            'total_income' => 'sometimes|required|numeric',
            'total_expense' => 'sometimes|required|numeric',
            'cash_balance' => 'sometimes|required|numeric',
        ]);

        $monthlyReport->update($request->all());

        return response()->json([
            'message' => 'Monthly report updated successfully',
            'data' => $monthlyReport
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $monthlyReport = MonthlyReport::find($id);

        if (!$monthlyReport) {
            return response()->json(['message' => 'Monthly report not found'], 404);
        }

        $monthlyReport->delete();

        return response()->json([
            'message' => 'Monthly report deleted successfully'
        ], 200);
    }
}
