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
 * Class Queue
 *
 * @package Licentia\Panda\Model
 */
class Queue extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_queue';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'queue';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Queue::class);
    }

    /**
     * @param $queueId
     *
     * @return $this
     */
    public function setQueueId($queueId)
    {

        return $this->setData('queue_id', $queueId);
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
     * @param $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId)
    {

        return $this->setData('campaign_id', $campaignId);
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
     * @param $senderEmail
     *
     * @return $this
     */
    public function setSenderEmail($senderEmail)
    {

        return $this->setData('sender_email', $senderEmail);
    }

    /**
     * @param $senderName
     *
     * @return $this
     */
    public function setSenderName($senderName)
    {

        return $this->setData('sender_name', $senderName);
    }

    /**
     * @param $subscriberId
     *
     * @return $this
     */
    public function setSubscriberId($subscriberId)
    {

        return $this->setData('subscriber_id', $subscriberId);
    }

    /**
     * @param $cellphone
     *
     * @return $this
     */
    public function setCellphone($cellphone)
    {

        return $this->setData('cellphone', $cellphone);
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
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {

        return $this->setData('subject', $subject);
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setMessage($message)
    {

        return $this->setData('message', $message);
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
     * @param $attempts
     *
     * @return $this
     */
    public function setAttempts($attempts)
    {

        return $this->setData('attempts', $attempts);
    }

    /**
     * @param $sendDate
     *
     * @return $this
     */
    public function setSendDate($sendDate)
    {

        return $this->setData('send_date', $sendDate);
    }

    /**
     * @param $processId
     *
     * @return $this
     */
    public function setProcessId($processId)
    {

        return $this->setData('process_id', $processId);
    }

    /**
     * @return mixed
     */
    public function getQueueId()
    {

        return $this->getData('queue_id');
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
    public function getCampaignId()
    {

        return $this->getData('campaign_id');
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
    public function getSenderEmail()
    {

        return $this->getData('sender_email');
    }

    /**
     * @return mixed
     */
    public function getSenderName()
    {

        return $this->getData('sender_name');
    }

    /**
     * @return mixed
     */
    public function getSubscriberId()
    {

        return $this->getData('subscriber_id');
    }

    /**
     * @return mixed
     */
    public function getCellphone()
    {

        return $this->getData('cellphone');
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
    public function getSubject()
    {

        return $this->getData('subject');
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {

        return $this->getData('message');
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
    public function getAttempts()
    {

        return $this->getData('attempts');
    }

    /**
     * @return mixed
     */
    public function getSendDate()
    {

        return $this->getData('send_date');
    }

    /**
     * @return mixed
     */
    public function getProcessId()
    {

        return $this->getData('process_id');
    }
}
