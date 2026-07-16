<?php

/*
 * Polskie komunikaty walidacji. Aktywne dzięki APP_LOCALE=pl
 * (config/app.php → 'locale'). Dzięki temu WSZYSTKIE komunikaty walidacji
 * widoczne dla klienta na froncie (formularz kontaktowy, zapytanie o auto itd.)
 * są po polsku.
 */

return [
    'accepted'             => 'Pole :attribute musi zostać zaakceptowane.',
    'active_url'           => 'Pole :attribute nie jest prawidłowym adresem URL.',
    'after'                => 'Pole :attribute musi być datą późniejszą niż :date.',
    'after_or_equal'       => 'Pole :attribute musi być datą późniejszą lub równą :date.',
    'alpha'                => 'Pole :attribute może zawierać wyłącznie litery.',
    'alpha_dash'           => 'Pole :attribute może zawierać wyłącznie litery, cyfry, myślniki i podkreślenia.',
    'alpha_num'            => 'Pole :attribute może zawierać wyłącznie litery i cyfry.',
    'array'                => 'Pole :attribute musi być tablicą.',
    'before'               => 'Pole :attribute musi być datą wcześniejszą niż :date.',
    'before_or_equal'      => 'Pole :attribute musi być datą wcześniejszą lub równą :date.',
    'between'              => [
        'numeric' => 'Pole :attribute musi zawierać się w przedziale od :min do :max.',
        'file'    => 'Plik :attribute musi mieć od :min do :max kilobajtów.',
        'string'  => 'Pole :attribute musi zawierać od :min do :max znaków.',
        'array'   => 'Pole :attribute musi zawierać od :min do :max elementów.',
    ],
    'boolean'              => 'Pole :attribute musi mieć wartość prawda albo fałsz.',
    'confirmed'            => 'Potwierdzenie pola :attribute nie zgadza się.',
    'date'                 => 'Pole :attribute nie jest prawidłową datą.',
    'date_equals'          => 'Pole :attribute musi być datą równą :date.',
    'date_format'          => 'Pole :attribute musi być w formacie :format.',
    'different'            => 'Pola :attribute oraz :other muszą się różnić.',
    'digits'               => 'Pole :attribute musi składać się z :digits cyfr.',
    'digits_between'       => 'Pole :attribute musi mieć od :min do :max cyfr.',
    'email'                => 'Pole :attribute musi być prawidłowym adresem e-mail.',
    'ends_with'            => 'Pole :attribute musi kończyć się jedną z wartości: :values.',
    'exists'               => 'Wybrana wartość pola :attribute jest nieprawidłowa.',
    'file'                 => 'Pole :attribute musi być plikiem.',
    'filled'               => 'Pole :attribute musi być wypełnione.',
    'gt'                   => [
        'numeric' => 'Pole :attribute musi być większe niż :value.',
        'file'    => 'Plik :attribute musi być większy niż :value kilobajtów.',
        'string'  => 'Pole :attribute musi być dłuższe niż :value znaków.',
        'array'   => 'Pole :attribute musi zawierać więcej niż :value elementów.',
    ],
    'gte'                  => [
        'numeric' => 'Pole :attribute musi być większe lub równe :value.',
        'file'    => 'Plik :attribute musi być większy lub równy :value kilobajtów.',
        'string'  => 'Pole :attribute musi być dłuższe lub równe :value znaków.',
        'array'   => 'Pole :attribute musi zawierać :value lub więcej elementów.',
    ],
    'image'                => 'Pole :attribute musi być obrazem.',
    'in'                   => 'Wybrana wartość pola :attribute jest nieprawidłowa.',
    'integer'              => 'Pole :attribute musi być liczbą całkowitą.',
    'ip'                   => 'Pole :attribute musi być prawidłowym adresem IP.',
    'json'                 => 'Pole :attribute musi być prawidłowym ciągiem JSON.',
    'lt'                   => [
        'numeric' => 'Pole :attribute musi być mniejsze niż :value.',
        'file'    => 'Plik :attribute musi być mniejszy niż :value kilobajtów.',
        'string'  => 'Pole :attribute musi być krótsze niż :value znaków.',
        'array'   => 'Pole :attribute musi zawierać mniej niż :value elementów.',
    ],
    'lte'                  => [
        'numeric' => 'Pole :attribute musi być mniejsze lub równe :value.',
        'file'    => 'Plik :attribute musi być mniejszy lub równy :value kilobajtów.',
        'string'  => 'Pole :attribute musi być krótsze lub równe :value znaków.',
        'array'   => 'Pole :attribute nie może zawierać więcej niż :value elementów.',
    ],
    'max'                  => [
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'file'    => 'Plik :attribute nie może być większy niż :max kilobajtów.',
        'string'  => 'Pole :attribute nie może być dłuższe niż :max znaków.',
        'array'   => 'Pole :attribute nie może zawierać więcej niż :max elementów.',
    ],
    'mimes'                => 'Pole :attribute musi być plikiem typu: :values.',
    'mimetypes'            => 'Pole :attribute musi być plikiem typu: :values.',
    'min'                  => [
        'numeric' => 'Pole :attribute musi być nie mniejsze niż :min.',
        'file'    => 'Plik :attribute musi mieć przynajmniej :min kilobajtów.',
        'string'  => 'Pole :attribute musi zawierać przynajmniej :min znaków.',
        'array'   => 'Pole :attribute musi zawierać przynajmniej :min elementów.',
    ],
    'not_in'               => 'Wybrana wartość pola :attribute jest nieprawidłowa.',
    'numeric'              => 'Pole :attribute musi być liczbą.',
    'present'              => 'Pole :attribute musi być obecne.',
    'regex'                => 'Format pola :attribute jest nieprawidłowy.',
    'required'             => 'Pole :attribute jest wymagane.',
    'required_if'          => 'Pole :attribute jest wymagane, gdy :other ma wartość :value.',
    'required_with'        => 'Pole :attribute jest wymagane, gdy podano :values.',
    'required_without'     => 'Pole :attribute jest wymagane, gdy nie podano :values.',
    'same'                 => 'Pola :attribute oraz :other muszą się zgadzać.',
    'size'                 => [
        'numeric' => 'Pole :attribute musi mieć wartość :size.',
        'file'    => 'Plik :attribute musi mieć :size kilobajtów.',
        'string'  => 'Pole :attribute musi mieć :size znaków.',
        'array'   => 'Pole :attribute musi zawierać :size elementów.',
    ],
    'starts_with'          => 'Pole :attribute musi zaczynać się od jednej z wartości: :values.',
    'string'               => 'Pole :attribute musi być ciągiem znaków.',
    'unique'               => 'Wartość pola :attribute jest już zajęta.',
    'uploaded'             => 'Przesyłanie pliku :attribute nie powiodło się. Sprawdź rozmiar i spróbuj ponownie.',
    'url'                  => 'Format pola :attribute jest nieprawidłowy.',

    /*
     * Nazwy pól używane w komunikatach (aby brzmiały naturalnie po polsku).
     */
    'attributes' => [
        'name'    => 'imię i nazwisko',
        'email'   => 'adres e-mail',
        'phone'   => 'telefon',
        'message' => 'wiadomość',
        'consent' => 'zgoda',
        'website' => 'to pole',
    ],

    'custom' => [
        'website' => [
            'size' => 'Wystąpił błąd. Spróbuj ponownie.',
            'max'  => 'Wystąpił błąd. Spróbuj ponownie.',
        ],
    ],
];
