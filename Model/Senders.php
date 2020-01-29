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
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Panda\Model;

/**
 * Class Senders
 *
 * @package Licentia\Panda\Model
 */
class Senders extends \Magento\Framework\Model\AbstractModel
{

    const OBSCURE_PASSWORD_REPLACEMENT = 'nothingtoseehere';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_senders';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'sender';

    /**
     * @var ResourceModel\Campaigns\CollectionFactory
     */
    protected $campaignsCollection;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptorInterface;

    /**
     * @var ResourceModel\Senders\CollectionFactory
     */
    protected $sendersCollection;

    /**
     * Senders constructor.
     *
     * @param ResourceModel\Campaigns\CollectionFactory                    $campaignsCollection
     * @param ResourceModel\Senders\CollectionFactory                      $sendersCollection
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Encryption\EncryptorInterface             $encryptorInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->encryptorInterface = $encryptorInterface;
        $this->campaignsCollection = $campaignsCollection;
        $this->sendersCollection = $sendersCollection;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Panda\Model\ResourceModel\Senders::class);
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {

        parent::_afterLoad();

        if ($this->getPassword()) {
            $this->setPassword($this->encryptorInterface->decrypt($this->getPassword()));
        }

        if ($this->getBouncesPassword()) {
            $this->setBouncesPassword($this->encryptorInterface->decrypt($this->getBouncesPassword()));
        }

        if ($this->getApiKey()) {
            $this->setApiKey($this->encryptorInterface->decrypt($this->getApiKey()));
        }

        return $this;
    }

    /**
     *
     */
    public function beforeSave()
    {

        if ($this->getApiKey()) {
            $this->setApiKey($this->encryptorInterface->encrypt($this->getApiKey()));
        }

        if ($this->getPassword()) {
            $this->setPassword($this->encryptorInterface->encrypt($this->getPassword()));
        }
        if ($this->getBouncesPassword()) {
            $this->setBouncesPassword($this->encryptorInterface->encrypt($this->getBouncesPassword()));
        }
    }

    /**
     * @return array
     */
    public function getGateways()
    {

        $list = ['0' => __('-- Please Select --')] + \Licentia\Panda\Model\Service\Sms\Core::PANDA_SMS_GATEWAYS;

        asort($list);

        return $list;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {

        $total = $this->campaignsCollection->create()
                                           ->addFieldToFilter('sender_id', $this->getId())
            #->addFieldToFilter('status', ['nin' => ['finished', 'canceled']])
                                           ->getSize();

        if ($total > 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    "You cannot remove this sender since there are campaigns sent from this sender %1. " .
                    "Please delete those campaigns before removing the subscriber  ",
                    $this->getName()
                )
            );
        }

        return parent::beforeDelete();
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getSenders($type = 'email')
    {

        $return = [];
        $senders = $this->sendersCollection->create()->addFieldToFilter('type', $type);

        /** @var \Licentia\Panda\Model\Senders $sender */
        foreach ($senders as $sender) {
            if ($type == 'sms') {
                $return[$sender->getId()] = $sender->getName() . ' / ' .
                                            $sender->getOriginator() . ' (' . ucfirst($sender->getGateway()) . ')';
            } else {
                $return[$sender->getId()] = $sender->getName() . ' / ' . $sender->getEmail();
            }
        }

        return $return;
    }

    /**
     * @param $senderId
     *
     * @return $this
     */
    public function setSenderId($senderId)
    {

        return $this->setData('sender_id', $senderId);
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {

        return $this->setData('type', $type);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {

        return $this->setData('name', $name);
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {

        return $this->setData('email', $email);
    }

    /**
     * @param $gateway
     *
     * @return $this
     */
    public function setGateway($gateway)
    {

        return $this->setData('gateway', $gateway);
    }

    /**
     * @param $server
     *
     * @return $this
     */
    public function setServer($server)
    {

        return $this->setData('server', $server);
    }

    /**
     * @param $username
     *
     * @return $this
     */
    public function setUsername($username)
    {

        return $this->setData('username', $username);
    }

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password)
    {

        return $this->setData('password', $password);
    }

    /**
     * @param $apiKey
     *
     * @return $this
     */
    public function setApiKey($apiKey)
    {

        return $this->setData('api_key', $apiKey);
    }

    /**
     * @param $originator
     *
     * @return $this
     */
    public function setOriginator($originator)
    {

        return $this->setData('originator', $originator);
    }

    /**
     * @param $service
     *
     * @return $this
     */
    public function setService($service)
    {

        return $this->setData('service', $service);
    }

    /**
     * @param $port
     *
     * @return $this
     */
    public function setPort($port)
    {

        return $this->setData('port', $port);
    }

    /**
     * @param $auth
     *
     * @return $this
     */
    public function setAuth($auth)
    {

        return $this->setData('auth', $auth);
    }

    /**
     * @param $ssl
     *
     * @return $this
     */
    public function setSsl($ssl)
    {

        return $this->setData('ssl', $ssl);
    }

    /**
     * @param $headers
     *
     * @return $this
     */
    public function setHeaders($headers)
    {

        return $this->setData('headers', $headers);
    }

    /**
     * @param $bouncesEmail
     *
     * @return $this
     */
    public function setBouncesEmail($bouncesEmail)
    {

        return $this->setData('bounces_email', $bouncesEmail);
    }

    /**
     * @param $bouncesServer
     *
     * @return $this
     */
    public function setBouncesServer($bouncesServer)
    {

        return $this->setData('bounces_server', $bouncesServer);
    }

    /**
     * @param $bouncesUsername
     *
     * @return $this
     */
    public function setBouncesUsername($bouncesUsername)
    {

        return $this->setData('bounces_username', $bouncesUsername);
    }

    /**
     * @param $bouncesPassword
     *
     * @return $this
     */
    public function setBouncesPassword($bouncesPassword)
    {

        return $this->setData('bounces_password', $bouncesPassword);
    }

    /**
     * @param $bouncesPort
     *
     * @return $this
     */
    public function setBouncesPort($bouncesPort)
    {

        return $this->setData('bounces_port', $bouncesPort);
    }

    /**
     * @param $bouncesSsl
     *
     * @return $this
     */
    public function setBouncesSsl($bouncesSsl)
    {

        return $this->setData('bounces_ssl', $bouncesSsl);
    }

    /**
     * @param $bouncesAuth
     *
     * @return $this
     */
    public function setBouncesAuth($bouncesAuth)
    {

        return $this->setData('bounces_auth', $bouncesAuth);
    }

    /**
     * @param $testSms
     *
     * @return $this
     */
    public function setTestSms($testSms)
    {

        return $this->setData('test_sms', $testSms);
    }

    /**
     * @param $replyTo
     *
     * @return $this
     */
    public function setReplyTo($replyTo)
    {

        return $this->setData('reply_to', $replyTo);
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {

        return $this->getData('sender_id');
    }

    /**
     * @return mixed
     */
    public function getType()
    {

        return $this->getData('type');
    }

    /**
     * @return mixed
     */
    public function getName()
    {

        return $this->getData('name');
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {

        return $this->getData('email');
    }

    /**
     * @return mixed
     */
    public function getGateway()
    {

        return $this->getData('gateway');
    }

    /**
     * @return mixed
     */
    public function getServer()
    {

        return $this->getData('server');
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
    public function getService()
    {

        return $this->getData('service');
    }

    /**
     * @return mixed
     */
    public function getPort()
    {

        return $this->getData('port');
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {

        return $this->getData('auth');
    }

    /**
     * @return mixed
     */
    public function getSsl()
    {

        return $this->getData('ssl');
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {

        return $this->getData('headers');
    }

    /**
     * @return mixed
     */
    public function getBouncesEmail()
    {

        return $this->getData('bounces_email');
    }

    /**
     * @return mixed
     */
    public function getBouncesServer()
    {

        return $this->getData('bounces_server');
    }

    /**
     * @return mixed
     */
    public function getBouncesUsername()
    {

        return $this->getData('bounces_username');
    }

    /**
     * @return mixed
     */
    public function getBouncesPassword()
    {

        return $this->getData('bounces_password');
    }

    /**
     * @return mixed
     */
    public function getBouncesPort()
    {

        return $this->getData('bounces_port');
    }

    /**
     * @return mixed
     */
    public function getBouncesSsl()
    {

        return $this->getData('bounces_ssl');
    }

    /**
     * @return mixed
     */
    public function getBouncesAuth()
    {

        return $this->getData('bounces_auth');
    }

    /**
     * @return mixed
     */
    public function getTestSms()
    {

        return $this->getData('test_sms');
    }

    /**
     * @return mixed
     */
    public function getReplyTo()
    {

        return $this->getData('reply_to');
    }
}
