<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\SocialLogins;
use App\Http\Controllers\API\PDFController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\AdminDashboardController;
use App\Http\Controllers\API\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
        
Route::middleware(['auth:sanctum', 'auth'])->group( function () {
    Route::get('/user_details', function() {
        return auth()->user();
    });

    Route::resource('products', ProductController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('announcements', AnnouncementController::class);
    Route::get('/user/remove/{uuid}', [RegisterController::class, 'user_remove']); // for Remove users
    Route::get('/agencies', [RegisterController::class, 'getAgencies']);
    Route::get('/companies', [RegisterController::class, 'getCompanies']);

    Route::get('generate-pdf', [PDFController::class, 'generatePDF']); // Generate PDF Report
    Route::get('getRecentAgencies', [AdminDashboardController::class, 'getRecentAgencies']); // Get Recent Agencies

});

Route::controller(RegisterController::class)->group(function(){

    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('/destroy', 'destroy');
    Route::post('/verify-email', 'verify_email');

});

Route::controller(SocialLogins::class)->group(function() {

    Route::get('/login/{provider}', 'redirectToProvider');
    Route::get('/login/{provider}/callback', 'handleProviderCallback');

});

Route::get('/users-export', [PDFController::class, 'export']);
Route::post('/users-import', [PDFController::class, 'import']);
Route::post('/email-check-record', [ForgotPasswordController::class, 'checkEmail']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);






