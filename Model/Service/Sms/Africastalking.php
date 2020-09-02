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
 * Class Africastalking
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Africastalking extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'api_key'  => ['label' => 'API key', 'type' => 'password', 'required' => true,],
        'username' => ['label' => 'Your Username', 'type' => 'text', 'required' => true,],
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
            $url = "https://api.africastalking.com/restless/send?username={$this->getUsername()}&" .
                   "Apikey={$this->getApiKey()}&to=+{$this->getPhone($phone)}&message=" . rawurlencode($message);

            $this->curl->post($url, []);

            return ($this->curl->getStatus() == 200);
        } catch (\Exception $e) {
        }

        return false;
    }
}
