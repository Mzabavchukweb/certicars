@include('errors.minimal', [
    'code'    => 403,
    'title'   => 'Brak dostępu',
    'message' => 'Nie masz uprawnień, aby wyświetlić tę stronę. Zaloguj się na właściwe konto lub wróć na stronę główną.',
    'color'   => '#ef4444',
    'iconSvg' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
])
