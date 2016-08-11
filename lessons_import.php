<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$clientBuilder = Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([
    'http://localhost:9200/'
]);
$client = $clientBuilder->build();

$filePath = __DIR__ . '/data/160404-Lessons upload.xlsx';

$indexName = 'mralbert';
$typeName = 'lessons';

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

$data = [];
$suggest_words = [];
foreach ($worksheet->getRowIterator(2) as $row) {

    $centralArea = $worksheet->getCellByColumnAndRow(1, $row->getRowIndex())->getCalculatedValue();
    $mainArea = $worksheet->getCellByColumnAndRow(2, $row->getRowIndex())->getCalculatedValue();
    $lessonId = $worksheet->getCellByColumnAndRow(0, $row->getRowIndex())->getValue();
    $lessonName = $worksheet->getCellByColumnAndRow(5, $row->getRowIndex())->getCalculatedValue();
    $keywords = $worksheet->getCellByColumnAndRow(6, $row->getRowIndex())->getCalculatedValue();

    /**/
    $summary_string = implode(' ', [$centralArea, $mainArea, $lessonId, $lessonName, $keywords]);

    $words = str_word_count(strtolower($summary_string), 1, 'åäöáüè');
    $tags_array = array_filter($words, function ($word) {
        return (strlen($word) > 1);
    });

    $suggest_words = array_merge($suggest_words, $tags_array);
    /**/

    $data['body'][] = [
        'index' => [
            '_index' => $indexName,
            '_type' => $typeName,
            '_id' => $lessonId
        ]
    ];

    $data['body'][] = [
        'lessonId' => $lessonId,
        'lessonName' => $lessonName,
        'centralArea' => $centralArea,
        'mainArea' => $mainArea,
        'keywords' => $keywords,
    ];
}

$suggest_words = array_unique($suggest_words);
$lessons_suggest = [];
foreach ($suggest_words as $word) {
    $lessons_suggest['body'][] = [
        'index' => [
            '_index' => $indexName,
            '_type' => $typeName . '_suggest',
        ]
    ];

    $lessons_suggest['body'][] = [
        'word' => $word,
        'lessons_suggest' => [
            'input' => $word,
        ]
    ];
}

$responses = $client->bulk($data);
$responses2 = $client->bulk($lessons_suggest);