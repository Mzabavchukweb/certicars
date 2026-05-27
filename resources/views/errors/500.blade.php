@include('errors.minimal', [
    'code'    => 500,
    'title'   => 'Błąd serwera',
    'message' => 'Wystąpił nieoczekiwany problem po naszej stronie. Zespół został o tym powiadomiony. Spróbuj ponownie za chwilę.',
    'color'   => '#ef4444',
    'iconName' => 'alert-triangle',
])
