<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\AffiliationController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController; // ← ADD THIS

// ============================================
// PUBLIC ROUTES
// ============================================

// Homepage
Route::get('/', [PortfolioController::class, 'index'])->name('home');

// Contact Form
Route::post('/contact', [PortfolioController::class, 'contact'])->name('contact');

// ============================================
// BLOG ROUTES (Public)
// ============================================
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ============================================
// CERTIFICATE ROUTES (Public - for images)
// ============================================
Route::get('/certificates/{filename}', function ($filename) {
    // 🔥 Security: Only allow valid image/PDF extensions
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    
    if (!in_array(strtolower($extension), $allowedExtensions)) {
        abort(404);
    }
    
    $path = public_path('certificates/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    
    $mime = mime_content_type($path);
    return response()->file($path, ['Content-Type' => $mime]);
})->name('certificates.serve');
// ============================================
// AUTHENTICATION ROUTES
// ============================================

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================
// ADMIN ROUTES (Protected)
// ============================================

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // === DASHBOARD - Everyone with auth can view ===
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // === PROFILE - Everyone can manage their own profile ===
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // === EDUCATION - Editors and above ===
    Route::middleware(['role:editor,admin,super_admin'])->group(function () {
        Route::resource('education', EducationController::class)->except(['show']);
    });
    
    // === SKILLS - Editors and above ===
    Route::middleware(['role:editor,admin,super_admin'])->group(function () {
        Route::resource('skills', SkillController::class)->except(['show']);
    });
    
    // === EXPERIENCE - Editors and above ===
    Route::middleware(['role:editor,admin,super_admin'])->group(function () {
        Route::resource('experience', ExperienceController::class)->except(['show']);
    });
    
    // === PROJECTS - Editors and above ===
    Route::middleware(['role:editor,admin,super_admin'])->group(function () {
        Route::resource('projects', ProjectController::class)->except(['show']);
    });
    
    // === CERTIFICATES - Editors and above ===
    Route::middleware(['role:editor,admin,super_admin'])->group(function () {
        Route::resource('certificates', CertificateController::class)->except(['show']);
    });
    
    // === AFFILIATIONS - Editors and above ===
    Route::middleware(['role:editor,admin,super_admin'])->group(function () {
        Route::resource('affiliations', AffiliationController::class)->except(['show']);
    });
    
    // === BLOG - Authors can create drafts, Editors and above can publish ===
    Route::middleware(['role:author,editor,admin,super_admin'])->group(function () {
        Route::resource('blog', AdminBlogController::class)->except(['show']);
    });
    
    // === USERS - SuperAdmin only ===
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
    
    // === MESSAGES - Admins and above only ===
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::get('/messages', [MessageController::class, 'index'])->name('messages');
        Route::post('/messages/{id}/read', [MessageController::class, 'markRead'])->name('messages.read');
        Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');
    });
    
    // === NOTIFICATIONS - All authenticated users ===
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    
    // === AUDIT LOGS - Admins and above ===
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
    });
    
    // === THEME SETTING ===
    Route::get('/set-theme', function (Request $request) {
        if ($request->has('theme')) {
            session(['theme' => $request->theme]);
        }
        return response()->json(['success' => true]);
    })->name('admin.set-theme');
});

// ============================================
// ⚠️ IMPORTANT: This must be the LAST route
// Dynamic username route - catches any username after all fixed routes
// ============================================

Route::get('/{username}', [PortfolioController::class, 'index'])->name('portfolio.show');