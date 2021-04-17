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
 * Class Smsglobal
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Smsglobal extends Core
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
     * @return bool
     */
    public function sendSMS($phone, $message)
    {

        $from = urlencode(substr($this->getOriginator(), 0, 11));

        $url = 'http://www.smsglobal.com/http-api.php' .
               '?action=sendsms' .
               '&user=' . $this->getUsername() .
               '&password=' . $this->getPassword() .
               '&from=' . $from .
               '&to=' . $this->getPhone($phone) .
               '&text=' . substr(rawurlencode($message), 0, 153);

        $returnedData = file_get_contents($url);

        return strpos($returnedData, 'OK: 0') !== false;
    }
}
