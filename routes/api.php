<?php

use App\Http\Controllers\Api\BankWebhookController;
use App\Http\Controllers\Api\GroupSplitController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/bank-transfer', [BankWebhookController::class, 'handleBankTransfer'])
    ->name('api.webhooks.bank_transfer');

Route::middleware('auth')->group(function () {
    Route::post('/group-splits', [GroupSplitController::class, 'store']);
    Route::patch('/group-splits/{id}/cancel', [GroupSplitController::class, 'cancel']);
    Route::patch('/group-splits/{id}/return', [GroupSplitController::class, 'returnGuest']);
    Route::patch('/group-splits/{id}/extend', [GroupSplitController::class, 'extend']);
    Route::get('/group-splits', [GroupSplitController::class, 'index']);
});
