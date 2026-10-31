<?php

return [

    /*
    | Auto pokazywane na stronie „O nas” (prezentacja 360° i przykład CertiCheck).
    | Gdy auta o tym slugu nie ma albo nie jest aktywne, strona sama wybiera
    | pierwsze aktywne auto z klatkami 360° z zewnątrz (patrz PageController@about).
    */
    'about_showcase_car' => env('ABOUT_SHOWCASE_CAR', 'bmw-seria-216-d-active-tourer'),

    // Podpis tego auta na stronie „O nas” (jak w projekcie). Inne auta: marka + model z bazy.
    'about_showcase_label' => env('ABOUT_SHOWCASE_LABEL', 'BMW 216d'),

];
