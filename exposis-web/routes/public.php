// --- Add to routes/web.php, replacing the default '/' route ---
use App\Http\Controllers\PublicController;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/a-propos', [PublicController::class, 'about'])->name('about');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/projets', [PublicController::class, 'projects'])->name('public.projects.index');
Route::get('/projets/{project:slug}', [PublicController::class, 'project'])->name('public.projects.show');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/mentions-legales', [PublicController::class, 'legal'])->name('legal');
