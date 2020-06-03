<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   03/06/20, 15:35 GMT
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
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var Data
     */
    protected $pandaHelper;

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
