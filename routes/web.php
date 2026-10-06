<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\RegistrationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TrackController;
use App\Http\Controllers\Admin\BoardMemberController;
use App\Http\Controllers\Admin\StrengthController;
use App\Http\Controllers\Admin\WeaknessController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Board\DashboardController as BoardDashboardController;
use App\Http\Controllers\Board\ApplicationController as BoardApplicationController;
use App\Http\Controllers\Board\ReportController as BoardReportController;
use App\Http\Controllers\HighBoard\DashboardController as HighBoardDashboardController;
use App\Http\Controllers\HighBoard\ApplicationController as HighBoardApplicationController;
use App\Http\Controllers\HighBoard\ReportController as HighBoardReportController;
use App\Http\Controllers\HighBoard\ScheduleController as HighBoardScheduleController;
use App\Http\Controllers\HighBoard\TrackController as HighBoardTrackController;
use App\Http\Controllers\HighBoard\BoardMemberController as HighBoardBoardMemberController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('recruitment');
});

Route::get('/recruitment', [RegistrationController::class, 'recruitment'])
    ->name('recruitment');

Route::get('/register/slots', [RegistrationController::class, 'getAvailableSlots'])
    ->name('registration.slots');

Route::get('/register/{trackSlug}', [RegistrationController::class, 'createForTrack'])
    ->name('registration.track');

Route::post('/register', [RegistrationController::class, 'store'])
    ->name('registration.store');

Route::get('/register/success/{id}', [RegistrationController::class, 'success'])
    ->name('registration.success');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class);

    // Tracks
    Route::resource('tracks', TrackController::class);

    // Board Members
    Route::resource('board-members', BoardMemberController::class);

    // Strengths
    Route::resource('strengths', StrengthController::class);

    // Weaknesses
    Route::resource('weaknesses', WeaknessController::class);

    // Applications
    Route::resource('applications', ApplicationController::class);
    Route::post('applications/bulk-update', [ApplicationController::class, 'bulkUpdateStatus'])->name('applications.bulk-update');

    // Schedule
    Route::resource('schedule', ScheduleController::class);
    Route::post('schedule/bulk-create', [ScheduleController::class, 'bulkCreate'])->name('schedule.bulk-create');
    Route::post('schedule/copy-week', [ScheduleController::class, 'copyWeek'])->name('schedule.copy-week');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
});

/*
|--------------------------------------------------------------------------
| Board Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:board', 'track.access'])->prefix('board')->name('board.')->group(function () {
    // Dashboard
    Route::get('/', [BoardDashboardController::class, 'index'])->name('dashboard');

    // Applications
    Route::resource('applications', BoardApplicationController::class);

    // Reports
    Route::get('reports', [BoardReportController::class, 'index'])->name('reports.index');
    Route::get('reports/print', [BoardReportController::class, 'print'])->name('reports.print');
    Route::get('reports/export', [BoardReportController::class, 'export'])->name('reports.export');
    Route::get('reports/export-csv', [BoardReportController::class, 'exportCsv'])->name('reports.export-csv');
});

/*
|--------------------------------------------------------------------------
| High Board Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:highboard', 'track.access'])->prefix('highboard')->name('highboard.')->group(function () {
    // Dashboard
    Route::get('/', [HighBoardDashboardController::class, 'index'])->name('dashboard');

    // Applications
    Route::resource('applications', HighBoardApplicationController::class);

    // Reports
    Route::get('reports', [HighBoardReportController::class, 'index'])->name('reports.index');
    Route::get('reports/print', [HighBoardReportController::class, 'print'])->name('reports.print');
    Route::get('reports/export', [HighBoardReportController::class, 'export'])->name('reports.export');
    Route::get('reports/export-csv', [HighBoardReportController::class, 'exportCsv'])->name('reports.export-csv');

    // Schedule
    Route::resource('schedule', HighBoardScheduleController::class);
    Route::post('schedule/bulk-create', [HighBoardScheduleController::class, 'bulkCreate'])->name('schedule.bulk-create');
    Route::post('schedule/copy-week', [HighBoardScheduleController::class, 'copyWeek'])->name('schedule.copy-week');

    // Tracks
    Route::resource('tracks', HighBoardTrackController::class);

    // Board Members
    Route::resource('board-members', HighBoardBoardMemberController::class);
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Password Reset, etc.)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';