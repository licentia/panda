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
