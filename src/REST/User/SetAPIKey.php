<?php

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

namespace GW2Integration\REST\User;

/**
 * Description of SetAPIKey
 *
 * @author Jeppe Boysen Vennekilde
 */

require __DIR__.'/../RESTHelper.php';

use Exception;
use GW2Integration\API\APIKeyManager;
use GW2Integration\Exceptions\InvalidAPIKeyNameException;
use GW2Integration\Exceptions\MissingRequiredAPIKeyPermissions;
use GW2Integration\Exceptions\UnableToDetermineLinkId;
use GW2Integration\Persistence\Helper\GW2DataPersistence;
use GW2Integration\REST\RESTHelper;
use function GuzzleHttp\json_encode;

$linkedUser = RESTHelper::getLinkedUserFromParams();

$apiKey = htmlspecialchars(filter_input(INPUT_POST, 'api-key'));
//Attempt to add API Key
try {
    APIKeyManager::addAPIKeyForUser($linkedUser, $apiKey);
    
    //Respond with newly persisted account data
    $accountData = GW2DataPersistence::getExtensiveAccountData($linkedUser);
    echo json_encode($accountData);
    
} catch (Exception $e) {
    global $logger;
    //Exception handling
    if ($e instanceof InvalidAPIKeyNameException) {
        http_response_code(417); //invalid api keyname
        $logger->info('SetAPIKey Exception: InvalidAPIKeyNameException: '.$e->getMessage());
        echo $e->getMessage();
        
    } else if ($e instanceof MissingRequiredAPIKeyPermissions) {
        http_response_code(401); //Unauthorized
        $permissions = $e->getPermissions();
        $logger->info("SetAPIKey Exception: MissingRequiredAPIKeyPermissions",$permissions, $linkedUser);
        echo '["' . implode('","', $permissions) . '"]';
    } else if($e instanceof UnableToDetermineLinkId){
        $logger->info('SetAPIKey Exception: UnableToDetermineLinkId: '.$linkedUser, $e->getTrace());
        echo "false";
    } else {
        http_response_code(406); //Not acceptable
        //Could not add API key for what ever reason
        $logger->info('SetAPIKey Exception: '.$e->getMessage(), $e->getTrace());
        echo $e->getMessage();
    }
    exit(0);
}

http_response_code(202); //Accepted
//End response early, no reason to send rest
exit(0);