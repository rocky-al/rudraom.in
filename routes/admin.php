
<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\TmController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\QueryController;
use Illuminate\Support\Facades\Route;



Route::get('/test', function () {
    return view('email.registration_mail');
});



//Forget and reset password  admin and web 
Route::controller(AuthController::class)->group(function () {
    Route::get('forget-password', 'forgetPassword')->name('forget.password');
    Route::post('send-link', 'sendLink')->name('sendLink');
    Route::get('reset-password/{token}', 'resetPassword')->name('password.reset');
    Route::post('password-update', 'updatePassword')->name('password.update');
});


//Frontend route 
// Route::controller(FrontendController::class)->group(function () {
//     Route::get('', 'index')->name('frontend.index');
//     Route::get('signin', 'index');
//     Route::get('dashboard', 'dashboard')->name('frontend.dashboard');
//     Route::post('login', 'login')->name('frontend.login');
//     Route::get('logout', 'logout')->name('frontend.logout');
//     Route::post('register', 'register')->name('frontend.register');
//     Route::post('change_pswd/{id?}', 'change_pswd')->name('frontend.change_pswd');
//     Route::post('crop-image-upload', 'crop_image');
// });

// feed route

Route::controller(FeedController::class)->group(function () {
    Route::get('feed_list', 'feed_list')->name('feed.list');
    Route::get('form/{id?}', 'form')->name('feed.form');
    Route::get('/feed/{id}/edit', 'update')->name('feed.update');
    Route::post('/delete/{id}', 'delete')->name('feed.delete');
    Route::post('/removeimage/{id}', 'removeimage')->name('feed.removeimage');

    Route::post('manage', 'manage')->name('feed.manage');
});


// Order route

Route::controller(OrderController::class)->group(function () {
    Route::get('order_list', 'order_list')->name('order.list');
    Route::get('/order/{id}/edit', 'update')->name('order.update');
    Route::post('order_manage', 'order_manage')->name('order.manage');
    Route::get('order_view/{id}', 'order_view')->name('order.view');
    
});







// Auth Routing
Route::prefix('admin/')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('', 'login');
        Route::get('login', 'login')->name('admin.login');
        Route::post('do_login', 'do_login')->name('admin.do_login');
    });


    Route::group(['middleware' => 'auth'], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('logout', 'logout')->name('admin.logout');
        });
        //Admin Common functions and method
        Route::controller(HomeController::class)->group(function () {
            Route::post('theme_style', 'theme_style')->name('theme_style');
            Route::get('dashboard', 'index')->name('admin.index');
            Route::get('setting', 'setting')->name('admin.setting');
            Route::get('changePassword-form', 'changePassword')->name('admin.changePassword.form');
            Route::post('update-password', 'updatePassword')->name('admin.updatePassword');
            Route::get('setting-form', 'setting')->name('admin.setting.form');
            Route::Post('update-setting', 'updateSetting')->name('admin.update.setting');
            // PROFILE SECTION ROUTE 
            Route::get('profile', 'profile')->name('admin.profile');
            Route::Post('profile/update', 'updateProfile')->name('profile.update');
           
        });

        //Offer Management Routing
        Route::controller(NotificationController::class)->prefix('notification')->group(function () {
            Route::get('index', 'index')->name('notification.index');
            Route::get('form/{id?}', 'form')->name('notification.form');  //ajax request route add and edit form 
            Route::post('manage', 'manage')->name('notification.manage');
            Route::get('view{id?}', 'view')->name('notification.view');
            Route::get('status', 'status')->name('notification.status');
            Route::get('delete/{id}', 'delete')->name('notification.delete');
            Route::post('send_message', 'send_message')->name('notification.send');
        });



        /************************************ MASTER MODULE ROUTING START *********************************/

        // Role Controller Management Routing
        Route::controller(RoleController::class)->prefix('role')->group(function () {
            Route::get('index', 'index')->name('role.index');
            Route::get('add', 'add')->name('role.add');
            Route::Post('create', 'create')->name('role.create');
            Route::get('edit/{id}', 'edit')->name('role.edit');
            Route::Post('update', 'update')->name('role.update');
        });


        // Permission Management Routing
        Route::controller(PermissionController::class)->prefix('permission')->group(function () {
            Route::get('index', 'index')->name('permission.index');
            Route::get('form/{id?}', 'form')->name('permission.form');  //ajax request route add and edit form 
            Route::Post('manage', 'manage')->name('permission.manage');
        });

        // Content Management Routing
        Route::controller(ContentController::class)->prefix('content')->group(function () {
            Route::get('index', 'index')->name('content.index');
            Route::get('form/{id?}', 'form')->name('content.form');  //ajax request route add and edit form 
            Route::Post('manage', 'manage')->name('content.manage');
            Route::get('view{id?}', 'view')->name('content.view');
             Route::get('status', 'status')->name('content.status');
        });

        // Email Template Managenent Routing
        Route::controller(EmailTemplateController::class)->prefix('email-template')->group(function () {
            Route::get('', 'index')->name('emailTemplate.index');
            Route::get('form/{id?}', 'form')->name('emailTemplate.form');  //ajax request route add and edit form 
            Route::Post('manage', 'manage')->name('emailTemplate.manage');
            Route::get('view{id?}', 'view')->name('emailTemplate.view');
            Route::get('status', 'status')->name('emailTemplate.status');
        });

        Route::controller(UserController::class)->prefix('users')->group(function () {
            Route::get('', 'index')->name('users.list');
            Route::get('form/{id?}', 'form')->name('users.form');  //ajax request route add and edit form 
            Route::get('delete/{id?}', 'delete')->name('users.delete');
            Route::Post('manage', 'manage')->name('users.manage');
            Route::get('view{id?}', 'view')->name('users.view');
            Route::get('status', 'status')->name('users.status');
        });

        Route::controller(CategoryController::class)->prefix('category')->group(function () {
            Route::get('', 'index')->name('category.list');
            Route::get('form/{id?}', 'form')->name('category.form');  //ajax request route add and edit form 
            Route::get('delete/{id?}', 'delete')->name('category.delete');
            Route::Post('manage', 'manage')->name('category.manage');
            Route::get('view{id?}', 'view')->name('category.view');
            Route::get('status', 'status')->name('category.status');
        });


        Route::controller(CityController::class)->prefix('city')->group(function () {
            Route::get('', 'index')->name('city.list');
            Route::get('form/{id?}', 'form')->name('city.form');  //ajax request route add and edit form 
            Route::get('delete/{id?}', 'delete')->name('city.delete');
            Route::Post('manage', 'manage')->name('city.manage');
            Route::get('view{id?}', 'view')->name('city.view');
            Route::get('status', 'status')->name('city.status');
        });


        Route::controller(CountryController::class)->prefix('country')->group(function () {
            Route::get('', 'index')->name('country.list');
            Route::get('form/{id?}', 'form')->name('country.form');  //ajax request route add and edit form 
            Route::get('delete/{id?}', 'delete')->name('country.delete');
            Route::Post('manage', 'manage')->name('country.manage');
            Route::get('view{id?}', 'view')->name('country.view');
            Route::get('status', 'status')->name('country.status');
        });



         Route::controller(BusinessController::class)->prefix('business')->group(function () {
            Route::get('', 'index')->name('business.list');
            Route::get('form/{id?}', 'form')->name('business.form');  //ajax request route add and edit form 
            Route::get('delete/{id?}', 'delete')->name('business.delete');
            Route::Post('manage', 'manage')->name('business.manage');
            Route::get('view{id?}', 'view')->name('business.view');
            Route::get('status', 'status')->name('business.status');
        });

         Route::controller(QueryController::class)->prefix('query')->group(function () {
            Route::get('', 'index')->name('query.list');
            Route::get('form/{id?}', 'form')->name('query.form');  //ajax request route add and edit form 
            Route::get('delete/{id?}', 'delete')->name('query.delete');
            Route::Post('manage', 'manage')->name('query.manage');
            Route::get('view{id?}', 'view')->name('query.view');
            Route::get('status', 'status')->name('query.status');
        });


    });
});
