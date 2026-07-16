@extends('layouts.public')
@section('meta_title_full','Regulamin | CertiCars')
@section('title','Regulamin serwisu')
@section('description','Regulamin serwisu internetowego CertiCars — zasady korzystania z serwisu, charakter prezentowanych treści oraz postanowienia końcowe.')

@section('styles')
@include('pages.partials.legal-styles')
@endsection

@section('content')
<section class="lg-hero">
    <div class="lg-in">
        <div class="lg-eyebrow"><x-icon name="file-text" size="14" :strokeWidth="2.2"/> Dokumenty</div>
        <h1>Regulamin serwisu</h1>
        <p>Zasady korzystania z serwisu internetowego certicars.pl oraz charakter prezentowanych w nim treści.</p>
    </div>
</section>

<section class="lg-body">
    <div class="lg-in lg-card">
        <div class="lg-updated"><x-icon name="calendar" size="14" :strokeWidth="2"/> Ostatnia aktualizacja: {{ $updated }}</div>

        <h2 id="postanowienia">§1. Postanowienia ogólne</h2>
        <p>Niniejszy Regulamin określa zasady korzystania z serwisu internetowego dostępnego pod adresem certicars.pl (dalej: „Serwis"), prowadzonego przez Marsela Gebla prowadzącego działalność gospodarczą pod firmą CertiCars Marsel Gebel, NIP 8542461227, z adresem: Lipnik, 73-110 Stargard (dalej: „Usługodawca").</p>
        <div class="lg-admin">
            <dl>
                <dt>Usługodawca</dt><dd>CertiCars Marsel Gebel</dd>
                <dt>NIP</dt><dd>8542461227</dd>
                <dt>Adres</dt><dd>Lipnik, 73-110 Stargard</dd>
                <dt>E-mail</dt><dd>kontakt@certicars.pl</dd>
                <dt>Telefon</dt><dd>+48 515 440 623</dd>
            </dl>
        </div>

        <h2 id="definicje">§2. Definicje</h2>
        <ul>
            <li><strong>Serwis</strong> — strona internetowa certicars.pl wraz z podstronami.</li>
            <li><strong>Użytkownik</strong> — każda osoba korzystająca z Serwisu.</li>
            <li><strong>CertiCheck</strong> — rozszerzona prezentacja wybranych pojazdów przygotowana przez Usługodawcę, obejmująca m.in. pomiary lakieru, materiały 360°, mapę zauważonych śladów oraz dokumenty.</li>
        </ul>

        <h2 id="zakres">§3. Zakres i charakter treści</h2>
        <p>Serwis ma charakter informacyjny i prezentuje ofertę pojazdów używanych oraz materiały z nią związane. Korzystanie z Serwisu jest bezpłatne i nie wymaga rejestracji.</p>
        <div class="lg-note">
            <p><strong>Treści na stronie mają charakter informacyjny i nie stanowią oferty w rozumieniu art. 66 §1 Kodeksu cywilnego.</strong></p>
        </div>
        <div class="lg-note">
            <p><strong>Informacje zawarte na tej stronie zostały przygotowane z najwyższą starannością. Nie stanowią jednak oferty handlowej w rozumieniu art. 66 §1 Kodeksu cywilnego.</strong></p>
        </div>
        <p>Prezentowane w Serwisie ceny, parametry i opisy pojazdów mają charakter poglądowy i mogą ulec zmianie. Warunki ewentualnej transakcji ustalane są indywidualnie i potwierdzane w odrębnej umowie. Zdjęcia oraz wizualizacje mogą nieznacznie różnić się od stanu faktycznego pojazdu.</p>

        <h2 id="certicheck">§4. CertiCheck</h2>
        <p>Materiały CertiCheck przedstawiają zauważony stan pojazdu na dzień przygotowania materiałów, w zakresie dostępnym bez demontażu elementów. CertiCheck nie stanowi ekspertyzy technicznej, opinii rzeczoznawcy, zapewnienia o braku wad ani gwarancji wykrycia wszystkich usterek, w tym wad ukrytych. Zakres materiałów może różnić się w zależności od pojazdu i dostępnej dokumentacji. Użytkownik ma możliwość przeprowadzenia oględzin, jazdy próbnej oraz sprawdzenia pojazdu w wybranym serwisie przed zakupem.</p>

        <h2 id="korzystanie">§5. Zasady korzystania z Serwisu</h2>
        <ul>
            <li>Użytkownik zobowiązany jest korzystać z Serwisu zgodnie z prawem, dobrymi obyczajami oraz niniejszym Regulaminem.</li>
            <li>Zabronione jest dostarczanie treści o charakterze bezprawnym oraz podejmowanie działań zakłócających funkcjonowanie Serwisu.</li>
            <li>Do korzystania z Serwisu niezbędne jest urządzenie z dostępem do internetu oraz aktualna przeglądarka internetowa z obsługą JavaScript i plików cookies.</li>
        </ul>

        <h2 id="zapytania">§6. Formularz kontaktowy i zapytania</h2>
        <p>Za pośrednictwem Serwisu Użytkownik może skontaktować się z Usługodawcą, w szczególności przy użyciu formularza kontaktowego lub formularza zapytania o pojazd. Wysłanie zapytania nie jest równoznaczne z zawarciem umowy ani ze złożeniem oferty. Zasady przetwarzania danych osobowych opisane są w <a href="{{ route('privacy') }}">Polityce prywatności</a>.</p>

        <h2 id="wlasnosc">§7. Prawa autorskie</h2>
        <p>Treści Serwisu, w tym teksty, zdjęcia, materiały 360°, grafiki oraz układ i szata graficzna, podlegają ochronie prawnej i stanowią własność Usługodawcy lub są wykorzystywane na podstawie odpowiednich uprawnień. Ich kopiowanie lub wykorzystywanie bez zgody Usługodawcy jest zabronione.</p>

        <h2 id="reklamacje">§8. Reklamacje dotyczące działania Serwisu</h2>
        <p>Uwagi i reklamacje dotyczące funkcjonowania Serwisu można zgłaszać na adres <a href="mailto:kontakt@certicars.pl">kontakt@certicars.pl</a>. Usługodawca rozpatruje zgłoszenia niezwłocznie, nie później niż w terminie 14 dni.</p>

        <h2 id="koncowe">§9. Postanowienia końcowe</h2>
        <ul>
            <li>Usługodawca zastrzega sobie prawo do zmiany Regulaminu; aktualna wersja publikowana jest w Serwisie wraz z datą aktualizacji.</li>
            <li>W sprawach nieuregulowanych w Regulaminie zastosowanie mają przepisy prawa polskiego, w szczególności Kodeksu cywilnego.</li>
            <li>Jeżeli którekolwiek z postanowień Regulaminu okaże się nieważne, pozostałe postanowienia zachowują moc.</li>
        </ul>
    </div>
</section>
@endsection
