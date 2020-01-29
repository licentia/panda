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
 * Class Mosms
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Mosms extends Core
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
     * @return string
     */
    public function sendSMS($phone, $message)
    {

        $url = "http://www.mosms.com/se/sms-send.php";

        $result = file_get_contents(
            $url . "?username=" . $this->getUsername()
            . "&password=" . $this->getPassword() . "&nr=" . $this->getPhone($phone) . "&type=text&customsender="
            . $this->getOriginator() . "&allowlong=1&data=" . rawurlencode($message)
        );

        if ($result <> "0") {
            return false;
        } else {
            return true;
        }
    }
}
