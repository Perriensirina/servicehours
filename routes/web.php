<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\registerserviceController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [LandingController::class, 'index']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/login-success', function () {
    return 'Login successful';
})->name('login.success');

Route::get('/servicehours', function () {
    return view('servicehours');
})->name('servicehours');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/registerservice', [registerserviceController::class, 'index'])->name('registerservice');
Route::post('/registerservice', [registerserviceController::class, 'store'])->name('registerservice.store');
Route::get('/option3', [registerserviceController::class, 'overview'])->name('registerservice.overview');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);


// Register service hours: operator, teamleader, admin
Route::middleware(['auth', 'role:teamleader,admin'])->group(function () {
    Route::get('/registerservice', [registerserviceController::class, 'index'])->name('registerservice');
    Route::post('/registerservice', [registerserviceController::class, 'store'])->name('registerservice.store');
});


// Validate service hours: teamleader, admin
Route::middleware(['auth', 'role:teamleader,admin'])->group(function () {
    Route::get('/option2', [ValidationController::class, 'index'])->name('validate.hours');
    // add more validation-related routes here if needed
});

// Invoice client: admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/option4', [InvoiceController::class, 'index'])->name('invoice.client');
    // add more invoice-related routes here if needed
});


Route::get('/account', function () {
    return view('account');
})->middleware('auth')->name('account');

Route::get('/task/{task}',[registerserviceController::class,'show'])->name('tasks.show');

Route::post('/tasks/{task}/validate', [registerserviceController::class, 'validateTask'])
    ->name('tasks.validate');

Route::post('/tasks/{task}/users/{user}/start', [registerserviceController::class, 'startUserTask'])
    ->name('tasks.start.user');

Route::post('/tasks/{task}/users/{user}/stop', [registerserviceController::class, 'stopUserTask'])
    ->name('tasks.stop.user');


Route::get('/activetasks', [registerserviceController::class, 'activetasks'])
    ->name('activetasks.index');

Route::post('/tasks/{task}/start', [registerserviceController::class, 'startTask'])
    ->name('tasks.start');

Route::post('/tasks/{task}/stop', [registerserviceController::class, 'stopTask'])
    ->name('tasks.stop');



Route::post('/tasks/{task}/users/{user}/start', [RegisterServiceController::class, 'startUserTask'])
    ->name('tasks.start.user');

Route::post('/tasks/{task}/users/{user}/stop', [RegisterServiceController::class, 'stopUserTask'])
    ->name('tasks.stop.user');

Route::post('/tasks/{task}/update-time/{user}', [RegisterServiceController::class, 'updateTime'])
    ->name('tasks.updateTime')
    ->middleware('auth');

Route::post('/tasks/{task}/users/{user}/update-time', [RegisterServiceController::class, 'updateTime'])
    ->name('tasks.updateTime');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/tasks/{task}/invoice', [RegisterServiceController::class, 'invoiceTask'])
        ->name('tasks.invoice');

    Route::post('/tasks/bulk-invoice', [RegisterServiceController::class, 'bulkInvoice'])
        ->name('tasks.bulkInvoice');
});

Route::post('/tasks/{task}/invoice', [RegisterServiceController::class, 'invoiceTask'])
    ->name('tasks.invoice');


Route::get('/registerservice/export-csv', [registerserviceController::class, 'exportCsv'])
    ->name('registerservice.exportCsv');



Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('departments', DepartmentController::class)->except(['create','edit','show']);
});

// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
//     Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');

//     Route::put('/departments/{department}', [DepartmentController::class, 'updateDepartment'])->name('departments.update');
//     Route::delete('/departments/{department}', [DepartmentController::class, 'destroyDepartment'])->name('departments.destroy');

//     Route::put('/suppliers/{supplier}', [DepartmentController::class, 'updateSupplier'])->name('suppliers.update');
//     Route::delete('/suppliers/{supplier}', [DepartmentController::class, 'destroySupplier'])->name('suppliers.destroy');

// });

    Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');

    // Departments
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroyDepartment'])->name('departments.destroy');

    // Suppliers
    Route::put('/suppliers/{supplier}', [DepartmentController::class, 'updateSupplier'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [DepartmentController::class, 'destroySupplier'])->name('suppliers.destroy');

    // Zones
    Route::post('/zones', [DepartmentController::class, 'storeZone'])->name('zones.store');
    Route::put('/zones/{zone}', [DepartmentController::class, 'updateZone'])->name('zones.update');
    Route::delete('/zones/{zone}', [DepartmentController::class, 'destroyZone'])->name('zones.destroy');

    // Reasons
    Route::post('/reasons', [DepartmentController::class, 'storeReason'])->name('reasons.store');
    Route::put('/reasons/{reason}', [DepartmentController::class, 'updateReason'])->name('reasons.update');
    Route::delete('/reasons/{reason}', [DepartmentController::class, 'destroyReason'])->name('reasons.destroy');
});

Route::get('/activity-logs', [ActivityLogController::class, 'index'])
    ->middleware('auth') // only logged in users
    ->name('activity.logs');

Route::put('/departments/{department}/rate', [DepartmentController::class, 'updateDepartmentRate'])
    ->name('departments.updateRate');

