<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$clientBuilder = Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([
    'http://localhost:9200/'
]);
$client = $clientBuilder->build();

$filePath = __DIR__ . '/data/sample2.xlsx';
//$filePath = __DIR__ . '/data/160623-PRODUCTION-Matte Direkt 9.xlsx';

$indexName = 'mralbert_swedish_full_7';
$typeName = 'exercises';

try {
    $reader = new PHPExcel_Reader_Excel2007();

    // http://stackoverflow.com/questions/13626678/phpexcel-how-to-
    // check-whether-a-xls-file-is-valid-or-not
    //
    if ($reader->canRead($filePath) !== true) {
        echo "Invalid xlsx file.";
        exit();
    }

    /*
           If you're only interested in the cell values in a workbook,
           but don't need any of the cell formatting information,
           then you can set the reader to read only the data values
           and any formulas from each cell using the setReadDataOnly() method.

           It is important to note that Workbooks (and PHPExcel) store dates and
           times as simple numeric values: they can only be distinguished from
           other numeric values by the format mask that is applied to that cell.
           When setting read data only to true, PHPExcel doesn't read the cell
           format masks, so it is not possible to differentiate between dates/times
           and numbers.
     */
    $reader->setReadDataOnly(true);

    $excel = $reader->load($filePath);
} catch (PHPExcel_Reader_Exception $e) {
    echo "Error loading file" . $e->getMessage();
    exit();
} catch (Exception $e) {
    echo "Exception, error loading file 2:" . $e->getMessage();
    exit();
}

// set worksheet

$worksheet = $excel->setActiveSheetIndex(0);

$exercises = [];
foreach ($worksheet->getRowIterator(2) as $row) {

    $number = (string)$worksheet->getCellByColumnAndRow(8, $row->getRowIndex())->getValue();
    $variant = (string)$worksheet->getCellByColumnAndRow(9, $row->getRowIndex())->getValue();
    $chapterName = $worksheet->getCellByColumnAndRow(2, $row->getRowIndex())->getValue();
    $subChapterName = $worksheet->getCellByColumnAndRow(6, $row->getRowIndex())->getValue();

    $exerciseText = $worksheet->getCellByColumnAndRow(16, $row->getRowIndex())->getCalculatedValue();

    $exercise_words = str_word_count(strtolower($exerciseText), 1, 'åäöáüè');
    $exercise_words_array = array_filter($exercise_words, function($word) {
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
        'chapterNumber' => 'Kapitel ' .  $worksheet->getCellByColumnAndRow(1, $row->getRowIndex())->getValue(),
        'chapterName' => $chapterName,
        'subChapterName' => $subChapterName,
        'number' => $number,
        'variant' => $variant,
        'numberVariant' => $number . $variant,
        'exerciseNumberVariant1' => $variant ? 'tal ' .  $number . ' ' . $variant : 'tal ' .  $number,
        'exerciseNumberVariant2' => $variant ? 'uppgift ' .  $number . ' ' . $variant : 'uppgift ' .  $number,
        'exerciseNumberVariant3' => $variant ? 'övning ' .  $number . ' ' . $variant : 'övning ' .  $number,
        'exerciseText' => $exerciseText,
        'numVarChaptSubChapt' => $number . $variant . ' ' . $chapterName . ' ' . $subChapterName,
        'lessonId' => $worksheet->getCellByColumnAndRow(18, $row->getRowIndex())->getValue(),
        'lessonName' => $worksheet->getCellByColumnAndRow(20, $row->getRowIndex())->getCalculatedValue(),
        'suggest' => [
            'input' => array_merge([
                'Kapitel',
                'tal',
                'uppgift',
                'övning',
                $chapterName,
                $subChapterName,
                $number . $variant,
            ], $exercise_words_array),
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