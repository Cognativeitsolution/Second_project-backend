<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\SocialLogins;
use App\Http\Controllers\API\PDFController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserUpdateController;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\AdminDashboardController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\CountriesController;
use App\Http\Controllers\API\StatesController;
use App\Http\Controllers\API\CitiesController;
use App\Http\Controllers\API\SubAgencyController;

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
    Route::get('/user/edit/{uuid}', [UserUpdateController::class, 'user_edit']);
    Route::put('/user/update/{user:uuid}', [UserUpdateController::class, 'user_update']);
    Route::get('/agencies', [RegisterController::class, 'getAgencies']);
    Route::get('/companies', [RegisterController::class, 'getCompanies']);
    Route::post('/delete-accounts', [RegisterController::class, 'deleteAccounts']); // POST ids array
    Route::post('/changeStatus', [RegisterController::class, 'changeStatus']); // Change Status

    Route::get('generate-pdf', [PDFController::class, 'generatePDF']); // Generate PDF Report
    Route::get('getRecentAgencies', [AdminDashboardController::class, 'getRecentAgencies']); // Get Recent Agencies

    Route::get('/get-recent-activites', [AdminDashboardController::class, 'getRecentActivities']);

    Route::resource('/countries', CountriesController::class);
    Route::resource('/states', StatesController::class);
    Route::resource('/cities', CitiesController::class);
    Route::resource('/subAgencies', SubAgencyController::class);

});

Route::controller(RegisterController::class)->group(function(){

    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('/destroy', 'destroy');
    Route::post('/verify-email', 'verify_email');
    Route::post('/resendCode', 'resendCode');

});

Route::controller(SocialLogins::class)->group(function() {

    Route::get('/login/{provider}', 'redirectToProvider');
    Route::get('/login/{provider}/callback', 'handleProviderCallback');

});

Route::get('/users-export', [PDFController::class, 'export']);
Route::post('/users-import', [PDFController::class, 'import']);
Route::post('/email-check-record', [ForgotPasswordController::class, 'checkEmail']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);

Route::get('/show-countries', [CountriesController::class, 'showCountries']);
Route::get('/states/show-states/{country_id}', [StatesController::class, 'showStates']);
Route::get('/cities/show-cities/{state_id}', [CitiesController::class, 'showCities']);

Route::controller(ForgotPasswordController::class)->group(function() {

    Route::post('/forgot-password', 'checkEmail');
    Route::post('/reset-password', 'forgotPassword');

});






