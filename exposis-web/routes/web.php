<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\PublicProjectController as AdminPublicProjectController;
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
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/projects/export', ExportController::class)->name('projects.export');
    Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [AdminProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');
    Route::put('/projects/{project}/evaluation', [AdminProjectController::class, 'evaluate'])->name('projects.evaluate');
    Route::resource('public-projects', AdminPublicProjectController::class)->except('show');
});

require __DIR__.'/auth.php';
