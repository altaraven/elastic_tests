<?php

//$str = 'Vad ska stå i stället för rutan? 50 cl $= \\, \\textup{\\Square} \\,$ liter';
$str = strtolower('Välj rätt enhet i metersystemet. Ett badkar kan rymma $150 \\, \\textup{\\Square}.$');

$words = str_word_count($str, 1, 'åäöáüè');

$newarray = array_filter($words, function($var) {
    return (strlen($var) > 1);
});

var_dump($words);
var_dump($newarray);
