# Projekt: CertiCars

## Stack
- Laravel 11, Filament 3, Blade + Livewire
- MySQL, Tailwind CSS

## Zasady
- Nie czytaj node_modules, vendor, storage
- Nie generuj testów jeśli nie proszę
- Krótkie odpowiedzi, bez tłumaczenia oczywistości
- Edytuj tylko pliki których dotyczył request

## Ignoruj zawsze
- /vendor, /node_modules, /.git, /storage
# Claude Code — Context Management Cheatsheet

## Zasada główna
Plan first. Build once. Reset early. Often.
Każdy token wydany na planowanie z góry oszczędza 10x na poprawkach. ⚠️ (claim bez źródła)

## Jak działa kontekst
- Każda wiadomość czyta wszystko, co było wcześniej
- Token ≈ jedno słowo
- Koszt nie sumuje się — kompounduje (Message 1 = 500 tokens, Message 30 = ~15,500 tokens)
- Im dłuższa sesja, tym gorsze retrieval i więcej halucynacji ("AI dementia")

## Session chain (workflow)
1. **Discovery** → przeczytaj codebase, wygeneruj summary doc
2. **Planning** → przeczytaj summary, zrób task plan
3. **Execution** → przeczytaj plan, buduj

## Habity które działają
- [ ] Reset przy 12% kontekstu (nie czekaj na auto-compact przy ~95%)
- [ ] Trzymaj `CLAUDE.md` < 200 linii
- [ ] Startuj sesję w **plan mode** (Shift+Tab cyklicznie)
- [ ] Zapisuj progres w plikach trackerów — nie ufaj pamięci modelu
- [ ] Używaj `/rewind` (lub Esc Esc) żeby wyrzucić nieudane próby
- [ ] Konwertuj PDF/docs do Markdown przed wrzuceniem
- [ ] Używaj sub-agentów do researchu (świeży kontekst, tańszy model)
- [ ] `/btw` do pytań pobocznych — nie zaśmieca głównego wątku

## Manual reset workflow (przy 12%)
1. Poproś Claude'a o pełne podsumowanie sesji
2. `/clear` — wyczyść sesję
3. Wklej podsumowanie z powrotem i kontynuuj
> Reset powinien czuć się jakby nic się nie zmieniło.

## Komendy (zweryfikowane w docs)
- `/context` — pokaż użycie kontekstu (siatka)
- `/clear` — wyczyść konwersację
- `/compact [focus]` — kompresuj historię z opcjonalnym focusem
- `/rewind` (alias `/undo`, też Esc Esc) — cofnij się do checkpointu
- `/btw` — pytanie poboczne bez zaśmiecania głównego wątku
- `/agents` — zarządzanie sub-agentami
- `/memory` — edytuj CLAUDE.md
- `/usage` — koszty i tokeny
- Shift+Tab — przełącz między normal / auto-accept / plan mode
