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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Panda\Model;

/**
 * Class Templates
 *
 * @package Licentia\Panda\Model
 */
class Templates extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_templates';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'templates';

    /**
     * @var ResourceModel\Templates\CollectionFactory
     */
    protected $templatesCollection;

    /**
     * @var ResourceModel\Chains\CollectionFactory
     */
    protected $chainsCollection;

    /**
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ResourceModel\Templates\CollectionFactory                    $templatesCollection
     * @param ResourceModel\Chains\CollectionFactory                       $chainsCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Templates\CollectionFactory $templatesCollection,
        ResourceModel\Chains\CollectionFactory $chainsCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->templatesCollection = $templatesCollection;
        $this->chainsCollection = $chainsCollection;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Templates::class);
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {

        $list = $this->templatesCollection->create()
                                          ->addFieldToFilter('is_active', 1)
                                          ->addFieldToSelect('template_id')
                                          ->addFieldToSelect('name');

        $result = [];

        foreach ($list as $template) {
            $result[] = ['value' => $template->getId(), 'label' => $template->getName()];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toFormValues()
    {

        $options = $this->getOptionArray();

        $return = [];

        foreach ($options as $option) {
            $return[$option['value']] = $option['label'];
        }

        return $return;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _beforeDelete()
    {

        parent::_beforeDelete();

        $chains = $this->chainsCollection->create()->addFieldToFilter('template_id', $this->getId());

        $url = false;
        if ($chains->count() > 0) {
            $ars = [];
            foreach ($chains as $chain) {
                if (isset($ars[$chain->getAutoresponderId()])) {
                    continue;
                }
                $ars[$chain->getAutoresponderId()] = 1;

                $url .= ' ' . $chain->getAutoresponderId();
            }

            if ($url) {
                $url = ' Autoresponders IDs: ' . $url;
            }

            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'There is at least one Autoresponder Chain using this template. Please delete the Chain to continue.' .
                    $url
                )
            );
        }

        return $this;
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
     * @param $globalTemplateId
     *
     * @return $this
     */
    public function setGlobalTemplateId($globalTemplateId)
    {

        return $this->setData('global_template_id', $globalTemplateId);
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {

        return $this->setData('store_id', $storeId);
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
     * @param $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId)
    {

        return $this->setData('campaign_id', $campaignId);
    }

    /**
     * @param $segmentsIds
     *
     * @return $this
     */
    public function setSegmentsIds($segmentsIds)
    {

        return $this->setData('segments_ids', $segmentsIds);
    }

    /**
     * @param $customerGroups
     *
     * @return $this
     */
    public function setCustomerGroups($customerGroups)
    {

        return $this->setData('customer_groups', $customerGroups);
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
     * @param $message
     *
     * @return $this
     */
    public function setMessage($message)
    {

        return $this->setData('message', $message);
    }

    /**
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {

        return $this->setData('is_active', $isActive);
    }

    /**
     * @param $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {

        return $this->setData('gender', $gender);
    }

    /**
     * @param $age
     *
     * @return $this
     */
    public function setAge($age)
    {

        return $this->setData('age', $age);
    }

    /**
     * @param $recipients
     *
     * @return $this
     */
    public function setRecipients($recipients)
    {

        return $this->setData('recipients', $recipients);
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
    public function getGlobalTemplateId()
    {

        return $this->getData('global_template_id');
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {

        return $this->getData('store_id');
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
    public function getCampaignId()
    {

        return $this->getData('campaign_id');
    }

    /**
     * @return mixed
     */
    public function getSegmentsIds()
    {

        return $this->getData('segments_ids');
    }

    /**
     * @return mixed
     */
    public function getCustomerGroups()
    {

        return $this->getData('customer_groups');
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
    public function getMessage()
    {

        return $this->getData('message');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
    }

    /**
     * @return mixed
     */
    public function getGender()
    {

        return $this->getData('gender');
    }

    /**
     * @return mixed
     */
    public function getAge()
    {

        return $this->getData('age');
    }

    /**
     * @return mixed
     */
    public function getRecipients()
    {

        return $this->getData('recipients');
    }
}
