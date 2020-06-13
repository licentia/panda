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
 * Class Mosms
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Mosms extends Core
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
     * @return string
     */
    public function sendSMS($phone, $message)
    {

        $url = "http://www.mosms.com/se/sms-send.php";

        $result = file_get_contents(
            $url . "?username=" . $this->getUsername()
            . "&password=" . $this->getPassword() . "&nr=" . $this->getPhone($phone) . "&type=text&customsender="
            . $this->getOriginator() . "&allowlong=1&data=" . rawurlencode($message)
        );

        if ($result <> "0") {
            return false;
        } else {
            return true;
        }
    }
}
