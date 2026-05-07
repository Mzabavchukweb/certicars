@include('errors.minimal', [
    'code'    => 419,
    'title'   => 'Sesja wygasła',
    'message' => 'Formularz został odrzucony z powodu wygaśnięcia sesji. Odśwież stronę i spróbuj ponownie.',
    'color'   => '#f59e0b',
    'iconSvg' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
])
