<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\PublicProjectController as AdminPublicProjectController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserRoleController as AdminUserRoleController;
use App\Http\Controllers\CommunityProjectController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\ProjectMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/a-propos', [PublicController::class, 'about'])->name('about');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/projets', [PublicController::class, 'projects'])->name('public.projects.index');
Route::get('/projets/{project:slug}', [PublicController::class, 'project'])->name('public.projects.show');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/mentions-legales', [PublicController::class, 'legal'])->name('legal');

Route::get('/soumettre', [ProjectController::class, 'publicCreate'])->name('public.submissions.create');
Route::post('/soumettre', [ProjectController::class, 'store'])->middleware('throttle:5,10')->name('public.submissions.store');
Route::get('/soumission-recue', fn () => session()->has('submission_reference')
    ? view('public.submission-received', ['reference' => session('submission_reference')])
    : redirect()->route('home'))->name('public.submissions.received');

Route::get('/dashboard', fn () => redirect()->route('projects.index'))->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/api/community/session', fn (\Illuminate\Http\Request $request) => response()->json([
        'user' => ['name' => $request->user()->name],
    ]))->name('community.session');
    Route::get('/communaute/projets', [CommunityProjectController::class, 'index'])->name('community.projects.index');
    Route::get('/communaute/projets/nouveau', [CommunityProjectController::class, 'create'])->name('community.projects.create');
    Route::post('/communaute/projets', [CommunityProjectController::class, 'store'])->middleware('throttle:10,10')->name('community.projects.store');
    Route::get('/communaute/projets/{project:slug}', [CommunityProjectController::class, 'show'])->name('community.projects.show');
    Route::get('/communaute/projets/{project:slug}/images/{image}', [CommunityProjectController::class, 'image'])->name('community.projects.images.show');
    Route::post('/communaute/projets/{project:slug}/rapport', [CommunityProjectController::class, 'storeReport'])
        ->middleware('throttle:10,10')
        ->name('community.projects.report.store');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->middleware('throttle:5,10')->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects/{project}/messages', [ProjectMessageController::class, 'store'])->middleware('throttle:20,1')->name('projects.messages.store');
    Route::post('/projects/{project}/files', [ProjectFileController::class, 'store'])->middleware('throttle:10,1')->name('projects.files.store');
    Route::get('/files/{file}', [ProjectFileController::class, 'download'])->name('files.download');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->middleware('permission:dashboard.view')->name('dashboard');
    Route::middleware('permission:projects.manage')->group(function () {
        Route::get('/projects/export', ExportController::class)->name('projects.export');
        Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [AdminProjectController::class, 'show'])->name('projects.show');
        Route::patch('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');
        Route::put('/projects/{project}/evaluation', [AdminProjectController::class, 'evaluate'])->name('projects.evaluate');
    });
    Route::middleware('permission:community_projects.manage')->group(function () {
        Route::get('/public-projects/{public_project}/images/{image}', [AdminPublicProjectController::class, 'image'])->name('public-projects.images.show');
        Route::get('/public-projects/{public_project}/reports/{report}', [AdminPublicProjectController::class, 'report'])->name('public-projects.reports.show');
        Route::resource('public-projects', AdminPublicProjectController::class)->except('show');
    });
    Route::get('/users', [AdminUserRoleController::class, 'index'])->middleware('permission:users.manage')->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserRoleController::class, 'update'])->middleware('permission:users.manage')->name('users.role.update');
    Route::resource('roles', AdminRoleController::class)->except('show')->middleware('permission:roles.manage');
});

require __DIR__.'/auth.php';
