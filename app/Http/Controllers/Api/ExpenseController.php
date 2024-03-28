<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     summary="Get all expenses",
     *     tags={"Expenses"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="List of all expenses"),
     *     @OA\Response(response="404", description="No expenses found"),
     * )
     */
    public function index()
    {
        $expenses = Expense::where('user_id', Auth::id())->get();

        if ($expenses->isEmpty()) {
            return response()->json([
                'message' => 'No expenses found.',
            ], 404);
        }
        return response()->json(array('expenses' => $expenses));
    }

    /**
     * @OA\Post(
     *     path="/api/expenses",
     *     summary="Create a new expense",
     *     tags={"Expenses"},
     *     operationId="index_op",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "expense", "description"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="expense", type="number"),
     *             @OA\Property(property="description", type="string"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Expense created successfully"),
     *     @OA\Response(response=400, description="Bad Request"),
     * )
     */

    public function store(StoreExpenseRequest $request)
    {
        $userId = $request->user()->id;

        $expenseData = $request->all();
        $expenseData['user_id'] = $userId;

        $expense = Expense::create($expenseData);

        return response()->json(['message' => 'Expense created successfully.'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/{expense}",
     *     summary="Get a specific expense",
     *     operationId="showExpense",
     *     security={{"sanctum": {}}},
     *     description="Retrieve an Expense by its ID",
     *     tags={"Expenses"},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID of the expense",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Expense details"),
     *     @OA\Response(response=404, description="Expense not found"),
     * )
     */
    public function show(Expense $expense)
    {

        try {
            abort_if(!$expense, 404, 'Expense not found.');

            return response()->json([
                ['expense' => $expense]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
            ], 500);
        }

    }


    /**
     * @OA\Put(
     *     path="/api/expenses/{expense}",
     *     summary="Update an expense",
     *     tags={"Expenses"},
     *     description="Update an existing expense by its ID",
     *     operationId="updateExpense",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID of the expense",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Expense data to be updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="expense", type="number"),
     *             @OA\Property(property="description", type="string"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Expense updated successfully"),
     *     @OA\Response(response=404, description="Expense not found"),
     * )
     */

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {

        try {

            $this->authorize('update', $expense);

            abort_if(!$expense, 404, 'Expense not found.');

            $expense->update($request->all());

            return response()->json([
                'message' => 'Expense updated successfully.',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Something went wrong! Please try again.',
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/{expense}",
     *     summary="Delete an expense",
     *     tags={"Expenses"},
     *     description="Delete an existing expense by its ID",
     *     operationId="deleteExpense",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID of the expense",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Expense deleted successfully"),
     *     @OA\Response(response=404, description="Expense not found"),
     * )
     */
    public function destroy(Expense $expense)
    {

        try {

            $this->authorize('delete', $expense);

            abort_if(!$expense, 404, 'Expense not found.');

            $expense->delete();

            return response()->json([
                'message' => 'Expense deleted successfully.',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Something went wrong! Please try again.',
            ], 500);
        }
    }
}
