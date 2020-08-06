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
 * Class Msg91
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Msg91 extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Originator', 'type' => 'text', 'required' => true],
        'api_key'    => ['label' => 'API KEY', 'type' => 'password', 'required' => true],
    ];

    /**
     * @param $phone
     * @param $message
     *
     * @return bool
     */
    public function sendSMS($phone, $message)
    {

        $authKey = $this->getApiKey();
        $mobileNumber = $this->getPhone($phone);
        $senderId = $this->getOriginator();

        $message = urlencode($message);

        $route = "default";
        $postData = [
            'authkey' => $authKey,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender'  => $senderId,
            'route'   => $route,
        ];

        $url = "https://control.msg91.com/api/sendhttp.php";

        $this->curl->post($url, $postData);

        return ($this->curl->getStatus() == 200);
    }
}
