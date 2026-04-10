<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ScanLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\MasjidController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\NotificationController;

// Language switcher
Route::post('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');

// Welcome page for guests
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome.index');
})->name('home');

// Public Help & Documentation (accessible without login)
Route::get('/help/faq', [HelpController::class, 'faq'])->name('help.faq');
Route::get('/help/guide', [HelpController::class, 'guide'])->name('help.guide');

// Public QR scan redirect (accessible without login for easy scanning)
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/i/{qrKey}', [QrCodeController::class, 'handleScan'])->name('qrcode.redirect');
    Route::get('/loans/return-scan/{qrKey}', [LoanController::class, 'handleScanReturn'])->name('loans.scan-return.handle');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Masjid self-registration
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
    
    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/widgets', [DashboardController::class, 'updateWidgets'])->name('dashboard.updateWidgets');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');

    // QR Code Scanner (all authenticated users)
    Route::get('/scan', [QrCodeController::class, 'scanPage'])->name('qrcode.scan');

    // Items - All roles can view
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    
    // Items - Admin & Operator only (MUST be before {item} route)
    Route::middleware(['role:admin,operator', 'ensure.masjid.context'])->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        
        // QR Code management
        Route::post('/items/{item}/qr/generate', [QrCodeController::class, 'generate'])->name('qrcode.generate');
        Route::get('/items/{item}/qr/preview', [QrCodeController::class, 'preview'])->name('qrcode.preview');
        Route::get('/items/{item}/qr/print', [QrCodeController::class, 'printSingle'])->name('qrcode.print');
        Route::get('/items/{item}/qr.svg', [QrCodeController::class, 'qrSvg'])->name('qrcode.svg');
        Route::get('/qr/bulk', [QrCodeController::class, 'bulkForm'])->name('qrcode.bulk');
        Route::post('/qr/bulk/print', [QrCodeController::class, 'bulkPrint'])->name('qrcode.bulk.print');
    });
    
    // Items - Show (after /create to avoid conflict)
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    
    // Items - Admin only delete
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])
        ->name('items.destroy')
        ->middleware(['role:admin', 'ensure.masjid.context']);

    // Categories - Admin & Operator can manage
    Route::middleware(['role:admin,operator', 'ensure.masjid.context'])->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
    });
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Locations - Admin & Operator can manage
    Route::middleware(['role:admin,operator', 'ensure.masjid.context'])->group(function () {
        Route::resource('locations', LocationController::class)->except(['show', 'index']);
    });
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

    // Reports - All roles can view
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // Loans - Admin & Operator can manage
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::middleware(['role:admin,operator', 'ensure.masjid.context'])->group(function () {
        Route::get('/loans/scan-return', [LoanController::class, 'scanReturnPage'])->name('loans.scan-return');
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnItem'])->name('loans.return.store');
        Route::post('/loans/{loan}/qr/generate', [LoanController::class, 'generateReturnQr'])->name('loans.qr.generate');
        Route::get('/loans/{loan}/qr.svg', [LoanController::class, 'returnQrSvg'])->name('loans.qr.svg');
        Route::get('/loans/{loan}/qr/print', [LoanController::class, 'printReturnQr'])->name('loans.qr.print');
        Route::post('/loans/quick-return/{qrKey}', [LoanController::class, 'quickReturn'])->name('loans.quick-return');
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
    });

    // Stock Movements - Admin & Operator can manage
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('/stock-movements/item/{item}', [StockMovementController::class, 'itemHistory'])->name('stock-movements.item');
    Route::middleware(['role:admin,operator', 'ensure.masjid.context'])->group(function () {
        Route::get('/stock-movements/create', [StockMovementController::class, 'create'])->name('stock-movements.create');
        Route::post('/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
    });

    // Export - All roles can export (needs masjid context)
    Route::middleware('ensure.masjid.context')->group(function () {
        Route::get('/export', [ExportController::class, 'index'])->name('export.index');
        Route::get('/export/excel', [ExportController::class, 'excel'])->name('export.excel');
        Route::get('/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
    });

    // Feedback - All roles can submit (needs masjid context)
    Route::middleware('ensure.masjid.context')->group(function () {
        Route::get('/feedbacks/create', [FeedbackController::class, 'create'])->name('feedbacks.create');
        Route::post('/feedbacks', [FeedbackController::class, 'store'])->name('feedbacks.store');
    });
    
    // Feedback management - Admin only
    Route::middleware(['role:admin', 'ensure.masjid.context'])->group(function () {
        Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedbacks.index');
        Route::put('/feedbacks/{feedback}', [FeedbackController::class, 'update'])->name('feedbacks.update');
        Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy'])->name('feedbacks.destroy');
    });

    // Users - Admin only
    Route::middleware(['role:admin', 'ensure.masjid.context'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // User transfer - Superadmin only
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/users/{user}/transfer', [UserController::class, 'transfer'])->name('users.transfer');
        Route::put('/users/{user}/transfer', [UserController::class, 'processTransfer'])->name('users.transfer.process');
    });

    // Backups - Superadmin only (full database dump)
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });

    // Maintenances - Admin & Operator can manage
    Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
    Route::middleware(['role:admin,operator', 'ensure.masjid.context'])->group(function () {
        Route::get('/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
        Route::post('/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
        Route::get('/maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
        Route::get('/maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
        Route::put('/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
        Route::patch('/maintenances/{maintenance}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenances.status');
        Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');
        Route::get('/items/{item}/maintenances', [MaintenanceController::class, 'itemHistory'])->name('maintenances.item');
        
        // Maintenance Photos
        Route::post('/maintenances/{maintenance}/photos', [MaintenanceController::class, 'uploadPhoto'])->name('maintenances.photos.upload');
        Route::put('/maintenance-photos/{photo}', [MaintenanceController::class, 'updatePhoto'])->name('maintenances.photos.update');
        Route::delete('/maintenance-photos/{photo}', [MaintenanceController::class, 'deletePhoto'])->name('maintenances.photos.delete');
    });

    // Import - Admin only
    Route::middleware(['role:admin', 'ensure.masjid.context'])->group(function () {
        Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
        Route::get('/imports/create', [ImportController::class, 'create'])->name('imports.create');
        Route::post('/imports/preview', [ImportController::class, 'preview'])->name('imports.preview');
        Route::post('/imports/process', [ImportController::class, 'process'])->name('imports.process');
        Route::get('/imports/template/{type}', [ImportController::class, 'template'])->name('imports.template');
        Route::get('/imports/{import}', [ImportController::class, 'show'])->name('imports.show');
    });

    // Activity Logs - Admin only (superadmin can view global)
    Route::middleware('role:admin')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

    // Scan Logs - Admin only (superadmin can view global)
    Route::middleware('role:admin')->group(function () {
        Route::get('/scan-logs', [ScanLogController::class, 'index'])->name('scan-logs.index');
        Route::get('/scan-logs/export', [ScanLogController::class, 'export'])->name('scan-logs.export');
        Route::get('/scan-logs/{scanLog}', [ScanLogController::class, 'show'])->name('scan-logs.show');
    });

    // Audit Scan - Admin & Operator
    Route::middleware(['role:admin,operator', 'ensure.masjid.context'])->group(function () {
        Route::get('/scan/audit', [QrCodeController::class, 'auditScanPage'])->name('qrcode.audit-scan');
        Route::post('/i/{qrKey}/audit', [QrCodeController::class, 'handleScanWithPurpose'])->name('qrcode.scan-with-purpose');
    });

    // Settings - Admin only
    Route::middleware(['role:admin', 'ensure.masjid.context'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Superadmin - Masjid management
    Route::middleware('superadmin')->group(function () {
        Route::get('/masjids', [MasjidController::class, 'index'])->name('masjids.index');
        Route::get('/masjids/create', [MasjidController::class, 'create'])->name('masjids.create');
        Route::post('/masjids', [MasjidController::class, 'store'])->name('masjids.store');
        Route::get('/masjids/{masjid}', [MasjidController::class, 'show'])->name('masjids.show');
        Route::get('/masjids/{masjid}/edit', [MasjidController::class, 'edit'])->name('masjids.edit');
        Route::put('/masjids/{masjid}', [MasjidController::class, 'update'])->name('masjids.update');
        Route::delete('/masjids/{masjid}', [MasjidController::class, 'destroy'])->name('masjids.destroy');
        Route::post('/masjids/switch', [MasjidController::class, 'switchContext'])->name('masjids.switch');
    });

    // About page - All authenticated users
    Route::get('/about', [SettingController::class, 'about'])->name('about');
});
