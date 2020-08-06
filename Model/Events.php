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

namespace Licentia\Panda\Model;

/**
 * Class Events
 *
 * @package Licentia\Panda\Model
 */
class Events extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_events';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'events';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Events::class);
    }

    /**
     * @param $eventId
     *
     * @return $this
     */
    public function setEventId($eventId)
    {

        return $this->setData('event_id', $eventId);
    }

    /**
     * @param $event
     *
     * @return $this
     */
    public function setEvent($event)
    {

        return $this->setData('event', $event);
    }

    /**
     * @param $autoresponderId
     *
     * @return $this
     */
    public function setAutoresponderId($autoresponderId)
    {

        return $this->setData('autoresponder_id', $autoresponderId);
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {

        return $this->setData('customer_id', $customerId);
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
     * @param $subscriberFirstname
     *
     * @return $this
     */
    public function setSubscriberFirstname($subscriberFirstname)
    {

        return $this->setData('subscriber_firstname', $subscriberFirstname);
    }

    /**
     * @param $subscriberLastname
     *
     * @return $this
     */
    public function setSubscriberLastname($subscriberLastname)
    {

        return $this->setData('subscriber_lastname', $subscriberLastname);
    }

    /**
     * @param $subscriberEmail
     *
     * @return $this
     */
    public function setSubscriberEmail($subscriberEmail)
    {

        return $this->setData('subscriber_email', $subscriberEmail);
    }

    /**
     * @param $executeAt
     *
     * @return $this
     */
    public function setExecuteAt($executeAt)
    {

        return $this->setData('execute_at', $executeAt);
    }

    /**
     * @param $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {

        return $this->setData('created_at', $createdAt);
    }

    /**
     * @param $createdAtGrid
     *
     * @return $this
     */
    public function setCreatedAtGrid($createdAtGrid)
    {

        return $this->setData('created_at_grid', $createdAtGrid);
    }

    /**
     * @param $executed
     *
     * @return $this
     */
    public function setExecuted($executed)
    {

        return $this->setData('executed', $executed);
    }

    /**
     * @param $executedAt
     *
     * @return $this
     */
    public function setExecutedAt($executedAt)
    {

        return $this->setData('executed_at', $executedAt);
    }

    /**
     * @param $dataObjectId
     *
     * @return $this
     */
    public function setDataObjectId($dataObjectId)
    {

        return $this->setData('data_object_id', $dataObjectId);
    }

    /**
     * @param $chainId
     *
     * @return $this
     */
    public function setChainId($chainId)
    {

        return $this->setData('chain_id', $chainId);
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {

        return $this->getData('event_id');
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {

        return $this->getData('event');
    }

    /**
     * @return mixed
     */
    public function getAutoresponderId()
    {

        return $this->getData('autoresponder_id');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {

        return $this->getData('customer_id');
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
    public function getSubscriberFirstname()
    {

        return $this->getData('subscriber_firstname');
    }

    /**
     * @return mixed
     */
    public function getSubscriberLastname()
    {

        return $this->getData('subscriber_lastname');
    }

    /**
     * @return mixed
     */
    public function getSubscriberEmail()
    {

        return $this->getData('subscriber_email');
    }

    /**
     * @return mixed
     */
    public function getExecuteAt()
    {

        return $this->getData('execute_at');
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {

        return $this->getData('created_at');
    }

    /**
     * @return mixed
     */
    public function getCreatedAtGrid()
    {

        return $this->getData('created_at_grid');
    }

    /**
     * @return mixed
     */
    public function getExecuted()
    {

        return $this->getData('executed');
    }

    /**
     * @return mixed
     */
    public function getExecutedAt()
    {

        return $this->getData('executed_at');
    }

    /**
     * @return mixed
     */
    public function getDataObjectId()
    {

        return $this->getData('data_object_id');
    }

    /**
     * @return mixed
     */
    public function getChainId()
    {

        return $this->getData('chain_id');
    }
}
