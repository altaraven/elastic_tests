<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$clientBuilder = Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([
    'http://localhost:9200/'
]);
$client = $clientBuilder->build();

$filePath = __DIR__ . '/data/160404-Lessons upload.xlsx';

$indexName = 'mralbert_swedish_full_14';
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
foreach ($worksheet->getRowIterator(2) as $row) {

    $chapterName = $worksheet->getCellByColumnAndRow(1, $row->getRowIndex())->getCalculatedValue();
    $subChapterName = $worksheet->getCellByColumnAndRow(2, $row->getRowIndex())->getCalculatedValue();
    $lessonId = $worksheet->getCellByColumnAndRow(0, $row->getRowIndex())->getValue();
    $lessonName = $worksheet->getCellByColumnAndRow(5, $row->getRowIndex())->getCalculatedValue();

    $summary_string = implode(' ', [$chapterName, $subChapterName, $lessonId, $lessonName]);

    $words = str_word_count(strtolower($summary_string), 1, 'åäöáüè');
    $tags_array = array_filter(array_unique($words), function ($word) {
        return (strlen($word) > 1);
    });

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
        'chapterName' => $chapterName,
        'subChapterName' => $subChapterName,
        'lessons_suggest' => [
            'input' => $tags_array,
        ]
    ];
}

$responses = $client->bulk($data);