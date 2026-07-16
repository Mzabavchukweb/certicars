@extends('layouts.public')
@section('meta_title_full','Polityka prywatności | CertiCars')
@section('title','Polityka prywatności')
@section('description','Polityka prywatności serwisu CertiCars — informacje o administratorze danych, celach i podstawach przetwarzania oraz prawach użytkownika zgodnie z RODO.')

@section('styles')
@include('pages.partials.legal-styles')
@endsection

@section('content')
<section class="lg-hero">
    <div class="lg-in">
        <div class="lg-eyebrow"><x-icon name="shield-check" size="14" :strokeWidth="2.2"/> Dokumenty</div>
        <h1>Polityka prywatności</h1>
        <p>Zasady przetwarzania danych osobowych oraz ochrony prywatności użytkowników serwisu certicars.pl.</p>
    </div>
</section>

<section class="lg-body">
    <div class="lg-in lg-card">
        <div class="lg-updated"><x-icon name="calendar" size="14" :strokeWidth="2"/> Ostatnia aktualizacja: {{ $updated }}</div>

        <div class="lg-toc">
            <strong>Spis treści</strong>
            <ol>
                <li><a href="#administrator">Administrator danych</a></li>
                <li><a href="#cele">Cele i podstawy prawne przetwarzania</a></li>
                <li><a href="#zakres">Zakres przetwarzanych danych</a></li>
                <li><a href="#okres">Okres przechowywania</a></li>
                <li><a href="#odbiorcy">Odbiorcy danych</a></li>
                <li><a href="#prawa">Prawa użytkownika</a></li>
                <li><a href="#cookies">Pliki cookies</a></li>
                <li><a href="#zmiany">Zmiany polityki</a></li>
            </ol>
        </div>

        <h2 id="administrator">1. Administrator danych</h2>
        <p>Administratorem danych osobowych przetwarzanych w związku z korzystaniem z serwisu internetowego dostępnego pod adresem certicars.pl jest:</p>
        <div class="lg-admin">
            <dl>
                <dt>Administrator</dt><dd>Marsel Gebel prowadzący działalność gospodarczą pod firmą CertiCars Marsel Gebel</dd>
                <dt>NIP</dt><dd>8542461227</dd>
                <dt>Adres</dt><dd>Lipnik, 73-110 Stargard</dd>
                <dt>E-mail</dt><dd>kontakt@certicars.pl</dd>
                <dt>Telefon</dt><dd>+48 515 440 623</dd>
            </dl>
        </div>
        <p>We wszystkich sprawach dotyczących przetwarzania danych osobowych oraz korzystania z przysługujących praw można kontaktować się z Administratorem pod adresem e-mail: <a href="mailto:kontakt@certicars.pl">kontakt@certicars.pl</a>.</p>

        <h2 id="cele">2. Cele i podstawy prawne przetwarzania</h2>
        <p>Dane osobowe przetwarzane są zgodnie z Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 z dnia 27 kwietnia 2016 r. (RODO) w następujących celach:</p>
        <ul>
            <li><strong>Obsługa zapytań i formularza kontaktowego</strong> — w celu udzielenia odpowiedzi na przesłane pytanie oraz kontaktu w sprawie prezentowanych pojazdów; podstawa: art. 6 ust. 1 lit. b oraz lit. f RODO (podjęcie działań na żądanie osoby oraz prawnie uzasadniony interes Administratora polegający na obsłudze korespondencji).</li>
            <li><strong>Zawarcie i realizacja umowy</strong> — w przypadku zainteresowania zakupem pojazdu; podstawa: art. 6 ust. 1 lit. b RODO.</li>
            <li><strong>Wypełnienie obowiązków prawnych</strong> — w szczególności podatkowych i rachunkowych; podstawa: art. 6 ust. 1 lit. c RODO.</li>
            <li><strong>Ustalenie, dochodzenie lub obrona roszczeń</strong> — podstawa: art. 6 ust. 1 lit. f RODO (prawnie uzasadniony interes Administratora).</li>
            <li><strong>Analityka i zapewnienie bezpieczeństwa serwisu</strong> — w oparciu o pliki cookies i podobne technologie; podstawa: art. 6 ust. 1 lit. f RODO, a w zakresie plików nieniezbędnych — art. 6 ust. 1 lit. a RODO (zgoda).</li>
        </ul>

        <h2 id="zakres">3. Zakres przetwarzanych danych</h2>
        <p>W zależności od sposobu kontaktu z Administratorem przetwarzane mogą być następujące dane: imię i nazwisko, adres e-mail, numer telefonu, treść wiadomości, a także dane techniczne (adres IP, informacje o urządzeniu i przeglądarce) gromadzone automatycznie w celach bezpieczeństwa i statystycznych. Podanie danych jest dobrowolne, jednak niezbędne do udzielenia odpowiedzi na zapytanie.</p>

        <h2 id="okres">4. Okres przechowywania danych</h2>
        <ul>
            <li>dane z korespondencji i zapytań — przez czas niezbędny do obsługi sprawy, a następnie do czasu przedawnienia ewentualnych roszczeń;</li>
            <li>dane związane z realizacją umowy oraz obowiązkami prawnymi — przez okres wymagany przepisami prawa (m.in. podatkowymi i rachunkowymi);</li>
            <li>dane przetwarzane na podstawie zgody — do czasu jej wycofania.</li>
        </ul>

        <h2 id="odbiorcy">5. Odbiorcy danych</h2>
        <p>Dane osobowe mogą być powierzane podmiotom wspierającym Administratora w prowadzeniu działalności, w szczególności dostawcom usług hostingu, poczty elektronicznej, obsługi IT oraz narzędzi analitycznych — wyłącznie w zakresie niezbędnym i na podstawie umów powierzenia przetwarzania danych. Administrator nie sprzedaje danych osobowych. W przypadku korzystania z narzędzi dostawców spoza Europejskiego Obszaru Gospodarczego przekazanie danych następuje z zapewnieniem odpowiednich zabezpieczeń przewidzianych w RODO.</p>

        <h2 id="prawa">6. Prawa użytkownika</h2>
        <p>Każdej osobie, której dane dotyczą, przysługuje prawo do:</p>
        <ul>
            <li>dostępu do swoich danych oraz otrzymania ich kopii;</li>
            <li>sprostowania (poprawiania) danych;</li>
            <li>usunięcia danych („prawo do bycia zapomnianym");</li>
            <li>ograniczenia przetwarzania;</li>
            <li>przenoszenia danych;</li>
            <li>wniesienia sprzeciwu wobec przetwarzania opartego na prawnie uzasadnionym interesie;</li>
            <li>cofnięcia zgody w dowolnym momencie — bez wpływu na zgodność z prawem przetwarzania dokonanego przed jej cofnięciem.</li>
        </ul>
        <p>Osobie, której dane dotyczą, przysługuje również prawo wniesienia skargi do organu nadzorczego — Prezesa Urzędu Ochrony Danych Osobowych (ul. Stawki 2, 00-193 Warszawa).</p>
        <div class="lg-note">
            <p>Dane nie są wykorzystywane do zautomatyzowanego podejmowania decyzji, w tym profilowania wywołującego skutki prawne wobec użytkownika.</p>
        </div>

        <h2 id="cookies">7. Pliki cookies</h2>
        <p>Serwis korzysta z plików cookies i podobnych technologii. Szczegółowe informacje o rodzajach plików cookies, celach ich stosowania oraz sposobie zarządzania zgodami znajdują się w <a href="{{ route('cookies') }}">Polityce plików cookies</a>.</p>

        <h2 id="zmiany">8. Zmiany polityki prywatności</h2>
        <p>Administrator zastrzega sobie prawo do zmiany niniejszej Polityki prywatności w celu jej dostosowania do zmian w przepisach prawa lub w sposobie funkcjonowania serwisu. Aktualna wersja jest zawsze dostępna na tej stronie, wraz z datą ostatniej aktualizacji.</p>
    </div>
</section>
@endsection
