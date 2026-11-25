@extends('layouts.public')
@section('title', 'Rapport — ' . $project->title . ' | EXPOSIS')
@section('description', 'Consultez le rapport du projet ' . $project->title . '.')
@section('robots', 'noindex, nofollow')
@section('content')
    <article class="report-reader section-wrap">
        <a class="back-link" href="{{ route('public.projects.show', $project) }}"><span aria-hidden="true">←</span> Retour au projet</a>
        <header class="report-reader__header">
            <span class="section-kicker"><span>Rapport de projet</span> {{ $project->pole }}</span>
            <h1>{{ $project->title }}</h1>
            <p>{{ $report->original_name }}</p>
        </header>

        @if ($fullAccess)
            <div class="report-reader__notice">
                <span>Version complète</span>
                <a class="button button-outline" href="{{ route('public.projects.report.viewer', $project) }}" rel="noopener" target="_blank">
                    Ouvrir le rapport complet <span aria-hidden="true">↗</span>
                </a>
            </div>
            <p class="report-reader__hint">Le lecteur PDF du navigateur permet de parcourir les pages. Pour les rapports Word, il présente le texte du document; téléchargez l’original pour retrouver sa mise en page et ses images.</p>
            <a class="button button-orange project-external-link" href="{{ route('public.projects.report.download', $project) }}">
                Télécharger le rapport <span aria-hidden="true">↓</span>
            </a>
        @else
            <div class="report-reader__notice">
                <span>Aperçu limité · {{ $previewPages }} page{{ $previewPages > 1 ? 's' : '' }}</span>
                @guest
                    <a class="underlined-link" href="{{ route('login') }}">Se connecter pour consulter la version complète <span aria-hidden="true">↗</span></a>
                @endguest
            </div>
            <div
                class="report-reader__preview"
                data-report-preview
                data-page-count="{{ $previewPages }}"
                data-page-url="{{ route('public.projects.report.preview.page', [$project, 'page' => 1]) }}"
            >
                <div class="report-reader__page">
                    <img
                        alt="Page 1 de l’aperçu limité du rapport {{ $project->title }}"
                        data-report-preview-image
                        src="{{ route('public.projects.report.preview.page', [$project, 'page' => 1]) }}"
                    >
                </div>
                <p class="report-reader__error" data-report-preview-error hidden role="alert">Cette page de l’aperçu n’a pas pu être affichée. Réessayez dans quelques instants.</p>
                <div class="report-reader__controls" aria-label="Navigation dans l’aperçu du rapport">
                    <button type="button" data-report-preview-previous disabled>Page précédente</button>
                    <span aria-live="polite">Page <strong data-report-preview-current>1</strong> sur {{ $previewPages }}</span>
                    <button type="button" data-report-preview-next @disabled($previewPages <= 1)>Page suivante</button>
                </div>
            </div>
            <p class="report-reader__hint">L’aperçu est limité aux premières pages, sans dépasser la moitié du rapport. Les rapports Word sont prévisualisés sous forme de texte paginé; les images et la mise en page d’origine sont disponibles dans le téléchargement complet pour les membres autorisés.</p>
        @endif
    </article>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-report-preview]').forEach((preview) => {
            const image = preview.querySelector('[data-report-preview-image]');
            const current = preview.querySelector('[data-report-preview-current]');
            const previous = preview.querySelector('[data-report-preview-previous]');
            const next = preview.querySelector('[data-report-preview-next]');
            const error = preview.querySelector('[data-report-preview-error]');
            const pageCount = Number(preview.dataset.pageCount);
            const pageUrl = new URL(preview.dataset.pageUrl, window.location.origin);
            let page = 1;

            const updatePage = (nextPage) => {
                page = nextPage;
                pageUrl.searchParams.set('page', String(page));
                image.src = pageUrl.toString();
                image.alt = `Page ${page} de l’aperçu limité du rapport`;
                current.textContent = String(page);
                previous.disabled = page === 1;
                next.disabled = page === pageCount;
                error.hidden = true;
            };

            previous.addEventListener('click', () => updatePage(page - 1));
            next.addEventListener('click', () => updatePage(page + 1));
            image.addEventListener('error', () => {
                error.hidden = false;
            });
        });
    </script>
@endpush
