<?php
namespace App\Http\Controllers;

use App\Models\PublicProject;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = collect(['home', 'about', 'services', 'public.projects.index', 'contact', 'legal'])->map(fn ($n) => ['loc' => route($n), 'lastmod' => null]);
        $projects = PublicProject::published()->get()->map(fn ($p) => ['loc' => route('public.projects.show', $p), 'lastmod' => $p->updated_at->toAtomString()]);
        return response()->view('sitemap', ['urls' => $urls->merge($projects)])->header('Content-Type', 'application/xml');
    }
}
