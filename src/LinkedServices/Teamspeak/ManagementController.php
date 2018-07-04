<?php

use GW2Integration\LinkedServices\Teamspeak\Teamspeak;
use function GuzzleHttp\json_encode;

/* 
 * The MIT License
 *
 * Copyright 2016 Jeppe Boysen Vennekilde.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once __DIR__ . "/../../Admin/RestrictAdminPanel.php";

$form = filter_input(INPUT_POST, 'form');
$formData = array();
$result = array();
foreach($_POST AS $key => $value){
    $formData[$key] = filter_input(INPUT_POST, $key);
}
switch($form){
    case "soft-restart-ts":
        try{
            Teamspeak::sendRESTCommand(array("srs" => null));
            $result["status"] = "Sent soft restart command to teamspeak server";
        } catch(Exception $e){
            $result["status"] = $e->getMessage();
        }
        break;
      
        
    default:
        $result = $_POST;
        break;
}

echo json_encode(array(
    "data" => $result
));