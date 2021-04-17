<?php

/*
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
 * Class Semysms
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Semysms extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Device Code', 'type' => 'text', 'required' => true],
        'api_key'    => ['label' => 'Your Token', 'type' => 'password', 'required' => true,],
        'password'   => ['label' => 'Your App Secret', 'type' => 'password', 'required' => true,],
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
            $url = "https://semysms.net/api/3/sms.php";
            $phone = '+' . $this->getPhone($phone);

            $data = [
                "phone"  => $phone,
                "msg"    => $message,
                "device" => $this->getOriginator(),
                "token"  => $this->getApiKey(),
            ];

            $this->curl->post($url, $data);

            return ($this->curl->getStatus() == 200);
        } catch (\Exception $e) {
        }

        return false;
    }
}
