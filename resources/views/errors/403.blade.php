@include('errors.minimal', [
    'code'    => 403,
    'title'   => 'Brak dostępu',
    'message' => 'Nie masz uprawnień, aby wyświetlić tę stronę. Zaloguj się na właściwe konto lub wróć na stronę główną.',
    'color'   => '#ef4444',
    'iconName' => 'lock',
])
