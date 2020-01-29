<?php

/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Panda\Model\Service\Sms;

/**
 * Class Smsglobal
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Smsglobal extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Originator', 'type' => 'text', 'required' => true],
        'username'   => ['label' => 'Username', 'type' => 'text', 'required' => true],
        'password'   => ['label' => 'Password', 'type' => 'password', 'required' => true],
    ];

    /**
     * @param $phone
     * @param $message
     *
     * @return bool
     */
    public function sendSMS($phone, $message)
    {

        $from = urlencode(substr($this->getOriginator(), 0, 11));

        $url = 'http://www.smsglobal.com/http-api.php' .
               '?action=sendsms' .
               '&user=' . $this->getUsername() .
               '&password=' . $this->getPassword() .
               '&from=' . $from .
               '&to=' . $this->getPhone($phone) .
               '&clientcharset=ISO-8859-1' .
               '&text=' . substr(rawurlencode($message), 0, 153);

        $returnedData = file_get_contents($url);

        return strpos($returnedData, 'OK: 0') !== false;
    }
}
