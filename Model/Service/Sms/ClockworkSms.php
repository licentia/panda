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
 * Class ClockworkSms
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class ClockworkSms extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'api_key'    => ['label' => 'Your API Key', 'type' => 'password', 'required' => true,],
        'originator' => ['label' => 'From', 'type' => 'text', 'required' => true,],
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
            $url = "https://api.clockworksms.com/http/send.aspx?";

            $URL = $url . "key=" . $this->getApiKey()
                   . "&to=" . $this->getPhone($phone)
                   . "&content=" . urlencode($message)
                   . "&from=" . $this->getOriginator();

            $fp = fopen($URL, 'r');
            $result = fread($fp, 1024);
            if (stripos($result, 'Invalid') !== false) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
        }

        return false;
    }
}
