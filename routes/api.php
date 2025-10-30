<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CenterController;
use App\Http\Controllers\Api\Center\SaleController;
use App\Http\Controllers\Api\NotifyController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/employees', [EmployeeController::class, 'store'])->name('add_employee');
    Route::post('/grant_role', [EmployeeController::class, 'grant_role'])->name('grant_role');
    //Route::post('/roles', [EmployeeController::class, 'show_roles'])->name('show_roles');
    Route::post('/show_employees', [EmployeeController::class, 'show_employees'])->name('show_employees');
    Route::post('/add_product', [ProductController::class, 'store'])->name('add_product');
    Route::post('/product/show', [ProductController::class, 'product_by_id'])->name('product_by_id');        
    Route::post('/product/show/all', [ProductController::class, 'all_products'])->name('all_products');
    Route::post('/product/update', [ProductController::class, 'update'])->name('update_product'); 
    Route::post('/product/delete', [ProductController::class, 'delete'])->name('delete_product'); 
    Route::post('/product/image/delete', [ProductController::class, 'deleteImg'])->name('deleteImg'); 

        
        
    Route::post('/add_center', [CenterController::class, 'store'])->name('add_center');
    Route::post('/centers', [CenterController::class, 'centers'])->name('centers');
    Route::post('/centers/products', [ProductController::class, 'store'])
        ->middleware('has.center.permission:view_sales');
    Route::post('/centers/roles', [EmployeeController::class, 'show_roles'])
        ->middleware('has.center.role:center_admin');
        
    Route::post('/centers/information', [CenterController::class, 'info'])->middleware('has.center.role:center_admin');        
    Route::post('/center/employees', [EmployeeController::class, 'EmpByCenter']);
    
});
//Route::post('/register', [AuthController::class, 'register'])->name('register');

/*Route::prefix('customer')->group(function () {
    Route::post('register', [CustomerAuth::class, 'register']);
    Route::post('login', [CustomerAuth::class, 'login']);
});*/
Route::prefix('center')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\Center\AuthController::class, 'register']);
});

Route::prefix('employee')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\Employee\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\Center\AuthController::class, 'login']);
});
Route::post('/create_account', [EmployeeController::class, 'create_account'])->name('create_account');


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/', function () {
    return view('page1');
});

Route::prefix('Center')->group(function () {

        Route::post('/TodaySales', [SaleController::class, 'TodaySales'])->name('TodaySales');
        Route::post('/DailySales', [SaleController::class, 'DailySales'])->name('DailySales');

        Route::post('/MonthSales', [SaleController::class, 'MonthSales'])->name('MonthSales');
        Route::post('/MonthlySales', [SaleController::class, 'MonthlySales'])->name('MonthlySales');

        Route::post('/YearSales', [SaleController::class, 'YearSales'])->name('YearSales');
        Route::post('/YearlySales', [SaleController::class, 'YearlySales'])->name('YearlySales');        
        
        Route::post('/addSale', [SaleController::class, 'addSale'])->name('addSale');


});

Route::prefix('AllCenter')->group(function () {


        Route::post('/DailySales', [SaleController::class, 'AllDailySales'])->name('AllDailySales');

        Route::post('/MonthlySales', [SaleController::class, 'AllMonthlySales'])->name('AllMonthlySales');

        Route::post('/YearlySales', [SaleController::class, 'AllYearlySales'])->name('AllYearlySales');        
        
});


Route::post('/sendTestNotification', [NotifyController::class, 'sendTestNotification'])->name('sendTestNotification');



