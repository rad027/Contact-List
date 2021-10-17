<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

//developer controllers
use App\Http\Controllers\DeveloperTools\UsersController as DT_UC;
use App\Http\Controllers\DeveloperTools\ToolsController as DT_TC;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('test',function(){
    return "test";
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('user', [UserController::class, 'current']);

    Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);

    //functions
    Route::group([ 'prefix' => 'functions'], function(){
        //developer tools
        Route::group([ 'middleware' => [ 'role:Developer|Super Admin|Admin' ] ], function(){
            //fetch all roles and permissions
                //roles
                Route::post('roles/all', [ DT_TC::class, 'role_list' ])->middleware([ 'permission:can list user' ]);
                //roles
                Route::post('permissions/all', [ DT_TC::class, 'permission_list' ])->middleware([ 'permission:can list user' ]);
            //user management
            Route::group([ 'prefix' => 'users' ], function(){
                //list
                Route::post('list', [ DT_UC::class, 'init_list' ])->middleware([ 'permission:can list user' ]);
                //create
                Route::post('create', [ DT_UC::class, 'create' ])->middleware([ 'permission:can create user' ]);
                //update
                Route::post('update', [ DT_UC::class, 'update' ])->middleware([ 'permission:can update user' ]);
                //ban process
                Route::post('ban/process', [ DT_UC::class, 'ban_process' ])->middleware([ 'permission:can update user' ]);
            });
        });
    });
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);

    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
});
