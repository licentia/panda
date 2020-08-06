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
 * Class Core
 *
 * @package Licentia\Panda\Model\Service\Sms
 */
abstract class Core extends \Magento\Framework\DataObject implements SmsInterface
{

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * Core constructor.
     *
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param array                               $data
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        array $data = []
    ) {

        parent::__construct($data);

        $this->curl = $curl;
    }

    const PANDA_SMS_GATEWAYS = [
        'Clickatel'      => 'Clickatell',
        'Egoi'           => 'E-Goi (Transactional)',
        'Egoicampaign'   => 'E-Goi (Campaign)',
        'Mosms'          => 'Mosms',
        'Msg91'          => 'Msg91',
        'Smsglobal'      => 'Smsglobal',
        'Spryng'         => 'Spryng',
        'Textmarketer'   => 'Textmarketer',
        'Nexmo'          => 'Nexmo',
        'Txtlocal'       => 'Txtlocal',
        'SmsGateway'     => 'SmsGateway',
        'Semysms'        => 'Semysms',
        'Africastalking' => 'Africastalking',
        'Textbelt'       => 'Textbelt',
        'ClockworkSms'   => 'ClockworkSms',
    ];

    /**
     * @var array
     */
    protected $fields = [
        'originator' => ['label' => 'Originator', 'type' => 'text', 'required' => true],
        'username'   => ['label' => 'Username', 'type' => 'text', 'required' => true],
        'api_key'    => ['label' => 'API KEY', 'type' => 'password', 'required' => true],
        'password'   => ['label' => 'Password', 'type' => 'password', 'required' => true],
    ];

    /**
     * @return mixed
     */
    public function getApiKey()
    {

        return $this->getData('api_key');
    }

    /**
     * @return mixed
     */
    public function getOriginator()
    {

        return $this->getData('originator');
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {

        return $this->getData('username');
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {

        return $this->getData('password');
    }

    /**
     * @return array
     */
    public function getFields()
    {

        return array_merge(
            ['name' => ['label' => __('Internal Identifier'), 'type' => 'text', 'required' => true]],
            [
                'test_sms' => [
                    'label'    => __('SMS TestNumber'),
                    'type'     => 'text',
                    'required' => false,
                    'note'     => __(
                        'Use this field to test your settings after saving. Please use the format: ' .
                        'countryCode-number. Eg: 1-555555555'
                    ),
                ],
            ],
            $this->fields
        );
    }

    /**
     * @param      $phone
     * @param bool $hifen
     *
     * @return string
     */
    public function getPhone($phone, $hifen = false)
    {

        if ($hifen) {
            $phone = trim(preg_replace('/[^0-9\-]/', '', $phone));
        } else {
            $phone = trim(preg_replace('/[^0-9]/', '', $phone));
        }

        return $phone;
    }
}
