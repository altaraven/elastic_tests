<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/../vendor/autoload.php');

$clientBuilder = Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([
    'http://localhost:9200/'
]);
$client = $clientBuilder->build();

$filePath = __DIR__ . '/../data/sample_exercises.xlsx';

$indexName = 'mralbert_ngram_10';
$typeName = 'exercises';

try {
    $reader = new PHPExcel_Reader_Excel2007();
    if ($reader->canRead($filePath) !== true) {
        echo "Invalid xlsx file.";
        exit();
    }

    $reader->setReadDataOnly(true);

    $excel = $reader->load($filePath);
} catch (PHPExcel_Reader_Exception $e) {
    echo "Error loading file" . $e->getMessage();
    exit();
} catch (Exception $e) {
    echo "Exception, error loading file 2:" . $e->getMessage();
    exit();
}

$worksheet = $excel->setActiveSheetIndex(0);

$exercises = [];
$suggest_words = [];
foreach ($worksheet->getRowIterator(2) as $row) {

    $isbn = (string)$worksheet->getCellByColumnAndRow(0, $row->getRowIndex())->getValue();
    $_id = $worksheet->getCellByColumnAndRow(13, $row->getRowIndex())->getCalculatedValue();
    $number = (string)$worksheet->getCellByColumnAndRow(8, $row->getRowIndex())->getValue();
    $variant = (string)$worksheet->getCellByColumnAndRow(9, $row->getRowIndex())->getValue();
    $chapterName = $worksheet->getCellByColumnAndRow(2, $row->getRowIndex())->getValue();
    $subChapterName = $worksheet->getCellByColumnAndRow(6, $row->getRowIndex())->getValue();

    $exerciseText = $worksheet->getCellByColumnAndRow(16, $row->getRowIndex())->getCalculatedValue();

//    $summary_string = implode(' ', [
//        'Kapitel', 'tal', 'uppgift', 'övning', $chapterName, $subChapterName, $number . $variant, $exerciseText
//    ]);

//    $words = str_word_count(strtolower($summary_string), 1, 'åäöáüè');
//    $tags_array = array_filter($words, function ($word) {
//        return (strlen($word) > 1);
//    });

//    $suggest_words = array_merge($suggest_words, $tags_array);

    $exercises['body'][] = [
        'index' => [
            '_index' => $indexName,
            '_type' => $typeName,
            '_id' => $_id
        ]
    ];

//    9789152302484-3-x3_1x-G-11c
    $exercises['body'][] = [
        'isbn' => $isbn,
        'chapterNumber' => 'Kapitel ' . $worksheet->getCellByColumnAndRow(1, $row->getRowIndex())->getValue(),
        'chapterName' => $chapterName,
        'subChapterName' => $subChapterName,
        'number' => $number,
        'variant' => $variant,
        'numberVariant' => $number . $variant,
        'exerciseNumberVariant1' => $variant ? 'tal ' . $number . ' ' . $variant : 'tal ' . $number,
        'exerciseNumberVariant2' => $variant ? 'uppgift ' . $number . ' ' . $variant : 'uppgift ' . $number,
        'exerciseNumberVariant3' => $variant ? 'övning ' . $number . ' ' . $variant : 'övning ' . $number,
        'exerciseText' => $exerciseText,
        'numVarChaptSubChapt' => $number . $variant . ' ' . $chapterName . ' ' . $subChapterName,
        'lessonId' => $worksheet->getCellByColumnAndRow(18, $row->getRowIndex())->getValue(),
        'lessonName' => $worksheet->getCellByColumnAndRow(20, $row->getRowIndex())->getCalculatedValue(),
    ];
}
//var_dump(count($suggest_words));
//$suggest_words = array_unique($suggest_words);
//$exercises_suggest = [];
//foreach ($suggest_words as $word) {
//    $exercises_suggest['body'][] = [
//        'index' => [
//            '_index' => $indexName,
//            '_type' => $typeName . '_suggest',
//        ]
//    ];
//
//    $exercises_suggest['body'][] = [
//        'word' => $word,
//        'exercises_suggest' => [
//            'input' => $word,
//        ]
//    ];
//}

$responses = $client->bulk($exercises);
//$responses2 = $client->bulk($exercises_suggest);