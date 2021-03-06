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

namespace GW2Integration\Persistence\Helper;

use GW2Integration\Entity\LinkedUser;
use GW2Integration\Persistence\Persistence;
use GW2Integration\Utils\GW2DataFieldConverter;
use PDO;

if (!defined('GW2Integration')) {
    die('Hacking attempt...');
}
/**
 * Description of StatisticsPersistenceHelpter
 *
 * @author Jeppe Boysen Vennekilde
 */
class AccountPersistenceHelper {
    
    /**
     * 
     * @param LinkedUser $linkedUser
     */
    public static function getAccountData($linkedUser) {
        $userIdentification = LinkingPersistencyHelper::getUserIdColumnAndValue($linkedUser);
        
        if($userIdentification === null){
            return false;
        }
        $preparedQueryString = 'SELECT * FROM gw2_accounts INNER JOIN gw2_api_keys ON gw2_accounts.link_id = gw2_api_keys.link_id '.$userIdentification[0].' LIMIT 1';

        $queryParams = (array) $userIdentification[1];
        
        $preparedStatement = Persistence::getDBEngine()->prepare($preparedQueryString);

        $preparedStatement->execute($queryParams);
        $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);
        
        return $result;
    }
    
    /**
     * 
     * @param LinkedUser $linkedUser
     */
    public static function getAccountAndAPIKeyData($linkedUser) {
        $userIdentification = LinkingPersistencyHelper::getUserIdColumnAndValue($linkedUser);
        if($userIdentification === null){
            return false;
        }
        
        $preparedQueryString = 
                'SELECT gw2_accounts.*, gw2_api_keys.*, gw2_banned_accounts.ban_id, gw2_banned_accounts.reason, gw2_banned_accounts.banned_by, gw2_banned_accounts.timestamp '
                . 'FROM gw2_accounts '
                . 'INNER JOIN gw2_api_keys ON gw2_accounts.link_id = gw2_api_keys.link_id '
                . 'LEFT JOIN gw2_banned_accounts ON UPPER(gw2_accounts.username) = UPPER(gw2_banned_accounts.username)'
                .$userIdentification[0].' LIMIT 1';
        $queryParams = (array) $userIdentification[1];
        
        $preparedStatement = Persistence::getDBEngine()->prepare($preparedQueryString);

        $preparedStatement->execute($queryParams);
        $result =  $preparedStatement->fetch(PDO::FETCH_ASSOC);
        
        return $result;
    }
    
}
