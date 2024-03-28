<?php

use App\Models\Expense;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});


test('authenticated user can create an Expense', function () {
    $expenseData = [
        'title' => 'Test Expense',
        'description' => 'Test Description',
        'expense' => 100,
    ];

    $this->actingAs($this->user)
        ->postJson('/api/expenses', $expenseData)
        ->assertStatus(201)
        ->assertJson(['message' => 'Expense created successfully.']);
});



test('authenticated user can view an Expense', function () {
    $expense = Expense::factory()->create();

    $this->actingAs($expense->user)
        ->get("/api/expenses/{$expense->id}")
        ->assertStatus(200)
        ->assertJson([
            [
                'expense' => [
                    'title' => $expense->title,
                    'description' => $expense->description,
                    'expense' => $expense->expense,
                    'user_id' => $expense->user_id,
                ]
            ]
        ]);
});


test('authenticated user cannot access Expenses', function () {
    $this->get('/api/expenses')
    ->assertStatus(404)
        ->assertJson(['message' => 'No expenses found.']);
});


test('authenticated user can delete an Expense', function () {
    $expense = Expense::factory()->create();

    $this->actingAs($expense->user)
        ->delete("/api/expenses/{$expense->id}")
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Expense deleted successfully.',
        ]);

    // Assert that the expense is no longer in the database
    $this->assertDatabaseMissing('expenses', [
        'id' => $expense->id,
    ]);
});


test('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);


test('sum', function () {
    $value = 1 + 2;

    expect($value)->toBe(3);
});




