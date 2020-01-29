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
 * Class Textmarketer
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Textmarketer extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'username' => ['label' => 'Username', 'type' => 'text', 'required' => true],
        'password' => ['label' => 'Password', 'type' => 'password', 'required' => true],
    ];

    /**
     * @param $phone
     * @param $message
     *
     * @return string
     */
    public function sendSMS($phone, $message)
    {

        $textmarketer_url = "https://api.textmarketer.co.uk/gateway/";

        $URL = $textmarketer_url . "?username=" . $this->getUsername() . "&password=" . $this->getPassword()
               . "&option=xml&to=" . $this->getPhone($phone) . "&message=" . urlencode($message) . "&orig="
               . urlencode('SERVERALERT');

        $fp = fopen($URL, 'r');
        $result = fread($fp, 1024);
        if (stripos($result, '<error') !== false) {
            return false;
        }

        return true;
    }
}
