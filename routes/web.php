<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\TitipanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin & Accounting Routes
    Route::middleware('role:admin,accounting')->group(function () {
        // PENTING: Routes untuk fitur penggunaan titipan otomatis HARUS di atas Route::resource
        Route::get('/transactions/create-with-titipan-confirmation', [TransactionController::class, 'createWithTitipanConfirmation'])->name('transactions.create-with-titipan-confirmation');
        Route::post('/transactions/use-titipan-for-piutang', [TransactionController::class, 'useTitipanForPiutang'])->name('transactions.use-titipan-for-piutang');

        // Resource routes
        Route::resource('transactions', TransactionController::class);
        Route::resource('titipans', TitipanController::class);
    });

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('debtors', DebtorController::class);
        Route::resource('users', UserController::class);

        // Routes untuk pengecekan ketersediaan titipan
        Route::get('/debtors/{id}/check-titipan', [DebtorController::class, 'checkTitipan'])->name('debtors.check-titipan');
        Route::get('/debtors/{id}/check-relations', [DebtorController::class, 'checkRelations'])->name('debtors.check-relations');
    });

    // Journal Routes (Admin & Accounting)
    Route::middleware('role:admin,accounting')->group(function () {
        Route::get('journal', [JournalController::class, 'index'])->name('journal.index');
        Route::get('journal/{id}', [JournalController::class, 'show'])->name('journal.show');
    });

    // Report Routes (Admin & Accounting)
    Route::middleware('role:admin,accounting')->prefix('reports')->group(function () {
        // Kartu Mutasi
        Route::get('kartu-mutasi', [ReportController::class, 'kartuMutasi'])->name('reports.kartu-mutasi');
        Route::get('kartu-mutasi/{id}', [ReportController::class, 'showKartuMutasi'])->name('reports.kartu-mutasi.show');
        Route::get('export-kartu-mutasi', [ReportController::class, 'exportKartuMutasi'])->name('reports.export-kartu-mutasi');

        // Piutang Per Bulan
        Route::get('piutang-perbulan', [ReportController::class, 'piutangPerBulan'])->name('reports.piutang-perbulan');
        Route::get('export-piutang-perbulan', [ReportController::class, 'exportPiutangPerBulan'])->name('reports.export-piutang-perbulan');

        // Pembayaran Per Bulan
        Route::get('pembayaran-perbulan', [ReportController::class, 'pembayaranPerBulan'])->name('reports.pembayaran-perbulan');
        Route::get('export-pembayaran-perbulan', [ReportController::class, 'exportPembayaranPerBulan'])->name('reports.export-pembayaran-perbulan');

        // Debit Piutang
        Route::get('debit-piutang', [ReportController::class, 'debitPiutang'])->name('reports.debit-piutang');
        Route::get('export-debit-piutang', [ReportController::class, 'exportDebitPiutang'])->name('reports.export-debit-piutang');
    });

    // Settings Routes
    Route::prefix('settings')->group(function () {
        // Profile Settings (All authenticated users)
        Route::get('profile', [SettingController::class, 'profile'])->name('settings.profile');
        Route::put('profile', [SettingController::class, 'updateProfile'])->name('settings.update-profile');
        Route::get('password', [SettingController::class, 'password'])->name('settings.password');
        Route::put('password', [SettingController::class, 'updatePassword'])->name('settings.update-password');

        // Application Settings (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::get('application', [SettingController::class, 'application'])->name('settings.application');
            Route::put('application', [SettingController::class, 'updateApplication'])->name('settings.update-application');
            Route::get('backup', [SettingController::class, 'backup'])->name('settings.backup');
            Route::post('backup', [SettingController::class, 'createBackup'])->name('settings.create-backup');
            Route::get('backup/download/{filename}', [SettingController::class, 'downloadBackup'])->name('settings.download-backup');
            Route::delete('backup/{filename}', [SettingController::class, 'deleteBackup'])->name('settings.delete-backup');
            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('settings.activity-logs');
        });
    });
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});
