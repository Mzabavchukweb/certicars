@include('errors.minimal', [
    'code'    => 429,
    'title'   => 'Zbyt wiele prób',
    'message' => 'Wysłałeś/aś zbyt dużo zapytań w krótkim czasie. Poczekaj chwilę i spróbuj ponownie.',
    'color'   => '#f59e0b',
    'iconSvg' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
])
