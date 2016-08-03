<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$filePath = __DIR__ . '/data/sample1.xlsx';
//$filePath = __DIR__ . '/data/160623-PRODUCTION-Matte Direkt 9.xlsx';

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
    $exercises[] = [
        'id' => $worksheet->getCellByColumnAndRow(13, $row->getRowIndex())->getOldCalculatedValue(),
        'chapterName' => $worksheet->getCellByColumnAndRow(2, $row->getRowIndex())->getValue(),
        'subChapterName' => $worksheet->getCellByColumnAndRow(6, $row->getRowIndex())->getValue(),
//        'difficultyLevel' => $worksheet->getCellByColumnAndRow(10, $row->getRowIndex())->getValue(),
        'number' => (int)$worksheet->getCellByColumnAndRow(8, $row->getRowIndex())->getValue(),
        'albertDifficultyLevel' => $worksheet->getCellByColumnAndRow(11, $row->getRowIndex())->getValue(),
        'numberAndVariant' => $worksheet->getCellByColumnAndRow(12, $row->getRowIndex())->getOldCalculatedValue(),
//        'globalId' => $worksheet->getCellByColumnAndRow(13, $row->getRowIndex())->getValue(),
        'exerciseText' => $worksheet->getCellByColumnAndRow(16, $row->getRowIndex())->getOldCalculatedValue(),
//        'lessonId' => $worksheet->getCellByColumnAndRow(18, $row->getRowIndex())->getValue(),
//        'lessonName' => $worksheet->getCellByColumnAndRow(20, $row->getRowIndex())->getOldCalculatedValue(),
        'lesson' => [
            'id' => $worksheet->getCellByColumnAndRow(18, $row->getRowIndex())->getValue(),
            'name' => $worksheet->getCellByColumnAndRow(20, $row->getRowIndex())->getOldCalculatedValue(),
        ],

    ];
}

var_dump($exercises);