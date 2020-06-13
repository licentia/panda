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
 * Class Egoicampaign
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
class Egoicampaign extends Core
{

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'FROM ID', 'type' => 'text', 'required' => true],
        'username'   => ['label' => 'List ID', 'type' => 'text', 'required' => true],
        'api_key'    => ['label' => 'API KEY', 'type' => 'password', 'required' => true],
    ];

    const API_URL_SOAP = 'http://api.e-goi.com/v2/soap.php?wsdl';

    /**
     * @param $phone
     * @param $message
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function sendSMS($phone, $message)
    {

        $params = [
            'apikey'    => $this->getApiKey(),
            'listID'    => $this->getUsername(),
            'subject'   => 'Sent From Magento 2',
            'fromID'    => $this->getOriginator(),
            'cellphone' => $this->getPhone($phone, true),
            'message'   => $message,
        ];

        $client = new \SoapClient(self::API_URL_SOAP);
        $result = $client->sendSMS($params);

        if (!is_array($result) || !isset($result['ID'])) {
            throw new \Exception(print_r($result, true));
        }

        return true;
    }
}
