# CertiCars — platforma komisowa certyfikowanych samochodów używanych

> Dokument prezentacyjny — co i jak działa w obecnej wersji serwisu.

---

## 1. Przegląd projektu

**CertiCars** to kompletna platforma komisowa dla dealera aut używanych. Klient końcowy przegląda samochody na publicznej stronie, a właściciel komisu zarządza całą ofertą z poziomu panelu administracyjnego — bez dotykania kodu.

Serwis składa się z dwóch warstw:
- **Strona publiczna** — dla klientów szukających auta
- **Panel administracyjny** — dla zespołu komisu

---

## 2. Strona publiczna — co widzi klient

### 2.1 Strona główna (`/`)

- Efektowny hero z wyszukiwarką aut w pasku top
- Karuzela **wyróżnionych ofert** (ręcznie ustawianych przez admina)
- **Kategorie** pojazdów z ikonami (SUV, Sedan, Kombi, Coupé, Hatchback, Kabriolet)
- Sekcja "Dlaczego CertiCars" — wartości marki, proces certyfikacji
- Footer z danymi kontaktowymi, regulaminem

### 2.2 Katalog aut (`/samochody`)

- **Zaawansowane filtry:**
  - marka, rodzaj paliwa, kategoria, rodzaj skrzyni
  - zakres ceny (od/do)
  - rocznik (od/do)
  - przebieg (od/do)
  - moc (od/do)
- **Sortowanie:** cena rosnąco/malejąco, przebieg, rok, data dodania
- **Paginacja** (12 aut na stronę), zachowuje filtry w URL
- Karty aut z miniaturką, podstawowymi parametrami, ceną, przyciskiem "Zobacz szczegóły"
- Oznaczenia "Wyróżnione", "Sprzedane"

### 2.3 Szczegóły auta (`/samochody/{nazwa-auta}`)

To **serce platformy** — każde auto ma pełną kartę techniczną:

**Galeria zdjęć — 3 tryby:**
- 📷 **Wszystkie zdjęcia** — standardowa galeria z lightboxem (klik = pełny ekran, strzałki / ESC)
- 🔄 **Widok 360°** — sekwencja 24-60 zdjęć dookoła auta. Klient przeciąga myszką lub palcem → auto się obraca. Auto-obrót jednym kliknięciem.
- 🌐 **Wnętrze 360°** — pełna panorama wnętrza (Pannellum). Klient rozgląda się jak w Google Street View. Zoom, fullscreen, auto-rotacja po 3 sek.

**Pełna specyfikacja:**
- Dane podstawowe (marka, model, rok, przebieg, cena, waluta, typ ceny np. "VAT marża")
- Silnik (paliwo, pojemność, moc KM/kW, skrzynia, szczegóły skrzyni)
- Pojazd (drzwi, siedzenia, masa, tapicerka, VIN, lokalizacja)
- Serwis i emisja (ostatni serwis, przebieg przy serwisie, następna inspekcja, klasa emisji, CO₂, dokumentacja)
- Dokumenty (książka serwisowa, COC, teczka pojazdu, raport HU/AU)
- Sprzedawca (opcjonalnie wyświetlane)

**Wyposażenie:**
- 5 kategorii (bezpieczeństwo, komfort, zewnętrze, wnętrze, dodatkowe)
- Zwijane/rozwijane sekcje

**Stan techniczny:**
- 5 kategorii (silnik, skrzynia, zawieszenie, elektronika, nadwozie)
- Notatki z inspekcji

**Pomiar grubości lakieru:**
- Tabela: każdy element nadwozia + wartość w µm

**Uszkodzenia** (jeśli są):
- Interaktywny schemat auta (5 widoków: góra, przód, tył, lewy bok, prawy bok)
- Kliknięcie w marker pokazuje szczegóły uszkodzenia
- Każde uszkodzenie ma: typ (aktualne / naprawione / powypadkowe), istotność (info/ostrzeżenie/krytyczne), tagi, opis, **zdjęcie**
- Kliknięcie w zdjęcie uszkodzenia = lightbox

**Opony:**
- Zestawy z typem (letnie/zimowe/całoroczne), felgami, informacją czy zamontowane
- Per koło: głębokość bieżnika + stan

**Film z pracy silnika:**
- YouTube/Vimeo embed lub lokalny plik MP4/WebM
- Odtwarzacz wbudowany na stronie

**Przycisk "Pobierz PDF"** — pełna karta techniczna auta jako PDF (3 strony z wszystkimi danymi + zdjęciami, gotowa do wysłania klientowi mailem).

**Podobne oferty** — 3 auta z tej samej marki na dole strony.

### 2.4 Obserwowane (`/obserwowane`)

- Lista aut dodanych do ulubionych
- Działa bez rejestracji (localStorage w przeglądarce)
- Serca na kartach aut w katalogu = dodaj/usuń z obserwowanych

### 2.5 Kontakt (`/kontakt`)

- Formularz: imię, e-mail, telefon, treść
- Walidacja po stronie serwera + przyjazne komunikaty błędów
- **Ochrona antyspamowa:**
  - Honeypot (ukryte pole którego bot nie powinien wypełnić)
  - Rate limit (max 5 wiadomości na minutę z jednego IP)
  - CSRF token
- Wiadomości trafiają do panelu admina
- Zbiera metadane: IP, przeglądarka, data

### 2.6 O nas (`/o-nas`)

- Statyczna strona: misja firmy, statystyki (liczba aut, marek, wyróżnionych), wartości

---

## 3. Panel administracyjny

Dostępny pod `/admin/login`. Chroniony hasłem. Tylko użytkownicy oznaczeni jako "admin" mają dostęp.

### 3.1 Dashboard (`/admin`)

Widok startowy po zalogowaniu. Prezentuje aktualny stan biznesu:

**Ruch na stronie:**
- Wejścia dziś / 7 dni / 30 dni / łącznie
- Odsłony ofert (ile razy konkretne auta były oglądane)
- Liczba wiadomości + licznik nieprzeczytanych

**Stan magazynu:**
- Wszystkie auta, aktywne, sprzedane, szkice, wyróżnione, liczba marek
- **Wartość stocku** (suma cen aktywnych aut) + średnia cena

**Wykres aktywności 14 dni:**
- Trzy linie: wejścia, odsłony ofert, wiadomości
- Interaktywne (tooltip z dokładnymi wartościami)

**🔥 Najpopularniejsze oferty:**
- Top 5 aut z największą liczbą odsłon (łącznie)
- Tabela z miniaturką, ceną, licznikiem wyświetleń

**Źródła ruchu (30 dni):**
- Skąd przychodzą użytkownicy (bezpośrednio, Google, Facebook, inne)

**Top strony (30 dni):**
- Które sekcje są najczęściej odwiedzane

**Ostatnie samochody** + **Ostatnie wiadomości** — szybki podgląd.

### 3.2 Zarządzanie samochodami (`/admin/cars`)

**Lista aut:**
- Tabela z miniaturką, tytułem, ceną, przebiegiem, liczbą odsłon, statusem, gwiazdką "wyróżnione", datą dodania
- **Sortowanie po kolumnach** (klik w nagłówek)
- **Filtry:** wyszukiwanie (marka/model/VIN/ID), marka, status (aktywne/szkic/sprzedane), zakres ceny
- **Zaawansowane akcje zbiorcze:** zaznacz kilka aut i zrobić na nich: oznacz wyróżnione, oznacz sprzedane, przywróć do aktywnych, usuń
- Dla każdego auta w wierszu: podgląd publiczny, PDF, edycja, usuń
- Szybki toggle "wyróżnione" jednym kliknięciem
- Widok mobilny: karty zamiast tabeli

**Dodawanie / edycja auta — formularz z 11 zakładkami:**

1. **Podstawowe** — marka, model, kategoria, nadwozie, cena, waluta, typ ceny (VAT marża/netto), opodatkowanie
2. **Silnik i historia** — data pierwszej rejestracji, przebieg, liczba właścicieli, liczba kluczy, użytkowanie, źródło pozyskania, paliwo, skrzynia i jej szczegóły, pojemność, moc KM/kW, film silnika (URL lub plik)
3. **Pojazd** — drzwi, siedzenia, masa, kolor, kod koloru, tapicerka, VIN, import, lokalizacja, dystans, kraj rejestracji
4. **Serwis i emisja** — ostatni serwis, przebieg przy serwisie, następna inspekcja, dokumentacja, zużycie paliwa, CO₂, klasa emisji, procedura (WLTP/NEDC), książka serwisowa, COC, teczka, HU/AU
5. **Sprzedawca** — dane kontaktowe osoby sprzedającej + notatka komisowa (wewnętrzna) + data przyjęcia pojazdu
6. **Wyposażenie** — 5 kategorii (bezpieczeństwo, komfort, zewnętrze, wnętrze, dodatkowe), każda jako lista pozycji
7. **Stan i lakier** — 5 kategorii stanu technicznego + tabela pomiarów grubości lakieru (element + wartość µm)
8. **Uszkodzenia** — **najnowocześniejsza sekcja:**
   - 5 widoków auta w technicznej grafice wektorowej (góra/przód/tył/lewy bok/prawy bok)
   - Kliknięcie w karoserię stawia marker z numerem
   - Dla każdego markera: obszar, typ, istotność, tagi, opis, zdjęcie (upload)
   - Markery można przeciągać, usuwać, klikać (przewija do karty)
   - Licznik markerów per widok w tabach
9. **Opony** — nieograniczona liczba zestawów (letnie/zimowe/całoroczne + felgi + czy zamontowane) × 4 koła (pozycja, bieżnik, stan)
10. **Zdjęcia** — galeria + zdjęcia uszkodzeń + **360° zewnątrz** + **panorama 360° wnętrza**:
    - Galeria standardowa: drag&drop, wybór głównego radio, alt text per zdjęcie
    - **Widok 360° zewnątrz:** drag&drop 24-60 zdjęć (sekwencja). System sortuje po nazwie pliku i odtwarza obrót. Można robić zwykłym telefonem (tryb seryjny + obejście auta).
    - **Panorama 360° wnętrza:** drag&drop jeden plik (equirectangular JPG). Najprostsze: darmowa apka "Google Street View" w telefonie — siadasz w aucie, apka prowadzi przez wszystkie kierunki, eksportujesz JPG.
    - Każda sekcja z miniaturkami, licznikiem klatek, możliwością podmiany / usunięcia
11. **SEO** — **styl Yoast:**
    - Focus keyword (fraza pod którą chcesz być znaleziony)
    - Meta title (licznik znaków: czerwony/żółty/zielony)
    - Meta description (licznik znaków)
    - Live podgląd wyników Google (SERP) desktop + mobile
    - Live podgląd OG (Facebook)
    - Analiza SEO ze score 0-100 (sprawdza długość tytułu/opisu, obecność focus keyword w tytule/opisie/URL, obecność marki i modelu w tytule)
    - Checkbox "noindex" (ukryj stronę z Google)

**Funkcje ułatwiające pracę:**
- **Sticky save bar** — gdy formularz ma niezapisane zmiany, u dołu ekranu pojawia się pasek "Masz niezapisane zmiany" z przyciskiem zapisz
- **Auto-generacja slug i ID** — system sam generuje przyjazny URL (np. `audi-a4-20-tfsi-quattro`) i unikalny identyfikator (np. `CC-2026-001`)
- **Spójność statusu** — jeśli oznaczysz jako sprzedane, status auto-zmieni się na "sprzedane"
- **Auto-alt zdjęć** — jeśli nie wpiszesz alt, system użyje tytułu auta ("Audi A4 — zdjęcie 1")
- **Podgląd PDF** jednym kliknięciem z listy
- **Powrót do strony publicznej** jednym kliknięciem

### 3.3 Marki (`/admin/brands`)

- Lista wszystkich marek z licznikiem aut każdej
- Dodaj nową (nazwa → slug generowany automatycznie)
- Edycja inline
- Usuwanie (blokada: nie można usunąć marki z przypisanymi autami)

### 3.4 Wiadomości (`/admin/messages`)

Skrzynka z formularza kontaktowego:

- **Licznik nieprzeczytanych** w sidebarze (badge)
- **Filtry:** nieprzeczytane / przeczytane / wszystkie, wyszukiwanie pełnotekstowe
- **Akcje zbiorcze:** oznacz jako przeczytane / nieprzeczytane / usuń
- Po otwarciu wiadomości automatyczne oznaczenie jako przeczytana
- Widok szczegółowy: dane kontaktowe + treść + metadane (IP, przeglądarka, data)
- Przycisk **"Odpowiedz e-mailem"** — otwiera mailto z tematem "Re:"
- Możliwość oznaczenia z powrotem jako nieprzeczytane

### 3.5 Profil admina (`/admin/profile`)

- Zmiana imienia/emaila
- Zmiana hasła (wymaga obecnego hasła)
- Walidacja siły hasła (min. 8 znaków + confirmation)

### 3.6 Globalne narzędzia

**Command Palette (⌘K lub `/`):**
- Otwiera się z każdej strony admina
- Szybka nawigacja: wpisz "samochody" → przechodzi; "nowy" → otwiera formularz
- Wyszukiwanie aut po nazwie/VIN/ID (z miniaturką i ceną w podpowiedziach)
- Nawigacja strzałkami, Enter

**Skróty klawiaturowe:**
- `⌘K` lub `/` — panel szybkich akcji
- `?` — pokaż pomoc ze skrótami
- `n` — nowe auto
- `g d` — dashboard, `g c` — samochody, `g m` — wiadomości, `g b` — marki
- `Esc` — zamknij dialog

**Global search w topbarze:**
- Live-search aut z autocompletion (miniaturki + ID + cena)

**Mobile:**
- Hamburger menu (pełnoekranowy sidebar)
- Tabele zamieniają się w karty

**Toasty:**
- Wszystkie komunikaty sukcesu/błędu jako eleganckie powiadomienia top-right, auto-dismiss

**Custom modal zamiast `confirm()`:**
- Przed usunięciem ładne okno dialogowe z ostrzeżeniem

**Sticky save bar** na długich formularzach.

**Filter chips** — aktywne filtry jako usuwalne tagi nad listą.

---

## 4. Bezpieczeństwo

- **Rate limiting:**
  - Kontakt: max 5 wysyłek/min z jednego IP
  - Logowanie: max 10 prób/min z jednego IP
  - Reset hasła: max 5 prób/min
- **CSRF tokens** we wszystkich formularzach (419 dla ataków)
- **Honeypot** na formularzu kontaktowym (odrzuca boty)
- **Autoryzacja:**
  - Niezalogowany → redirect do logowania
  - Zalogowany nie-admin → 403 Forbidden
  - Tylko użytkownicy `is_admin=true` mają dostęp
- **Walidacja plików:**
  - Zdjęcia: tylko JPG/PNG/WEBP/AVIF, max 8 MB, max 30 w jednym uploadzie
  - Filmy: tylko MP4/WebM/MOV/AVI/MKV, max 100 MB
  - Bot próbujący wrzucić `.php` → 302 redirect z błędem walidacji
- **Sesja szyfrowana** + 12h lifetime
- **Hasła hashowane** przez bcrypt (Laravel standard)
- **Ukryty `.env`** poza document root, nigdy w git
- **SQL injection** — niemożliwy (Eloquent ORM + prepared statements)
- **XSS** — Blade templating escapuje wszystko z automatu
- **Reset hasła** przez e-mail z jednorazowym tokenem (Laravel Password facade)

---

## 5. SEO

- **Meta tags per strona:** title, description, canonical, OG (Facebook), Twitter Cards
- **JSON-LD Vehicle schema** dla każdego auta → Google może pokazać w wynikach: cena, rok, przebieg, stan jako bogate dane
- **Sitemap.xml** generowany automatycznie (tylko aktywne, niesprzedane, niezablokowane auta)
- **robots.txt** z właściwym disallow (admin, storage, obserwowane) + link do sitemap
- **URL przyjazne SEO:** `/samochody/audi-a4-20-tfsi-quattro` (nie `?id=1`)
- **Yoast-style SEO** panel w admin (zob. sekcja 3.2)
- **Alt text** na każdym zdjęciu (auto-generowany lub ręczny)
- **Noindex** per auto dla szkiców / sprzedanych

---

## 6. Analityka

- **Wbudowane liczniki** (brak zewnętrznych narzędzi typu Google Analytics — pełna prywatność):
  - Każde wejście na publiczną stronę zapisuje: path, IP, referer, session ID, user-agent
  - Każda odsłona konkretnego auta zapisuje: car_id, session, IP
  - **Deduplicja:** refresh w ciągu 30 min = 1 odsłona (realne unikalne sesje)
  - **Filtrowanie botów** po user-agent
  - **Admin wykluczony** — oglądanie ofert jako admin nie psuje statystyk
- Statystyki widoczne w dashboardzie oraz per auto (na stronie edycji wykres 30-dniowy odsłon)

---

## 7. Wydajność

- Czas ładowania: **<50 ms** panel, **<20 ms** katalog (na lokalnym)
- Eager loading relacji (brak problemu N+1)
- Cache filtrów katalogu (10 min TTL)
- Lazy loading zdjęć (`loading="lazy"`)
- Asyst JS ładowany defer
- Gotowe do podłączenia CDN (Cloudflare R2 / AWS S3) dla plików

---

## 8. Obsługa mobilna

- **100% responsywne** — każdy element dostosowuje się do ekranu
- Hamburger menu w panelu admina
- Tabele → karty na urządzeniach <900px
- Touch-friendly (gest swipe w galeriach, duże przyciski)
- Optymalizacja dla iOS/Android/Desktop

---

## 9. Dostępność (WCAG)

- Skip link "Przejdź do treści" (Tab)
- Semantyczny HTML (header/nav/main/footer)
- Aria-labels na elementach interaktywnych
- Focus-visible outline (klawiatura)
- Kontrasty zgodne z WCAG AA
- Alt text na wszystkich zdjęciach

---

## 10. Stack technologiczny (dla zainteresowanych)

- **Backend:** Laravel 11 + PHP 8.2
- **Frontend:** Blade templates + vanilla JS (zero ciężkich frameworków = szybkie)
- **CSS:** Tailwind + inline styles (modułowość)
- **Baza:** SQLite (dev) → MySQL/PostgreSQL (prod)
- **PDF:** DomPDF
- **Wykresy:** Chart.js
- **Ikony:** Lucide
- **Obsługa plików:** Laravel Storage (lokalnie + łatwe podpięcie S3)

---

## 11. Status projektu

✅ **Wdrożone i w pełni działające:**
- Cała strona publiczna (home, katalog, szczegóły, kontakt, o nas, obserwowane)
- Cały panel admina (dashboard, samochody, marki, wiadomości, profil)
- SEO (meta, OG, JSON-LD, sitemap, robots)
- Bezpieczeństwo (rate limit, CSRF, honeypot, walidacja plików, szyfrowanie sesji)
- Analityka (wejścia, odsłony, top strony, top referery)
- Reset hasła przez e-mail
- PDF karty auta
- 5-widoków schemat uszkodzeń z markerami i zdjęciami
- Lightbox, command palette, skróty klawiszowe
- A11y, mobile, toasty
- **28 testów automatycznych (60 asercji) — wszystkie zielone**

⏳ **Wymaga konfiguracji na produkcji (decyzje Klienta):**
- Wybór hostingu (rekomendacja: mydevil.net za ~21 zł/mc)
- Migracja bazy SQLite → MySQL
- Konfiguracja SMTP (do wysyłki maili kontaktowych i resetu hasła)
- Podpięcie domeny + SSL (Let's Encrypt darmowy)
- Ustawienie własnego hasła admina

---

## 12. Demo na żywo

- **Strona publiczna:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
- **Panel admina:** [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login)
  - Login: `admin@certicars.pl`
  - Hasło: `admin123`
  - (⚠️ zmieni się przy wdrożeniu produkcyjnym)

---

## 13. Scenariusz pokazu dla Klienta (15 min)

1. **Start:** `/` → zwróć uwagę na hero, wyróżnione auta, kategorie
2. **Katalog:** `/samochody` → filtry, sortowanie, chip'y, paginacja
3. **Szczegóły auta:** klik w dowolne auto → galeria lightbox, specyfikacja, uszkodzenia (schemat klikalny!), opony, wideo, PDF
4. **Kontakt:** `/kontakt` → wyślij testową wiadomość
5. **Logowanie:** `/admin/login`
6. **Dashboard:** pokaż wykres aktywności, top auta, źródła ruchu
7. **Skrzynka:** pokaż że wiadomość z punktu 4 tu trafiła
8. **Edycja auta:** wybierz jedno → przeklikaj wszystkie 11 zakładek
   - **Wow-factor:** tab "Uszkodzenia" — kliknij w auto, zobacz marker
   - **Wow-factor:** tab "SEO" — pokaż live SERP preview i scoring
9. **Lista aut:** pokaż bulk actions (zaznacz 3, oznacz jako wyróżnione)
10. **Command Palette:** `⌘K` → szybka nawigacja
11. **Mobile:** otwórz na telefonie (użyj Chrome DevTools)
12. **SEO:** otwórz `/sitemap.xml` i `/robots.txt` — pokaż że Google zadowolone
13. **Podgląd źródła** na stronie auta: pokaż JSON-LD schema (Google rich snippets)

---

## 14. Wyróżniki marketingowe dla Klienta

Trzy rzeczy, które wyróżniają ten projekt na tle konkurencji:

1. **Interaktywny schemat uszkodzeń** — rzadkość nawet u dużych graczy. Kupujący widzi dokładnie gdzie, jakie, jak poważne, ze zdjęciami.
2. **SEO klasy Yoast wbudowany w admin** — właściciel komisu sam optymalizuje strony aut bez pomocy SEO specjalisty.
3. **Własna analityka bez Google Analytics** — pełna prywatność, RODO-friendly, żadnych cookies trzecich stron.

---

*Dokument wygenerowany automatycznie. Aktualny stan: kwiecień 2026.*