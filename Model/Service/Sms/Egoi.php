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
 * Class Egoi
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Egoi extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'FROM ID', 'type' => 'text', 'required' => true, 'note' => 'Sender Hash'],
        'api_key'    => ['label' => 'API KEY', 'type' => 'password', 'required' => true],
    ];

    /**
     * @param $phone
     * @param $message
     *
     * @return bool
     * @throws \Exception
     */
    public function sendSMS($phone, $message)
    {

        $url = 'https://www51.e-goi.com/api/public/sms/send';

        $data = [
            "apikey"     => $this->getApiKey(),
            "mobile"     => $this->getPhone($phone, true),
            "senderHash" => $this->getOriginator(),
            "message"    => $message,
        ];

        $data = \Zend_Json::encode($data);

        $this->curl->addHeader('Content-Type', ' application/json');
        $this->curl->post($url, $data);

        return ($this->curl->getStatus() == 200);
    }
}
