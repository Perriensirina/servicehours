<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\registerserviceController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All application routes are defined here.
|
*/

// ------------------------------
// 🔹 Public Routes
// ------------------------------
Route::get('/', [LandingController::class, 'index']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/login-success', function () {
    return 'Login successful';
})->name('login.success');

// Servicehours overview page
Route::get('/servicehours', function () {
    return view('servicehours');
})->name('servicehours');

// Logout
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ------------------------------
// 🔹 Authentication & Registration
// ------------------------------
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);


// ------------------------------
// 🔹 Protected Routes (Authenticated users only)
// ------------------------------
Route::middleware(['auth'])->group(function () {

    // Account page
    Route::get('/account', function () {
        return view('account');
    })->name('account');

    // ----------------------------------
    // Register Service (Teamleader/Admin)
    // ----------------------------------
    Route::middleware(['role:teamleader,admin'])->group(function () {
        Route::get('/registerservice', [registerserviceController::class, 'index'])
            ->name('registerservice');
        Route::post('/registerservice', [registerserviceController::class, 'store'])
            ->name('registerservice.store');
    });

    // Overview (visible to all logged-in users)
    Route::get('/registerservice/overview', [registerserviceController::class, 'overview'])
        ->name('registerservice.overview');
    
    Route::delete('/tasks/{task}', [RegisterServiceController::class, 'destroy'])->name('tasks.destroy');

    // Export CSV
    Route::get('/registerservice/export-csv', [registerserviceController::class, 'exportCsv'])
        ->name('registerservice.exportCsv');
    Route::get('/overview', [registerserviceController::class, 'overview'])->name('overview');

    // ----------------------------------
    // Task Management
    // ----------------------------------
    Route::get('/task/{task}', [registerserviceController::class, 'show'])
        ->name('tasks.show');

    Route::post('/tasks/{task}/validate', [registerserviceController::class, 'validateTask'])
        ->name('tasks.validate');

    Route::get('/activetasks', [registerserviceController::class, 'activeTasks'])
        ->name('activetasks.index');

    Route::post('/tasks/{task}/start', [registerserviceController::class, 'startTask'])
        ->name('tasks.start');

    Route::post('/tasks/{task}/stop', [registerserviceController::class, 'stopTask'])
        ->name('tasks.stop');

    // User-specific start/stop
    Route::post('/tasks/{task}/users/{user}/start', [registerserviceController::class, 'startUserTask'])
        ->name('tasks.start.user');

    Route::post('/tasks/{task}/users/{user}/stop', [registerserviceController::class, 'stopUserTask'])
        ->name('tasks.stop.user');

    // Update time (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/tasks/{task}/update-time/{user}', [registerserviceController::class, 'updateTime'])
            ->name('tasks.updateTime');

        // Invoicing
        Route::post('/tasks/{task}/invoice', [registerserviceController::class, 'invoiceTask'])
            ->name('tasks.invoice');

        Route::post('/tasks/bulk-invoice', [registerserviceController::class, 'bulkInvoice'])
            ->name('tasks.bulkInvoice');
    });

    // ----------------------------------
    // Validation (Teamleader/Admin)
    // ----------------------------------
    // Route::middleware(['role:teamleader,admin'])->group(function () {
    //     Route::get('/option2', [ValidationController::class, 'index'])
    //         ->name('validate.hours');
    // });

    // ----------------------------------
    // Invoicing (Admin only)
    // ----------------------------------
    // Route::middleware(['role:admin'])->group(function () {
    //     Route::get('/option4', [InvoiceController::class, 'index'])
    //         ->name('invoice.client');
    // });

    // ----------------------------------
    // Department / Supplier / Zone / Reason Management (Admin only)
    // ----------------------------------
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('departments', DepartmentController::class)
            ->except(['create', 'edit', 'show']);

        Route::get('/departments', [DepartmentController::class, 'index'])
            ->name('departments.index');

        Route::post('/departments', [DepartmentController::class, 'store'])
            ->name('departments.store');

        Route::put('/departments/{department}', [DepartmentController::class, 'updateDepartment'])
            ->name('departments.update');

        Route::delete('/departments/{department}', [DepartmentController::class, 'destroyDepartment'])
            ->name('departments.destroy');

        // Suppliers
        Route::put('/suppliers/{supplier}', [DepartmentController::class, 'updateSupplier'])
            ->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [DepartmentController::class, 'destroySupplier'])
            ->name('suppliers.destroy');

        // Zones
        Route::post('/zones', [DepartmentController::class, 'storeZone'])
            ->name('zones.store');
        Route::put('/zones/{zone}', [DepartmentController::class, 'updateZone'])
            ->name('zones.update');
        Route::delete('/zones/{zone}', [DepartmentController::class, 'destroyZone'])
            ->name('zones.destroy');

        // Reasons
        Route::post('/reasons', [DepartmentController::class, 'storeReason'])
            ->name('reasons.store');
        Route::put('/reasons/{reason}', [DepartmentController::class, 'updateReason'])
            ->name('reasons.update');
        Route::delete('/reasons/{reason}', [DepartmentController::class, 'destroyReason'])
            ->name('reasons.destroy');

        // Department rate update
        Route::put('/departments/{department}/rate', [DepartmentController::class, 'updateDepartmentRate'])
            ->name('departments.updateRate');
    });

    // ----------------------------------
    // Activity Logs (All authenticated users)
    // ----------------------------------
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity.logs');
});
