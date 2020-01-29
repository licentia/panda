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
 * Class Clickatell
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class SmsGateway extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Device', 'type' => 'text', 'required' => true],
        'username'   => ['label' => 'Email', 'type' => 'text', 'required' => true,],
        'password'   => ['label' => 'Password', 'type' => 'password', 'required' => true,],
    ];

    /**
     * @param $phone
     * @param $message
     *
     * @return mixed
     */
    public function sendSMS($phone, $message)
    {

        try {
            $fields = [
                'number'  => '+' . $this->getPhone($phone),
                'message' => $message,
                'device'  => $this->getOriginator(),
            ];

            $fields['email'] = $this->getUsername();
            $fields['password'] = $this->getPassword();

            $url = "https://smsgateway.me/api/v3/messages/send";

            $this->curl->post($url, $fields);

            return ($this->curl->getStatus() == 200);
        } catch (\Exception $e) {
        }

        return false;
    }
}
