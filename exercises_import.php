<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$clientBuilder = Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([
    'http://localhost:9200/'
]);
$client = $clientBuilder->build();

$filePath = __DIR__ . '/data/sample_exercises.xlsx';

$indexName = 'mralbert_swedish_full_13';
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
foreach ($worksheet->getRowIterator(2) as $row) {

    $number = (string)$worksheet->getCellByColumnAndRow(8, $row->getRowIndex())->getValue();
    $variant = (string)$worksheet->getCellByColumnAndRow(9, $row->getRowIndex())->getValue();
    $chapterName = $worksheet->getCellByColumnAndRow(2, $row->getRowIndex())->getValue();
    $subChapterName = $worksheet->getCellByColumnAndRow(6, $row->getRowIndex())->getValue();

    $exerciseText = $worksheet->getCellByColumnAndRow(16, $row->getRowIndex())->getCalculatedValue();

    $summary_string = implode(' ', [
        'Kapitel', 'tal', 'uppgift', 'övning', $chapterName, $subChapterName, $number . $variant, $exerciseText
    ]);

    $words = str_word_count(strtolower($summary_string), 1, 'åäöáüè');
    $tags_array = array_filter(array_unique($words), function ($word) {
        return (strlen($word) > 1);
    });

    $exercises['body'][] = [
        'index' => [
            '_index' => $indexName,
            '_type' => $typeName,
            '_id' => $worksheet->getCellByColumnAndRow(13, $row->getRowIndex())->getCalculatedValue()
        ]
    ];

//    9789152302484-1-x1_1x-SF-1
//    "exerciseText": null
    $exercises['body'][] = [
//        'id' => $worksheet->getCellByColumnAndRow(13, $row->getRowIndex())->getCalculatedValue(),
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
        'exercises_suggest' => [
            'input' => $tags_array,
        ]
    ];
}

//var_dump($exercises);


$responses = $client->bulk($exercises);
//var_dump($responses);

/*
{
  "sort": {
      "_score": "desc",
      "chapterName": "asc",
      "subChapterName": "asc",
      "number": "asc",
      "variant": "asc"
    },
  "query": {
    "multi_match": {
      "query": "Kapitelen 1",
      "fields" : [ "chapterNumber", "chapterName", "subChapterName", "numberVariant", "exerciseNumberVariant*", "exerciseText" ]
    }
  }
}
*/

/*
http://localhost:9200/mralbert/exercises4/_search
{
  "sort": {
      "_score": "desc",
      "chapterName": "asc",
      "subChapterName": "asc",
      "number": "asc",
      "variant": "asc"
    },
  "query": {
    "match": {
      "_all": "15b Tal Grundkurs"
    }
  }
}
*/