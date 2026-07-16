@extends('layouts.public')
@section('meta_title_full','Polityka plików cookies | CertiCars')
@section('title','Polityka plików cookies')
@section('description','Polityka plików cookies serwisu CertiCars — jakie pliki cookies stosujemy, w jakim celu oraz jak zarządzać zgodami.')

@section('styles')
@include('pages.partials.legal-styles')
.lg-ck-table{width:100%;border-collapse:collapse;margin:8px 0 20px;font-size:13.5px}
.lg-ck-table th,.lg-ck-table td{text-align:left;padding:11px 14px;border-bottom:1px solid var(--lg-line);vertical-align:top}
.lg-ck-table th{background:#f8fafc;font-weight:800;color:#334155;font-size:12px;text-transform:uppercase;letter-spacing:.4px}
.lg-ck-table td{color:#334155;line-height:1.5}
.lg-ck-tag{display:inline-block;font-size:11px;font-weight:700;padding:3px 9px;border-radius:50px}
.lg-ck-tag.req{background:#dcfce7;color:#16a34a}
.lg-ck-tag.opt{background:#e0edff;color:#0066ff}
.lg-ck-manage{display:inline-flex;align-items:center;gap:8px;background:var(--lg-blue);color:#fff;border:none;font:inherit;font-size:14px;font-weight:700;padding:12px 22px;border-radius:50px;cursor:pointer;margin-top:6px;transition:background .15s}
.lg-ck-manage:hover{background:#0052cc}
.lg-ck-manage svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}
.lg-ck-wrap{overflow-x:auto}
@endsection

@section('content')
<section class="lg-hero">
    <div class="lg-in">
        <div class="lg-eyebrow"><x-icon name="cookie" size="14" :strokeWidth="2.2"/> Dokumenty</div>
        <h1>Polityka plików cookies</h1>
        <p>Informacje o plikach cookies stosowanych w serwisie certicars.pl oraz o sposobie zarządzania zgodami.</p>
    </div>
</section>

<section class="lg-body">
    <div class="lg-in lg-card">
        <div class="lg-updated"><x-icon name="calendar" size="14" :strokeWidth="2"/> Ostatnia aktualizacja: {{ $updated }}</div>

        <h2>1. Czym są pliki cookies</h2>
        <p>Pliki cookies to niewielkie pliki tekstowe zapisywane na urządzeniu Użytkownika podczas korzystania z serwisu. Umożliwiają one prawidłowe działanie strony, zapamiętanie preferencji oraz — za zgodą Użytkownika — prowadzenie statystyk i analiz.</p>

        <h2>2. Rodzaje stosowanych plików cookies</h2>
        <div class="lg-ck-wrap">
        <table class="lg-ck-table">
            <thead>
                <tr><th>Kategoria</th><th>Cel</th><th>Zgoda</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Niezbędne</strong></td>
                    <td>Zapewniają podstawowe funkcje serwisu (bezpieczeństwo, sesja, obsługa formularzy, zapamiętanie decyzji o zgodach). Bez nich serwis nie działa poprawnie.</td>
                    <td><span class="lg-ck-tag req">Zawsze aktywne</span></td>
                </tr>
                <tr>
                    <td><strong>Analityczne / statystyczne</strong></td>
                    <td>Pozwalają zrozumieć, w jaki sposób Użytkownicy korzystają z serwisu, aby ulepszać jego działanie. Dane gromadzone są w sposób zbiorczy.</td>
                    <td><span class="lg-ck-tag opt">Wymaga zgody</span></td>
                </tr>
                <tr>
                    <td><strong>Funkcjonalne</strong></td>
                    <td>Umożliwiają zapamiętanie wyborów Użytkownika (np. ulubione pojazdy) w celu wygodniejszego korzystania z serwisu.</td>
                    <td><span class="lg-ck-tag opt">Wymaga zgody</span></td>
                </tr>
                <tr>
                    <td><strong>Marketingowe</strong></td>
                    <td>Służą prezentowaniu dopasowanych treści i mierzeniu skuteczności działań. Stosowane wyłącznie po wyrażeniu zgody.</td>
                    <td><span class="lg-ck-tag opt">Wymaga zgody</span></td>
                </tr>
            </tbody>
        </table>
        </div>

        <h2>3. Podstawa prawna</h2>
        <p>Pliki cookies niezbędne stosowane są na podstawie prawnie uzasadnionego interesu Administratora (art. 6 ust. 1 lit. f RODO) oraz art. 173 Prawa telekomunikacyjnego. Pozostałe kategorie plików cookies stosowane są wyłącznie po wyrażeniu przez Użytkownika dobrowolnej zgody (art. 6 ust. 1 lit. a RODO), którą można w każdej chwili wycofać.</p>

        <h2>4. Zarządzanie zgodami</h2>
        <p>Podczas pierwszej wizyty w serwisie wyświetlany jest baner umożliwiający zaakceptowanie wszystkich plików cookies, odrzucenie plików opcjonalnych lub wybór poszczególnych kategorii. Swoją decyzję możesz zmienić w dowolnym momencie:</p>
        <p><button type="button" class="lg-ck-manage" onclick="if(window.ccCookieOpen)window.ccCookieOpen()"><x-icon name="sliders-horizontal" size="15" :strokeWidth="2.2"/> Zmień ustawienia cookies</button></p>
        <p>Możesz również zarządzać plikami cookies z poziomu ustawień swojej przeglądarki — w tym je blokować lub usuwać. Ograniczenie plików cookies może wpłynąć na niektóre funkcje serwisu.</p>

        <h2>5. Więcej informacji</h2>
        <p>Zasady przetwarzania danych osobowych opisane są w <a href="{{ route('privacy') }}">Polityce prywatności</a>. W sprawach dotyczących plików cookies można kontaktować się pod adresem <a href="mailto:kontakt@certicars.pl">kontakt@certicars.pl</a>.</p>
    </div>
</section>
@endsection
