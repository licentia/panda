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
 * Class Spryng
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Spryng extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Sender', 'type' => 'text', 'required' => true, 'note' => 'Originator address'],
        'username'   => ['label' => 'Username', 'type' => 'text', 'required' => true],
        'password'   => ['label' => 'Password', 'type' => 'password', 'required' => true],
        'api_key'    => [
            'label'    => 'Route',
            'type'     => 'text',
            'required' => true,
            'note'     => 'To select the Spryng Business, Spryng Economy or Specific User route.',
        ],
    ];

    /**
     * @param $phone
     * @param $message
     *
     * @return mixed
     */
    public function sendSMS($phone, $message)
    {

        $url = 'https://api.spryngsms.com/api/send.php' .
               '/SyncTextService' .
               '?OPERATION=send' .
               '&USERNAME=' . $this->getUsername() .
               '&PASSWORD=' . $this->getPassword() .
               '&DESTINATION=' . ltrim($this->getPhone($phone), 0) .
               '&SENDER=' . $this->getOriginator() .
               '&BODY=' . urlencode($message) .
               '&ROUTE=' . $this->getApiKey();

        $this->curl->get($url);

        return ($this->curl->getStatus() == 200);

    }
}
