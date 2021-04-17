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
 * Class Txtlocal
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Txtlocal extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => [
            'label'    => 'Sender',
            'type'     => 'text',
            'required' => true,
        ],
        'username'   => [
            'label'    => 'Username',
            'type'     => 'text',
            'required' => true,
            'note'     => 'Your Email',
        ],
        'api_key'    => [
            'label'    => 'Hash',
            'type'     => 'password',
            'required' => true,
            'note'     => 'Your API hash',
        ],
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
            $data = [
                'username' => $this->getUsername(),
                'hash'     => $this->getApiKey(),
                'numbers'  => $this->getPhone($phone),
                "sender"   => urlencode($this->getOriginator()),
                "message"  => rawurlencode($message),
            ];

            $this->curl->post('http://api.txtlocal.com/send/', $data);

            return ($this->curl->getStatus() == 200);
        } catch (\Exception $e) {
        }

        return false;
    }
}
