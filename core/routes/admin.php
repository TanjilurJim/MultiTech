<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\PurchaserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\RoleController;
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
        ->prefix('roles')
        ->name('roles.')
        ->group(function () {

            /* ---------- list ---------- */
            Route::get('/', 'index')
                ->name('index')
                ->middleware('role_or_permission:super-admin|roles.view');

            /* ---------- create ---------- */
            Route::get('create', 'create')
                ->name('create')
                ->middleware('role_or_permission:super-admin|roles.add');

            Route::post('/', 'store')
                ->name('store')
                ->middleware('role_or_permission:super-admin|roles.add');

            /* ---------- edit / update ---------- */
            Route::get('{role}/edit', 'edit')
                ->name('edit')
                ->middleware('role_or_permission:super-admin|roles.edit');

            Route::put('{role}', 'update')
                ->name('update')
                ->middleware('role_or_permission:super-admin|roles.edit');

            /* ---------- delete ---------- */
            Route::delete('{role}', 'destroy')
                ->name('destroy')
                ->middleware('role_or_permission:super-admin|roles.delete');
        });

    Route::resource('permissions', PermissionController::class)
        ->except('show')
        ->names('permissions')
        ->middleware('role:super-admin');




    // Banner

    Route::controller(\App\Http\Controllers\Admin\AdminUserController::class)
        ->prefix('admin-users')
        ->name('admin-users.')
        ->group(function () {

            // list
            Route::get('/', 'index')
                ->name('index')
                ->middleware('role_or_permission:super-admin|users.view');

            // create form
            Route::get('create', 'create')
                ->name('create')
                ->middleware('role_or_permission:super-admin|users.add');

            // store
            Route::post('/', 'store')
                ->name('store')
                ->middleware('role_or_permission:super-admin|users.add');

            // edit form
            Route::get('{admin}/edit', 'edit')
                ->name('edit')
                ->middleware('role_or_permission:super-admin|users.edit');

            // update
            Route::put('{admin}', 'update')
                ->name('update')
                ->middleware('role_or_permission:super-admin|users.edit');

            // delete
            Route::delete('{admin}', 'destroy')
                ->name('destroy')
                ->middleware('role_or_permission:super-admin|users.delete');

            Route::put('{admin}/deactivate', 'deactivate')
                ->name('deactivate');     // final = admin.admin-users.deactivate

            Route::put('{admin}/activate',   'activate')
                ->name('activate');       // final = admin.admin-users.ac
        });

   






    Route::controller('MediaController')->prefix('media')->name('media')->group(function () {
        Route::get('/', 'media');
        Route::get('files', 'mediaFiles')->name('.files');
        Route::post('upload', 'upload')->name('.upload');
        Route::post('delete/{id}', 'delete')->name('.delete');
    });










    Route::controller(CustomerController::class)
        ->prefix('customers')
        ->name('customers.')
        ->group(function () {

            // read
            Route::middleware('permission:customers.view')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('export', 'export')->name('export');
                Route::get('{customer}', 'show')->name('show');
            });

            // add
            Route::middleware('permission:customers.add')->post('/', 'store')->name('store');

            // edit
            Route::middleware('permission:customers.edit')->group(function () {
                Route::get('{customer}/edit', 'edit')->name('edit');
                Route::put('{customer}', 'update')->name('update');
            });

            // delete
            Route::middleware('permission:customers.delete')
                ->delete('{customer}', 'destroy')->name('destroy');
        });

    /* -------------------------------------------------
 |  Daily Follow-Up CRUD
 |-------------------------------------------------*/
    Route::prefix('follow-ups')
        ->name('followups.')
        ->controller(FollowUpLogController::class)
        ->group(function () {

            // 1 ▸ List + search + pagination
            Route::get('/', 'index')
                ->name('index')
                ->middleware('role_or_permission:super-admin|followup_logs.view');

            // 2 ▸ Create form
            Route::get('/create', 'create')
                ->name('create')
                ->middleware('role_or_permission:super-admin|followup_logs.add');

            // 3 ▸ Monthly report page
            Route::get('report', [\App\Http\Controllers\Admin\FollowUpReportController::class, 'monthly'])
                ->name('report')
                ->middleware('role_or_permission:super-admin|followup_reports.view');

            // 4 ▸ Monthly summaries list
            Route::get(
                '/monthly-summaries',
                [\App\Http\Controllers\Admin\MonthlyFollowUpSummaryController::class, 'index']
            )
                ->name('summaries')
                ->middleware('role_or_permission:super-admin|followup_summaries.view');

            Route::get(
                '/monthly-summaries/export',
                [\App\Http\Controllers\Admin\MonthlyFollowUpSummaryController::class, 'export']
            )
                ->name('summaries.export')
                ->middleware('role_or_permission:super-admin|followup_summaries.view');


            // 5 ▸ Update note in a summary
            Route::post(
                '/monthly-summaries/{summary}/note',
                [\App\Http\Controllers\Admin\MonthlyFollowUpSummaryController::class, 'updateNote']
            )
                ->name('summaries.note.update')
                ->middleware('role_or_permission:super-admin|followup_summaries.edit');

            // 6 ▸ Show single log (keep before store/edit/update/delete order unchanged)
            Route::get('/{log}', 'show')
                ->name('show')
                ->middleware('role_or_permission:super-admin|followup_logs.view');

            // 7 ▸ Store new log
            Route::post('/', 'store')
                ->name('store')
                ->middleware('role_or_permission:super-admin|followup_logs.add');

            // 8 ▸ Edit form
            Route::get('/{log}/edit', 'edit')
                ->name('edit')
                ->middleware('role_or_permission:super-admin|followup_logs.edit');

            // 9 ▸ Update existing log
            Route::put('/{log}', 'update')
                ->name('update')
                ->middleware('role_or_permission:super-admin|followup_logs.edit');

            // 10 ▸ Delete log
            Route::delete('/{log}', 'destroy')
                ->name('destroy')
                ->middleware('role_or_permission:super-admin|followup_logs.delete');
        });


    /* -------------------------------------------------
 |  30-Day Report & Excel export
 |-------------------------------------------------*/
    // Route::get(
    //     'admin/follow-ups/report',
    //     [FollowUpReportController::class, 'monthly']
    // )->name('followups.report');

    // Report





});
