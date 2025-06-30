<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\PurchaserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\FollowUpReportController;
use App\Http\Controllers\Admin\FollowUpLogController;
use App\Http\Controllers\Admin\PermissionController;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Purchaser;

Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login');
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });

        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change');
        });
    });
});

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('sales-chart', 'salesChart')->name('chart.sales');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification


        //Report Bugs

    });

    Route::controller(\App\Http\Controllers\Admin\RoleController::class)
        ->prefix('roles')->name('roles.')->group(function () {
            Route::get('/', 'index')
                ->name('index')
                ->middleware([
                    'role:super-admin',        // ← first check for the role
                    'permission:manage roles', // ← then the permission
                ]);
            Route::get('create',   'create')->name('create')->middleware('permission:manage roles');
            Route::post('/',       'store')->name('store')->middleware('permission:manage roles');
            Route::get('{role}/edit',  'edit')->name('edit')->middleware('permission:manage roles');
            Route::put('{role}',       'update')->name('update')->middleware('permission:manage roles');
            Route::delete('{role}',    'destroy')->name('destroy')->middleware('permission:manage roles');
        });

    Route::resource('permissions', PermissionController::class)
        ->except(['show'])
        ->names('permissions')
        ->middleware('role:super-admin');




    // Banner

    Route::controller(\App\Http\Controllers\Admin\AdminUserController::class)
        ->prefix('admin-users')                     //  /admin/admin-users/…
        ->name('admin-users.')
        ->group(function () {
            Route::get('/',            'index')->name('index');
            Route::get('create',       'create')->name('create');
            Route::post('/',           'store')->name('store');
            Route::get('{admin}/edit', 'edit')->name('edit');
            Route::put('{admin}',      'update')->name('update');
            Route::delete('{admin}',   'destroy')->name('destroy');
        });


    // Menu builder


    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('profile-completed', 'profileCompletedUsers')->name('profile.completed');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('list', 'list')->name('list');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });



    //Brand


    //Product Attributes


    // Product Type


    //Manage Products


    Route::controller('MediaController')->prefix('media')->name('media')->group(function () {
        Route::get('/', 'media');
        Route::get('files', 'mediaFiles')->name('.files');
        Route::post('upload', 'upload')->name('.upload');
        Route::post('delete/{id}', 'delete')->name('.delete');
    });

    //Coupons



    //Order




    Route::controller(CustomerController::class)
        ->prefix('customers')
        ->name('customers.')
        ->group(function () {

            // List + search + pagination
            Route::get('/', 'index')->name('index');

            // Create
            Route::post('/', 'store')->name('store');

            Route::get('/{customer}/edit',  'edit')->name('edit');

            // Update
            Route::put('/{customer}', 'update')->name('update');

            // Delete
            Route::delete('/{customer}', 'destroy')->name('destroy');

            // Export CSV / Excel
            Route::get('/export', 'export')->name('export');

            Route::get('/{customer}', 'show')->name('show');
        });

    /* -------------------------------------------------
 |  Daily Follow-Up CRUD
 |-------------------------------------------------*/
    Route::prefix('follow-ups')
        ->name('followups.')
        ->controller(FollowUpLogController::class)
        ->group(function () {

            // List + search + pagination
            Route::get('/',        'index')->name('index');

            // Create form
            Route::get('/create',  'create')->name('create');
            Route::get('report', [\App\Http\Controllers\Admin\FollowUpReportController::class, 'monthly'])
                ->name('report');

            Route::get(
                '/monthly-summaries',
                [\App\Http\Controllers\Admin\MonthlyFollowUpSummaryController::class, 'index']
            )
                ->name('summaries');

            Route::post('/monthly-summaries/{summary}/note', [\App\Http\Controllers\Admin\MonthlyFollowUpSummaryController::class, 'updateNote'])
                ->name('summaries.note.update');

            Route::get('/{log}',       'show')->name('show');

            // Store
            Route::post('/',       'store')->name('store');

            Route::get('/{log}/edit', 'edit')->name('edit');
            Route::put('/{log}',      'update')->name('update');

            // (Optional) Edit / Update / Delete
            // Route::get('/{log}/edit', 'edit')->name('edit');
            // Route::put('/{log}',      'update')->name('update');
            Route::delete('/{log}',   'destroy')->name('destroy');
        });

    /* -------------------------------------------------
 |  30-Day Report & Excel export
 |-------------------------------------------------*/
    // Route::get(
    //     'admin/follow-ups/report',
    //     [FollowUpReportController::class, 'monthly']
    // )->name('followups.report');

    // Report


    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::get('/admin/order/status-counts', [OrderController::class, 'statusCounts'])->name('order.status_counts');




    //Notification Setting


    // Plugin



    //System Information



    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');




    // Generate SKU

    Route::post(
        'products/generate-sku',
        [\App\Http\Controllers\Admin\ProductController::class, 'generateSku']
    )->name('products.generate-sku');

    // Live Barcode
    Route::prefix('products')->group(function () {
        Route::post('barcode-preview', function (\Illuminate\Http\Request $r) {
            $sku = $r->input('sku');
            if (!$sku) return response()->json(['base64' => null]);

            $png = (new \Milon\Barcode\DNS1D)->getBarcodePNG($sku, 'C128');
            return response()->json(['base64' => $png]);
        })->name('products.barcode.preview');
    });

    Route::post('stock/receive', [StockController::class, 'receive'])
        ->name('stock.receive');

    Route::post(
        'purchasers/store',        //  POST  admin/purchasers/store
        [\App\Http\Controllers\Admin\PurchaserController::class, 'store']
    )->name('purchasers.store');

    // routes/admin.php
    Route::get('purchasers/search', [PurchaserController::class, 'select2'])
        ->name('purchasers.search');
});

Route::get('sales/download', [OrderController::class, 'download'])->name('sales.download');

Route::get('reports/business/csv', [ReportController::class, 'businessReportCsv'])->name('reports.business.csv');
Route::get('reports/sales/csv', [ReportController::class, 'salesReportCsv'])->name('reports.sales.csv');


// Route::get('/orders/download-excel', [OrderController::class, 'downloadExcel'])->name('admin.orders.download.excel');

// Route::get('reports/business/download', [ReportController::class, 'businessReportDownload'])->name('admin.reports.business.download');
