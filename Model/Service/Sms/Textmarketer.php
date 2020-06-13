<?php

/**
 * Copyright (C) Licentia, Unipessoal LDA
 *
 * NOTICE OF LICENSE
 *
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  https://www.greenflyingpanda.com/panda-license.txt
 *
 *  @title      Licentia Panda - Magento® Sales Automation Extension
 *  @package    Licentia
 *  @author     Bento Vilas Boas <bento@licentia.pt>
 *  @copyright  Copyright (c) Licentia - https://licentia.pt
 *  @license    https://www.greenflyingpanda.com/panda-license.txt
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
