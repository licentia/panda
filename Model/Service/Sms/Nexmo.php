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
class Nexmo extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Sender', 'type' => 'text', 'required' => true],
        'api_key'    => ['label' => 'API KEY', 'type' => 'password', 'required' => true],
        'password'   => ['label' => 'API SECRET', 'type' => 'password', 'required' => true],
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
            $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                    [
                        'api_key'    => $this->getApiKey(),
                        'api_secret' => $this->getPassword(),
                        'to'         => $this->getPhone($phone),
                        'from'       => $this->getOriginator(),
                        'text'       => $message,
                    ]
                );

            $this->curl->post($url, []);

            return ($this->curl->getStatus() == 200);
        } catch (\Exception $e) {
        }

        return false;
    }
}
