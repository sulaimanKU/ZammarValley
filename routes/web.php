<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeeManagementController;
use App\Http\Controllers\OfficeExpensesController;
use App\Http\Controllers\OfficeIncomeController;
use App\Http\Controllers\PlotsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Models\Customer;
use App\Models\OfficeExpense;
use App\Models\PlotCategory;
use App\Models\PropertyFeature;
use App\Models\User;
use Illuminate\Support\Facades\Route;




Route::middleware([])->group(function () {

    // ── Transfer Deed (QR on printed deed) ───────────────
    Route::get('/transfers/{id}/deed', [TransferController::class, 'deed'])
        ->name('transfers.deed');


    //     // ── Payment Receipt (QR on printed receipt) ───────────
    Route::get('/payment/{id}/receipt', [AccountController::class, 'paymentReceipt'])
        ->name('payment.receipt');

    //     // ── Customer Card (QR on printed card) ────────────────
    Route::get('/customer/{bookingId}/card', [AccountController::class, 'customerCard'])
        ->name('customer.card');

    //     // ── Plot Show (QR on plot documents) ──────────────────
    Route::get('/plots/show/{id}', [PlotsController::class, 'plotShow'])
        ->name('plots.show');

    //     // ── Booking / Ledger Verification (QR on booking PDF) ─
Route::get('/verify-ledger/{id}', [BookingController::class, 'downloadPDF'])->name('downloadPDF');
    //     // ── Expense Detail PDF (QR on expense voucher) ────────
    Route::get('/expense/detail/pdf/{id}', [OfficeExpensesController::class, 'expenseDetailPdf'])
        ->name('expense.detail.pdf');

    //     // ── Office Expenses PDF Search/Export ─────────────────
   Route::get('/office/expenses/search', [OfficeExpensesController::class, 'officeExpensesSsearch'])
    ->name('office_expenses.search');
    Route::get('/verify/{booking_id}', [BookingController::class, 'publicVerify'])
        ->name('booking.verify');
        Route::get('transfers/{id}/qr-verify',    [TransferController::class, 'qrVerify'])->name('transfer.qr.verify');

    Route::get('/verify-possession/{booking}', [TransferController::class, 'verifyPossession'])
    ->name('verify_possession')
    ->middleware('signed');


});

// --- AUTHENTICATED ROUTES (Shared by everyone) ---
Route::middleware(['auth'])->group(function () {

    // ═══════════════════════════════════════════════════════
    //  DASHBOARD
    // ═══════════════════════════════════════════════════════
    Route::get('/', [AdminController::class, 'index'])->name('index.dashboard');

    // ═══════════════════════════════════════════════════════
    //  SETTINGS
    // ═══════════════════════════════════════════════════════
    Route::get('settings', [SettingController::class, 'index'])
        ->middleware('permission:settings_view')
        ->name('setting.view');

    Route::post('/permissions/store', [SettingController::class, 'storePermission'])
        ->middleware('permission:role_manage')
        ->name('permissions.store');

    Route::get('role/create', [SettingController::class, 'roleCreate'])
        ->middleware('permission:role_manage')
        ->name('role.create');

    Route::post('/role/store', [SettingController::class, 'storeRole'])
        ->middleware('permission:role_manage')
        ->name('role.store');

    Route::get('RolePermission/edit/{id}', [SettingController::class, 'RolePermissionEdit'])
        ->middleware('permission:role_manage')
        ->name('RolePermission.edit');

    Route::put('/role-permission/{id}', [SettingController::class, 'RolePermissionUpdate'])
        ->middleware('permission:role_manage')
        ->name('RolePermission.update');

    Route::delete('destroy/{id}', [UserController::class, 'roleDestroy'])
        ->middleware('permission:role_manage')
        ->name('role.destroy');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::put('/profile', [SettingController::class, 'updateProfile'])
            ->middleware('permission:profile_edit')
            ->name('profile.update');

        Route::get('/logo', [SettingController::class, 'logoView'])
            ->middleware('permission:society_config_manage')
            ->name('logo.view');
        Route::post('/society/logo', [SettingController::class, 'saveLogo'])
            ->middleware('permission:society_config_manage')
            ->name('society.logo');

        Route::get('/identity', [SettingController::class, 'showIdentity'])->name('identity.show');
        Route::post('/society/identity', [SettingController::class, 'saveIdentity'])
            ->middleware('permission:society_config_manage')
            ->name('society.identity');

        Route::get('/finance', [SettingController::class, 'showFinance'])->name('finance.show');
        Route::post('/society/finance', [SettingController::class, 'saveFinance'])
            ->middleware('permission:society_config_manage')
            ->name('society.finance');

        Route::get('/docs', [SettingController::class, 'showDocs'])->name('docs.show');
        Route::post('/society/docs', [SettingController::class, 'saveDocs'])
            ->middleware('permission:society_config_manage')
            ->name('society.docs');
            Route::get('/settings/email',  [SettingController::class, 'showEmail'])->name('email.show');
            Route::post('/settings/email', [SettingController::class, 'saveEmail'])->name('config.email');
        Route::post('/settings/email/test', [SettingController::class, 'testEmail'])->name('email.test');
    });

    // ═══════════════════════════════════════════════════════
    //  USER MANAGEMENT
    // ═══════════════════════════════════════════════════════
    Route::get('User/Management', [UserController::class, 'index'])
        ->middleware('permission:user_view')
        ->name('index.user');

    Route::get('search/user', [UserController::class, 'searchUser'])
        ->middleware('permission:user_view')
        ->name('search.user');

    Route::get('add/user', [UserController::class, 'addUser'])
        ->middleware('permission:user_create')
        ->name('add.user');

    Route::post('/users/store', [UserController::class, 'storeUser'])
        ->middleware('permission:user_create')
        ->name('users.store');

    Route::get('user/{id}/edit', [UserController::class, 'userEdit'])
        ->middleware('permission:user_edit')
        ->name('users.edit');

    Route::put('user/update/{id}', [UserController::class, 'userUpdate'])
        ->middleware('permission:user_edit')
        ->name('users.update');

    Route::delete('users/destroy/{id}', [UserController::class, 'userDestroy'])
        ->middleware('permission:user_delete')
        ->name('users.destroy');

    // ═══════════════════════════════════════════════════════
    //  ACCOUNTS & LEDGER (Recovery)
    // ═══════════════════════════════════════════════════════
    Route::get('account', [AccountController::class, 'index'])
        ->middleware('permission:recovery_dashboard_view')
        ->name('index.account');

    Route::post('account', [AccountController::class, 'clientSearch'])
        ->middleware('permission:recovery_dashboard_view')
        ->name('client.search');

    Route::get('ledger/{id}/view', [AccountController::class, 'ledgerView'])
        ->middleware('permission:ledger_view')
        ->name('ledger.view');

    Route::post('plot/payment/store', [AccountController::class, 'plotPaymentStore'])
        ->middleware('permission:payment_add')
        ->name('plot.payment.store');

    Route::delete('plot/payment/{id}/delete', [AccountController::class, 'plotPaymentDestroy'])
        ->middleware('permission:payment_add')
        ->name('plot.payment.destroy');

    Route::put('plot/payment/{id}/update', [AccountController::class, 'plotPaymentUpdate'])
        ->middleware('permission:payment_add')
        ->name('plot.payment.update');

    Route::get('/finance/report', [AccountController::class, 'financeReport'])
        ->middleware('permission:finance_reports_view')
        ->name('finance.report');

    // ── Fee Management — #44 fee_management_view / #45 fee_management_pay ──
    Route::get('fee-management', [FeeManagementController::class, 'index'])
        ->middleware('permission:fee_management_view')
        ->name('fee.management');

    Route::post('fee-management/pay', [FeeManagementController::class, 'store'])
        ->middleware('permission:fee_management_pay')
        ->name('fee.payment.store');

    Route::delete('fee-management/payment/{id}/delete', [FeeManagementController::class, 'paymentDestroy'])
        ->middleware('permission:fee_management_pay')
        ->name('fee.payment.destroy');

    Route::get('fee-management/history/{id}', [FeeManagementController::class, 'history'])
        ->middleware('permission:fee_management_view')
        ->name('fee.history');

    Route::get('fee-management/receipt/{id}', [FeeManagementController::class, 'receipt'])
        ->middleware('permission:fee_management_view')
        ->name('fee.receipt');

    Route::get('fee-management/booking-receipt/{bookingId}', [FeeManagementController::class, 'combinedReceipt'])
        ->middleware('permission:fee_management_view')
        ->name('fee.booking.receipt');

    Route::post('fee-management/bill/{id}/update-amount', [FeeManagementController::class, 'updateBillAmount'])
        ->middleware('permission:fee_management_pay')
        ->name('fee.bill.update-amount');

    // ═══════════════════════════════════════════════════════
    //  OFFICE EXPENSES (Finance)
    // ═══════════════════════════════════════════════════════
    Route::get('office/expenses', [OfficeExpensesController::class, 'index'])
        ->middleware('permission:expense_view')
        ->name('office_expenses.view');

    Route::get('office-expenses/create', [OfficeExpensesController::class, 'create'])
        ->middleware('permission:expense_add')
        ->name('expenses.create');

    Route::post('expenses/store', [OfficeExpensesController::class, 'store'])
        ->middleware('permission:expense_add')
        ->name('expenses.store');

    Route::get('expense/{id}/detail', [OfficeExpensesController::class, 'expenseDetail'])
        ->middleware('permission:expense_view')
        ->name('expense.detail.view');

    Route::get('expense/{id}/edit', [OfficeExpensesController::class, 'expenseEditView'])
        ->middleware('permission:expense_edit')
        ->name('expense.edit.view');

    Route::put('office_expenses/{id}/update', [OfficeExpensesController::class, 'update'])
        ->middleware('permission:expense_edit')
        ->name('office_expenses.update');

    Route::delete('expenses/destroy/{id}', [OfficeExpensesController::class, 'expenseDestroy'])
        ->middleware('permission:expense_delete')
        ->name('expenses.destroy');

    Route::get('/reports/daily-cash', [OfficeExpensesController::class, 'dailyCash'])
        ->middleware('permission:finance_reports_view')
        ->name('reports.daily_cash');

    Route::get('/reports/daily-cash/pdf', [OfficeExpensesController::class, 'dailyCashPdf'])
        ->middleware('permission:finance_reports_view')
        ->name('reports.daily_cash_pdf');

    Route::get('/reports/monthly-summary', [OfficeExpensesController::class, 'monthlySummary'])
        ->middleware('permission:finance_reports_view')
        ->name('reports.monthly_summary');

    Route::get('/reports/monthly-pdf', [OfficeExpensesController::class, 'monthlyPdf'])
        ->middleware('permission:finance_reports_view')
        ->name('reports.monthly_pdf');

    Route::get('/reports/yearly-pdf', [OfficeExpensesController::class, 'yearlyPdf'])
        ->middleware('permission:finance_reports_view')
        ->name('reports.yearly_pdf');

    // ═══════════════════════════════════════════════════════
    //  PLOTS & INVENTORY
    // ═══════════════════════════════════════════════════════
    Route::get('plots', [PlotsController::class, 'index'])
        ->middleware('permission:inventory_view')
        ->name('index.plots');

    Route::get('plot/add', [PlotsController::class, 'addPlot'])
        ->middleware('permission:plot_create')
        ->name('plot.add');

    Route::post('plots/store', [PlotsController::class, 'plotStore'])
        ->middleware('permission:plot_create')
        ->name('plots.store');

    Route::get('plots/edit/{id}', [PlotsController::class, 'plotsEdit'])
        ->middleware('permission:plot_edit')
        ->name('plots.edit');

    Route::put('plots/{id}/update', [PlotsController::class, 'plotUpdate'])
        ->middleware('permission:plot_edit')
        ->name('plots.update');

    Route::delete('plots/delete/{id}', [PlotsController::class, 'plotDestroy'])
        ->middleware('permission:plot_delete')
        ->name('plots.destroy');

    Route::get('plot/pricing', [PlotsController::class, 'plotPricingView'])
        ->middleware('permission:plot_pricing_manage')
        ->name('plot.pricing.view');

    Route::put('/pricing-plan/{id}/update', [PlotsController::class, 'updatePricePlan'])
        ->middleware('permission:plot_pricing_manage')
        ->name('pricing.plan.update');

    Route::delete('/plot-pricing/{id}', [PlotsController::class, 'PlotPricingDestroy'])
        ->middleware('permission:plot_pricing_manage')
        ->name('plot-pricing.destroy');

    Route::post('pricing-plans/store', [PlotsController::class, 'storePricePlan'])
        ->middleware('permission:plot_pricing_manage')
        ->name('pricing-plans.store');

    // ── Plot Categories ──────────────────────────────────────
    Route::get('categories/view', [PlotsController::class, 'categoriesView'])
        ->middleware('permission:plot_category_manage')
        ->name('categories.view');

    Route::get('create/category', [PlotsController::class, 'createCategory'])
        ->middleware('permission:plot_category_manage')
        ->name('categories.create');

    Route::post('categories/store', [PlotsController::class, 'categoryStore'])
        ->middleware('permission:plot_category_manage')
        ->name('categories.store');

    Route::get('/categories/{id}', [PlotsController::class, 'show'])
        ->middleware('permission:plot_category_manage')
        ->name('categories.show');

    Route::get('category/show/{id}', [PlotsController::class, 'categoryEdit'])
        ->middleware('permission:plot_category_manage')
        ->name('categories.edit');

    Route::put('/categories/update/{id}', [PlotsController::class, 'categoryUpdate'])
        ->middleware('permission:plot_category_manage')
        ->name('categories.update');

    Route::delete('/plot-categories/{id}', [PlotsController::class, 'PlotCategoryDestroy'])
        ->middleware('permission:plot_category_manage')
        ->name('plot-categories.destroy');

    // ── Property Features ────────────────────────────────────
    Route::get('property/feature/view', [PlotsController::class, 'propertyFeatureView'])
        ->middleware('permission:plot_category_manage')
        ->name('property.feature.view');

    Route::post('property/feature/store', [PlotsController::class, 'propertyFeatureStore'])
        ->middleware('permission:plot_category_manage')
        ->name('property.feature.store');

    Route::put('property/{id}/feature/update', [PlotsController::class, 'propertyFeatureUpdate'])
        ->middleware('permission:plot_category_manage')
        ->name('property.feature.update');

    Route::get('property/{id}/feature/edit', [PlotsController::class, 'propertyFeatureEdit'])
        ->middleware('permission:plot_category_manage')
        ->name('property.feature.edit');

    Route::delete('property/{id}/feature/destroy', [PlotsController::class, 'propertyFeatureDestroy'])
        ->middleware('permission:plot_category_manage')
        ->name('property.feature.destroy');

    // ── Blocks — #43 block_manage ────────────────────────────
    Route::get('blocks', [PlotsController::class, 'blockView'])
        ->middleware('permission:block_manage')
        ->name('blocks.index');

    Route::post('blocks/store', [PlotsController::class, 'blockStore'])
        ->middleware('permission:block_manage')
        ->name('blocks.store');

    Route::get('blocks/{id}/edit', [PlotsController::class, 'blockEditView'])
        ->middleware('permission:block_manage')
        ->name('blocks.edit');

    Route::put('blocks/{id}', [PlotsController::class, 'blockUpdate'])
        ->middleware('permission:block_manage')
        ->name('blocks.update');

    Route::delete('blocks/{id}', [PlotsController::class, 'blockDestroy'])
        ->middleware('permission:block_manage')
        ->name('blocks.destroy');

    // ── Sectors ──────────────────────────────────────────────
    Route::get('sector/view', [PlotsController::class, 'sectorView'])
        ->middleware('permission:location_manage')->name('sector.view');
    Route::post('sector/store', [PlotsController::class, 'sectorStore'])
        ->middleware('permission:location_manage')->name('sector.store');
    Route::get('sector/edit/view/{id}', [PlotsController::class, 'sectorEditView'])
        ->middleware('permission:location_manage')->name('sector.edit.view');
    Route::put('sector/update/{id}', [PlotsController::class, 'sectorUpdate'])
        ->middleware('permission:location_manage')->name('sector.update');
    Route::delete('sector/destroy/{id}', [PlotsController::class, 'sectorDestroy'])
        ->middleware('permission:location_manage')->name('sector.destroy');

    // ── Society ──────────────────────────────────────────────
    Route::get('society/view', [PlotsController::class, 'societyView'])
        ->middleware('permission:location_manage')->name('society.view');
    Route::post('society/store', [PlotsController::class, 'societyStore'])
        ->middleware('permission:location_manage')->name('society.store');
    Route::get('society/edit/view/{id}', [PlotsController::class, 'societyEditView'])
        ->middleware('permission:location_manage')->name('society.edit.view');
    Route::put('society/update/{id}', [PlotsController::class, 'societyUpdate'])
        ->middleware('permission:location_manage')->name('society.update');
    Route::delete('society/destroy/{id}', [PlotsController::class, 'societyDestroy'])
        ->middleware('permission:location_manage')->name('society.destroy');

    // ── Cities ───────────────────────────────────────────────
    Route::get('city/view', [SettingController::class, 'cityView'])
        ->middleware('permission:location_manage')->name('city.view');
    Route::post('city/store', [SettingController::class, 'cityStore'])
        ->middleware('permission:location_manage')->name('city.store');
    Route::get('city/edit/{id}', [SettingController::class, 'cityEditView'])
        ->middleware('permission:location_manage')->name('city.edit');
    Route::put('city/update/{id}', [SettingController::class, 'cityUpdate'])
        ->middleware('permission:location_manage')->name('city.update');
    Route::delete('city/destroy/{id}', [SettingController::class, 'cityDestroy'])
        ->middleware('permission:location_manage')->name('city.destroy');

    // ═══════════════════════════════════════════════════════
    //  BOOKINGS
    // ═══════════════════════════════════════════════════════
    Route::get('booking', [BookingController::class, 'index'])
        ->middleware('permission:booking_view_all')
        ->name('index.booking');

    Route::get('booking/{id}/detail', [BookingController::class, 'bookingDetailView'])
        ->middleware('permission:booking_view_all')
        ->name('booking.detail.view');

    Route::get('booking/search', [BookingController::class, 'searchPlots'])
        ->middleware('permission:booking_create')
        ->name('booking.search');

    Route::get('booking/create/{plotId}', [BookingController::class, 'NewBooking'])
        ->middleware('permission:booking_create')
        ->name('booking.create');

    Route::post('bookings/store', [BookingController::class, 'bookingStore'])
        ->middleware('permission:booking_create')
        ->name('bookings.store');

    Route::get('booking/reports', [BookingController::class, 'bookingReport'])
        ->middleware('permission:booking_reports')
        ->name('booking.reports');

    // #47 booking_docs_view
    Route::get('/bookings/{id}/application-form', [BookingController::class, 'bookingApplicationForm'])
        ->middleware('permission:booking_docs_view')
        ->name('booking.application.form');

    Route::get('/bookings/{id}/agreement', [BookingController::class, 'bookingAgreement'])
        ->middleware('permission:booking_docs_view')
        ->name('booking.agreement');

    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])
        ->middleware('permission:booking_edit')
        ->name('booking.edit');

    Route::put('/bookings/{id}/update', [BookingController::class, 'update'])
        ->middleware('permission:booking_edit')
        ->name('booking.update');

    Route::delete('booking/destroy/{id}', [BookingController::class, 'bookingDestroy'])
        ->middleware('permission:booking_cancel')
        ->name('booking.destroy');

    Route::post('booking/{id}/cancel', [BookingController::class, 'cancelBooking'])
        ->middleware('permission:booking_cancel')
        ->name('booking.cancel');

    Route::get('booking/{id}/cancellation-notice', [BookingController::class, 'cancellationNoticePdf'])
        ->middleware('permission:booking_view_all')
        ->name('booking.cancellation.notice');

Route::post('bookings/{id}/hold',   [BookingController::class, 'hold'])  ->name('booking.hold');
Route::post('bookings/{id}/unhold', [BookingController::class, 'unhold'])->name('booking.unhold');

    // ── Change Installment Plan ──────────────────────────────────────
    Route::post('booking/{id}/change-plan', [BookingController::class, 'changePlan'])
        ->middleware('permission:booking_plan_change')
        ->name('booking.change.plan');

    // ── Lump Sum Settlement ──────────────────────────────────
    Route::post('booking/{id}/lump-sum', [AccountController::class, 'lumpSumSettle'])
        ->middleware('permission:payment_add')
        ->name('booking.lump.sum');

    // ── Weekly Offer Letter ──────────────────────────────────
    Route::get('bookings/{id}/weekly-offer', [BookingController::class, 'weeklyOfferLetter'])
        ->middleware('permission:booking_view_all')
        ->name('booking.weekly.offer');

    Route::get('bookings/{id}/weekly-offer/pdf', [BookingController::class, 'weeklyOfferPdf'])
        ->middleware('permission:booking_view_all')
        ->name('booking.weekly.offer.pdf');

    // ── Customer Statement PDF & Email ───────────────────────
    Route::get('customer/{id}/statement', [BookingController::class, 'customerStatement'])
        ->middleware('permission:booking_view_all')
        ->name('customer.statement');
    Route::post('customer/{id}/send-statement', [BookingController::class, 'sendCustomerStatement'])
        ->middleware('permission:booking_view_all')
        ->name('customer.statement.email');
    // ═══════════════════════════════════════════════════════
    //  CUSTOMERS / CLIENTS
    // ═══════════════════════════════════════════════════════
    Route::get('/customers', [CustomerController::class, 'indexCustomer'])
        ->middleware('permission:client_view')
        ->name('index.customer');

    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware('permission:client_create')
        ->name('customers.store');

    Route::get('/customers/{id}', [CustomerController::class, 'show'])
        ->middleware('permission:client_view')
        ->name('customers.show');

    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])
        ->middleware('permission:client_edit')
        ->name('customers.edit');

    Route::put('/customers/{id}', [CustomerController::class, 'update'])
        ->middleware('permission:client_edit')
        ->name('customers.update');

    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])
        ->middleware('permission:client_delete')
        ->name('customers.destroy');

    // ═══════════════════════════════════════════════════════
    //  TRANSFERS
    //  IMPORTANT: deed & qr-verify OUTSIDE the prefix group
    //  so their URL is /transfers/{id}/deed (not /transfers/transfers/{id}/deed)
    // ═══════════════════════════════════════════════════════

    // ── Outside prefix — avoids double "transfers/transfers" ──
    Route::get('/transfers/{id}/deed',     [TransferController::class, 'deed'])     ->name('transfers.deed');


    Route::prefix('transfers')->group(function () {

        Route::get('/', [TransferController::class, 'index'])
            ->middleware('permission:transfer_history_view')
            ->name('index.transfer');

        Route::get('search', [TransferController::class, 'search'])
            ->middleware('permission:transfer_history_view')
            ->name('transfers.search');

        Route::get('create/{id}', [TransferController::class, 'create'])
            ->middleware('permission:transfer_create')
            ->name('transfers.create');

        Route::post('/', [TransferController::class, 'store'])
            ->middleware('permission:transfer_create')
            ->name('transfers.store');

        Route::get('{id}/application-form', [TransferController::class, 'applicationForm'])
            ->middleware('permission:transfer_history_view')
            ->name('transfers.application.form');

        Route::get('{id}/swap-deed', [TransferController::class, 'swapDeed'])
            ->middleware('permission:transfer_history_view')
            ->name('transfer.swap.deed');

        Route::get('{id}/qr-code', [TransferController::class, 'qrCode'])
            ->middleware('permission:transfer_history_view')
            ->name('transfer.qr.code');

        // #46 transfer_approve
        Route::post('{id}/approve', [TransferController::class, 'approve'])
            ->middleware('permission:transfer_approve')
            ->name('transfers.approve');

        Route::post('{id}/reject', [TransferController::class, 'reject'])
            ->middleware('permission:transfer_edit')
            ->name('transfers.reject');

        Route::get('{id}/pay-fee', [TransferController::class, 'payFee'])
            ->middleware('permission:transfer_edit')
            ->name('transfers.pay-fee');

        Route::post('{id}/pay-fee', [TransferController::class, 'processPayment'])
            ->middleware('permission:transfer_edit')
            ->name('transfers.process-payment');

        Route::get('{id}/fee-receipt', [TransferController::class, 'feeReceipt'])
            ->middleware('permission:transfer_history_view')
            ->name('transfers.fee-receipt');

        Route::get('{id}/edit', [TransferController::class, 'edit'])
            ->middleware('permission:transfer_edit')
            ->name('transfers.edit');

        Route::put('{id}', [TransferController::class, 'update'])
            ->middleware('permission:transfer_edit')
            ->name('transfers.update');

        Route::delete('{id}', [TransferController::class, 'destroy'])
            ->middleware('permission:transfer_delete')
            ->name('transfers.destroy');

        // #48 possession_letter_view
        Route::get('bookings/{id}/possession-letter', [TransferController::class, 'possessionLetter'])
            ->middleware('permission:possession_letter_view')
            ->name('booking.possession.letter');

        Route::post('/customers/quick-register', [TransferController::class, 'quickRegister'])
            ->middleware('permission:transfer_create')
            ->name('customers.quick-register');
    });

});

require __DIR__ . '/auth.php';
