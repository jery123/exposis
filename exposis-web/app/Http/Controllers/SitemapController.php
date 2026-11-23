<?php

namespace App\Http\Controllers;

use App\Models\PublicProject;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $siteUrl = rtrim(config('seo.site_url'), '/');
        $urls = collect(['home', 'about', 'services', 'public.projects.index', 'contact', 'legal'])
            ->map(fn (string $name): array => [
                'loc' => $siteUrl.route($name, absolute: false),
                'lastmod' => null,
            ]);
        $projects = PublicProject::published()
            ->get()
            ->map(fn (PublicProject $project): array => [
                'loc' => $siteUrl.route('public.projects.show', $project, false),
                'lastmod' => $project->updated_at->toAtomString(),
            ]);

        return response()
            ->view('sitemap', ['urls' => $urls->merge($projects)])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
