<?php

require_once __DIR__ . '\vendor\autoload.php';

function googleSheetSet($values, $num){
    $sheet = inSheet($num);
    if($sheet == FALSE){
        echo 'Invalid num for Sheet configuration';
        return;
    }
    $name  = $sheet['name'];
    $ssID  = $sheet['ssID'];
    $range = $sheet['range'];
    
    $valueInputOption = 'RAW';
    $params = ['valueInputOption' => $valueInputOption];
    $service = createServiceGoogle($name);
    $body = new Google_Service_Sheets_ValueRange([ 'values' => $values ]);
    $service->spreadsheets_values->append($ssID, $range, $body, $params); 
}

function googleSheetGet($num){
    $sheet = inSheet($num);
    if($sheet == FALSE){
        echo 'Invalid num for Sheet configuration';
        return;
    }
    $name  = $sheet['name'];
    $ssID  = $sheet['ssID'];
    $range = $sheet['range'];
    
    $service = createServiceGoogle($name);
    $response = $service->spreadsheets_values->get($ssID, $range);
    $values = $response->getValues();
    return $values;
}

function createServiceGoogle($name){
    $client = new \Google_Client();
    $client->setApplicationName($name);
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    $client->setAuthConfig(__DIR__.'/OlehM-8b90c40768b8.json');
    $client->setAccessToken(__DIR__.'/token.json');
    $service = new Google_Service_Sheets($client);
    return $service;
}
