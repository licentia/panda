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
 * Class Chains
 *
 * @package Licentia\Panda\Model
 */
class Chains extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_chains';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'chains';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Chains::class);
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
     * @param $autoresponderId
     *
     * @return $this
     */
    public function setAutoresponderId($autoresponderId)
    {

        return $this->setData('autoresponder_id', $autoresponderId);
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
     * @param $event
     *
     * @return $this
     */
    public function setEvent($event)
    {

        return $this->setData('event', $event);
    }

    /**
     * @param $templateId
     *
     * @return $this
     */
    public function setTemplateId($templateId)
    {

        return $this->setData('template_id', $templateId);
    }

    /**
     * @param $parentId
     *
     * @return $this
     */
    public function setParentId($parentId)
    {

        return $this->setData('parent_id', $parentId);
    }

    /**
     * @param $sortOrder
     *
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {

        return $this->setData('sort_order', $sortOrder);
    }

    /**
     * @param $extraData
     *
     * @return $this
     */
    public function setExtraData($extraData)
    {

        return $this->setData('extra_data', $extraData);
    }

    /**
     * @param $yesCondition
     *
     * @return $this
     */
    public function setYesCondition($yesCondition)
    {

        return $this->setData('yes_condition', $yesCondition);
    }

    /**
     * @param $mainCondition
     *
     * @return $this
     */
    public function setMainCondition($mainCondition)
    {

        return $this->setData('main_condition', $mainCondition);
    }

    /**
     * @param $editable
     *
     * @return $this
     */
    public function setEditable($editable)
    {

        return $this->setData('editable', $editable);
    }

    /**
     * @return mixed
     */
    public function getChainId()
    {

        return $this->getData('chain_id');
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
    public function getName()
    {

        return $this->getData('name');
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
    public function getTemplateId()
    {

        return $this->getData('template_id');
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {

        return $this->getData('parent_id');
    }

    /**
     * @return mixed
     */
    public function getSortOrder()
    {

        return $this->getData('sort_order');
    }

    /**
     * @return mixed
     */
    public function getExtraData()
    {

        return $this->getData('extra_data');
    }

    /**
     * @return mixed
     */
    public function getYesCondition()
    {

        return $this->getData('yes_condition');
    }

    /**
     * @return mixed
     */
    public function getMainCondition()
    {

        return $this->getData('main_condition');
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {

        return $this->getData('editable');
    }
}
