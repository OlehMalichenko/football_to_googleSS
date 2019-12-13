<?php
ini_set('max_execution_time', 100000);
header("Content-Type: text/plain; charset=utf-8");
require_once __DIR__ . '\vendor\autoload.php';
require_once 'curl.php';
require_once 'googleClient.php';

//-------------CallMainFunction---------------//
main();
//--------------------------------------------//



function main(){
    $arrData = getPlayerID();
    getValueData($arrData);
}



function inSheet($num){
  $dictSheet = [
      0 => [
          'name'  =>'Players_Football-observatory',
          'ssID'  =>'1AXpxu0npcAotoPgs7vgLMA4B4hsT3IRRAW1Gjb2zENM',
          'range' =>'Sheet1'
      ],
      1 => [
          'name'  =>'Players_Football-observatory',
          'ssID'  =>'1AXpxu0npcAotoPgs7vgLMA4B4hsT3IRRAW1Gjb2zENM',
          'range' =>'Sheet1!A2:A'
      ],
      2 => [
          'name'  =>'Players_Football-observatory',
          'ssID'  =>'1GiEnlK3Xyn4siRXES9qD5vwjcnYuDybg4pE0C4bERMI',
          'range' =>'Sheet1'
      ],
      3 => [
          'name'  =>'Players_Football-observatory',
          'ssID'  =>'1AXpxu0npcAotoPgs7vgLMA4B4hsT3IRRAW1Gjb2zENM',
          'range' =>'Sheet1!A2:E'
      ]
  ];
  $index = count($dictSheet) - 1;
  if($num > $index){
    return FALSE;
  }
  return $dictSheet[$num];
}



function getPlayerID(){
    $values = [];
    $url = 'https://football-observatory.com/-rankings-_none';
    $baseUrl = 'https://football-observatory.com/-values-';
    $html = curlstart($url);
    $htmlDoc = phpQuery::newDocument($html);
    $rowBlocks = $htmlDoc->find('.content_once');
    foreach ( $rowBlocks as $val ){
        $block = pq($val);
        $name = $block->find('.name')->text();
        $value = $block->find('.value')->texts()[1];
        $club = str_replace(['(', ')'], '', $block->find('.club')->text());
        $id = str_replace('_scroll', '', $block->children(0)->attr('id'));
        $arr = [$baseUrl.$id, $id, $name, $value, $club];
        $values[] = $arr;
    }
    googleSheetSet($values,0);
    return $values;
}



function getValueData($arrData){
    //$arrData = googleSheetGet(3);
    $arr = [];
    foreach ($arrData as $arrEl){
        $url = $arrEl[0];
        $id = $arrEl[1];
        $html = curlstart($url);
        $htmlDoc = phpQuery::newDocument($html);
        $hasData = $htmlDoc->find('#'.$id.'')->text();
        if($hasData == ''){
            $arrEl[0] = 'No Value Data';
            $arr[] = $arrEl;
            echo $arrEl[0].'  '.$arrEl[2];
            continue;
        }       
        $arr[] = getDataFromValues($arrEl,$htmlDoc);
        echo $arrEl[0].'  '.$arrEl[2];
    } 
//    var_dump($arr);
    googleSheetSet($arr, 2);
}



function getDataFromValues($arrEl, $htmlDoc){
    $arrC[] = trim($arrEl[0]);                                                                // 0 url
    $arrC[] = trim($arrEl[1]);                                                                // 1 id
    $arrC[] = trim($arrEl[2]);                                                                // 2 name
    $arrC[] = trim($arrEl[4]);                                                                // 3 club
    $arrC[] = trim(str_replace('years', '', $htmlDoc->find('.age')->text()));                 // 4 age
    $arrC[] = trim(str_replace('Contract until','',$htmlDoc->find('.end_contrat')->text()));  // 5 end contract
    $arrC[] = trim($htmlDoc->find('.money-info')->children(0)->getString()[0]);               // 6 cost
    $arrC[] = trim($arrEl[3]);                                                                // 7 rating       
    $arrC[] = trim($htmlDoc->find('#sample_goal1')->attr('data'));                            // 8 user rating
    return $arrC;
}