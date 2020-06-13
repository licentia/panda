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
 * Class Cancellation
 *
 * @package Licentia\Panda\Model
 */
class Cancellation extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_cancellation';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'cancellation';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Cancellation::class);
    }

    /**
     * @param $recordId
     *
     * @return $this
     */
    public function setRecordId($recordId)
    {

        return $this->setData('record_id', $recordId);
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
     * @return mixed
     */
    public function getRecordId()
    {

        return $this->getData('record_id');
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
}
