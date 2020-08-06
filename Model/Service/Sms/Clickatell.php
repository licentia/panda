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
 * Class Clickatell
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Clickatell extends Core
{

    /**
     * @param $phone
     * @param $message
     *
     * @return mixed
     */
    public function sendSMS($phone, $message)
    {

        $url = 'https://api.clickatell.com' .
               '/http/sendmsg' .
               '?user=' . $this->getUsername() .
               '&password=' . $this->getPassword() .
               '&to=' . $this->getPhone($phone) .
               '&api_id=' . $this->getApiKey() .
               '&text=' . substr(urlencode($message), 0, 153);

        $this->curl->get($url);

        return ($this->curl->getStatus() == 200);
    }
}
