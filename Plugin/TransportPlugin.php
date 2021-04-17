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

namespace Licentia\Panda\PLugin;

use Licentia\Panda\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Mail\TransportInterface;

/**
 * Class Transport
 *
 * @package Licentia\Panda\Model
 */
class TransportPlugin
{

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected EncryptorInterface $encryptor;

    /**
     * @var Data
     */
    protected Data $pandaHelper;

    /**
     * TransportPlugin constructor.
     *
     * @param Data                 $pandaHelper
     * @param EncryptorInterface   $encryptor
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param null                 $parameters
     */
    public function __construct(
        Data $pandaHelper,
        EncryptorInterface $encryptor,
        ScopeConfigInterface $scopeConfigInterface,
        $parameters = null
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->encryptor = $encryptor;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param TransportInterface $subject
     * @param \Closure           $proceed
     *
     * @return mixed
     */
    public function aroundSendMessage(
        TransportInterface $subject,
        \Closure $proceed
    ) {

        $smtp = $this->scopeConfig->getValue(
            'panda_nuntius/transactional',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/transactional/enabled') ||
            strlen(trim($smtp['server'])) == 0) {
            return $proceed();
        } else {

            /** @var \Magento\Framework\Mail\Message $message */
            $message = $subject->getMessage();
            $message = \Zend\Mail\Message::fromString($message->getRawMessage())->setEncoding('utf-8');

            $transport = new \Zend\Mail\Transport\Smtp();

            $optionsData = [
                'name'             => 'localhost',
                'host'             => $smtp['server'],
                'port'             => $smtp['port'],
                'connection_class' => $smtp['auth'],
            ];

            if ($smtp['auth'] != 'none') {
                $optionsData['connection_config'] = [
                    'username' => $smtp['username'],
                    'password' => $this->encryptor->decrypt($smtp['password']),
                ];
            }

            if ($smtp['ssl'] != 'none') {
                $optionsData['connection_config']['ssl'] = $smtp['ssl'];
            }

            $options = new \Zend\Mail\Transport\SmtpOptions($optionsData);
            $transport->setOptions($options);

            try {
                $transport->send($message);
            } catch (\Exception $e) {
                $this->pandaHelper->logException($e);
            }

        }
    }
}
