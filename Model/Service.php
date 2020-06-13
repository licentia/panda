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

namespace Licentia\Panda\Model;

/**
 * Class Service
 *
 * @package Licentia\Panda\Model
 */
class Service
{

    /**
     * @var Service\SmtpFactory
     */
    protected $smtpFactory;

    /**
     * @var Service\SmsFactory
     */
    protected $smsFactory;

    /**
     * Service constructor.
     *
     * @param Service\SmsFactory  $smsFactory
     * @param Service\SmtpFactory $smtpFactory
     */
    public function __construct(
        Service\SmsFactory $smsFactory,
        Service\SmtpFactory $smtpFactory
    ) {

        $this->smtpFactory = $smtpFactory;
        $this->smsFactory = $smsFactory;
    }

    /**
     * @return Service\Sms
     */
    public function getSmsService()
    {

        return $this->smsFactory->create();
    }

    /**
     * @return Service\Smtp
     */
    public function getEmailService()
    {

        return $this->smtpFactory->create();
    }
}
