<?php
function icon($name){
 $paths=[
    'home' => 'm3 12 9-8 9 8M5 11v10h5v-7h4v7h5V11',
    'pin' => 'M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0z M14 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z',
    'coin' => 'M3 6h18v13H3z M3 10h18m-6 5h3',
    'area' => 'M3 9V3h6m6 0h6v6m0 6v6h-6m-6 0H3v-6',
    'search' => 'M16 16l5 5 M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0z',
    'arrow' => 'M4 12h15m-6-6 6 6-6 6',
    'chev' => 'm7 10 5 5 5-5',
    'door' => 'M10 4h10v16H10M2 12h13m-5-5 5 5-5 5',
    'user' => 'M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z M3 21a9 9 0 0 1 18 0',
    'plus' => 'M12 4v16M4 12h16',
];
 return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="'.$paths[$name].'"/></svg>';
}
