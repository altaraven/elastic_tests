<?php
date_default_timezone_set( 'Europe/Kiev' );

require(__DIR__ . '/vendor/autoload.php');

use \GuzzleHttp\Client;

//$client = new Client();
//$res = $client->request('GET', 'https://api.github.com/user', [
//    'auth' => ['despected@gmail.com', 'Wareb0s5']
//]);
//
//var_dump($res);

//getSheetByName($pName = '')

//phpinfo();

//$objPHPExcel = PHPExcel_IOFactory::load(__DIR__ . '/data/160623-PRODUCTION-Matte Direkt 9.xlsx');
$objPHPExcel = PHPExcel_IOFactory::load(__DIR__ . '/data/sample1.xlsx');
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,false,true,false);
var_dump($sheetData);