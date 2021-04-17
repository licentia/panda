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
 * Class ClockworkSms
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class ClockworkSms extends Core
{

    /**
     * @var array
     */
    protected array $fields = [
        'api_key'    => ['label' => 'Your API Key', 'type' => 'password', 'required' => true,],
        'originator' => ['label' => 'From', 'type' => 'text', 'required' => true,],
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
            $url = "https://api.clockworksms.com/http/send.aspx?";

            $URL = $url . "key=" . $this->getApiKey()
                   . "&to=" . $this->getPhone($phone)
                   . "&content=" . urlencode($message)
                   . "&from=" . $this->getOriginator();

            $fp = fopen($URL, 'r');
            $result = fread($fp, 1024);
            if (stripos($result, 'Invalid') !== false) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
        }

        return false;
    }
}
