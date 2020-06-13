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
