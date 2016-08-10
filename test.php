<?php

//$str = 'Vad ska stå i stället för rutan? 50 cl $= \\, \\textup{\\Square} \\,$ liter';
//$str = strtolower('Välj rätt enhet i metersystemet. Ett badkar kan rymma $150 \\, \\textup{\\Square}.$');
$str = strtolower('En rätvinklig triangel har omkretsen 14 cm. En av kateterna är 6 cm. Hur långa är de övriga sidorna i triangeln?');

$words = str_word_count($str, 1, 'åäöáüè');

$newarray = array_filter($words, function($var) {
    return (strlen($var) > 1);
});

var_dump($words);
var_dump($newarray);
