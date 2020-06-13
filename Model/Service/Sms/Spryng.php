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
 * Class Spryng
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Spryng extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Sender', 'type' => 'text', 'required' => true, 'note' => 'Originator address'],
        'username'   => ['label' => 'Username', 'type' => 'text', 'required' => true],
        'password'   => ['label' => 'Password', 'type' => 'password', 'required' => true],
        'api_key'    => [
            'label'    => 'Route',
            'type'     => 'text',
            'required' => true,
            'note'     => 'To select the Spryng Business, Spryng Economy or Specific User route.',
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

        $url = 'https://api.spryngsms.com/api/send.php' .
               '/SyncTextService' .
               '?OPERATION=send' .
               '&USERNAME=' . $this->getUsername() .
               '&PASSWORD=' . $this->getPassword() .
               '&DESTINATION=' . ltrim($this->getPhone($phone), 0) .
               '&SENDER=' . $this->getOriginator() .
               '&BODY=' . urlencode($message) .
               '&ROUTE=' . $this->getApiKey();

        $this->curl->get($url);

        return ($this->curl->getStatus() == 200);

    }
}
