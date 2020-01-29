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
 * Class Sinch
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Chikka extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'username' => ['label' => 'Access code', 'type' => 'text', 'required' => true,],
        'password' => ['label' => 'Public key used for authentication', 'type' => 'password', 'required' => true,],
        'api_key'  => ['label' => 'Private key used for authentication', 'type' => 'password', 'required' => true,],
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
            $arr_post_body = [
                "message_type"  => "SEND",
                "mobile_number" => $this->getPhone($phone),
                "shortcode"     => $this->getUsername(),
                "message_id"    => sha1(microtime() . $phone . random_int(0, 973524)),
                "message"       => urlencode($message),
                "client_id"     => $this->getPassword(),
                "secret_key"    => $this->getApiKey(),
            ];

            $query_string = "";
            foreach ($arr_post_body as $key => $frow) {
                $query_string .= '&' . $key . '=' . $frow;
            }

            $url = "https://post.chikka.com/smsapi/request";

            $this->curl->post($url, $query_string);

            return ($this->curl->getStatus() == 200);
        } catch (\Exception $e) {
        }

        return false;
    }
}
