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
 * Class Templates
 *
 * @package Licentia\Panda\Model
 */
class TemplatesGlobal extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_templates_global';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'templates_global';

    /**
     * @var ResourceModel\TemplatesGlobal\CollectionFactory
     */
    protected $templatesCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * TemplatesGlobal constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ResourceModel\TemplatesGlobal\CollectionFactory              $templatesCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\TemplatesGlobal\CollectionFactory $templatesCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->templatesCollection = $templatesCollection;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\TemplatesGlobal::class);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {

        parent::beforeDelete();

        $connection = $this->getResource()->getConnection();

        $campaigns = $connection->fetchOne(
            $connection->select()
                       ->from($this->getResource()
                                   ->getTable('panda_campaigns'), ['COUNT(*)'])
                       ->where('global_template_id =?', $this->getId())
        );

        $splits = $connection->fetchOne(
            $connection->select()
                       ->from($this->getResource()
                                   ->getTable('panda_campaigns_splits'), ['COUNT(*)'])
                       ->where('global_template_id =?', $this->getId())
        );

        $follow = $connection->fetchOne(
            $connection->select()
                       ->from($this->getResource()
                                   ->getTable('panda_campaigns_followup'), ['COUNT(*)'])
                       ->where('global_template_id =?', $this->getId())
        );
        $autoresponders = $connection->fetchOne(
            $connection->select()
                       ->from($this->getResource()
                                   ->getTable('panda_autoresponders'), ['COUNT(*)'])
                       ->where('global_template_id =?', $this->getId())
        );

        $msg = '';
        $exception = false;
        if ($campaigns > 0) {
            $exception = true;
            $msg .= __("You have %1 campaigns using this Global Template. ", $campaigns);
        }
        if ($autoresponders > 0) {
            $exception = true;
            $msg .= __("You have %1 autoresponders using this Global Template. ", $autoresponders);
        }
        if ($follow > 0) {
            $exception = true;
            $msg .= __("You have %1 followups using this Global Template. ", $follow);
        }
        if ($splits > 0) {
            $exception = true;
            $msg .= __("You have %1 A/B campaigns using this Global Template. ", $splits);
        }

        if ($exception) {
            $msg .= __("You cannot remove this Global Template");
            throw new \Magento\Framework\Exception\LocalizedException(__($msg));
        }

        return $this;
    }

    /**
     * @param $templateId
     * @param $storeId
     * @param $message
     *
     * @return mixed
     */
    public function getTemplateForMessage($templateId, $storeId, $message)
    {

        $content = '{MESSAGE}';

        $collection = $this->getCollection();
        $collection->addFieldToFilter('is_active', '1');
        $collection->setOrder('store_id', 'DESC');

        $collection->getSelect()
                   ->where("store_id IS NULL OR store_id=?", $storeId)
                   ->where('template_id = ? OR parent_id=?', $templateId);

        if ($collection->getSize() > 0) {
            $content = $collection->getFirstItem()->getData('content');
        } else {
            $fallback = $this->scopeConfig->getValue(
                'panda_nuntius/info/fallback',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

            if ($fallback) {
                $collection = clone $this->getCollection();
                $collection->setOrder('store_id', 'DESC');

                $collection->getSelect()
                           ->where("store_id IS NULL OR store_id=?", $storeId)
                           ->where('template_id = ? OR parent_id=?', $fallback);

                if ($collection->getSize() > 0) {
                    $content = $collection->getFirstItem()->getData('content');
                }
            }
        }

        return str_replace('{MESSAGE}', $message, $content);
    }

    /**
     * @param $templateId
     * @param $storeId
     *
     * @return mixed
     */
    public function getTemplateFile($templateId, $storeId)
    {

        $collection = $this->getCollection();
        $collection->addFieldToFilter('is_active', '1');
        $collection->setOrder('store_id', 'DESC');

        $collection->getSelect()
                   ->where("store_id IS NULL OR store_id=?", $storeId)
                   ->where('template_id = ? OR parent_id=?', $templateId);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem()->getData('template_file');
        } else {
            $fallback = $this->scopeConfig->getValue(
                'panda_nuntius/info/fallback',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

            if ($fallback) {
                $collection = clone $this->getCollection();
                $collection->setOrder('store_id', 'DESC');

                $collection->getSelect()
                           ->where("store_id IS NULL OR store_id=?", $storeId)
                           ->where('template_id = ? OR parent_id=?', $fallback);

                if ($collection->getSize() > 0) {
                    return $collection->getFirstItem()->getData('template_file');
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $list = $this->templatesCollection->create()
                                          ->addFieldToFilter('is_active', 1)
                                          ->addFieldToFilter('parent_id', ['null' => true])
                                          ->addFieldToSelect('template_id')
                                          ->addFieldToSelect('name');

        $result = [];

        foreach ($list as $template) {
            $result[] = ['value' => $template->getId(), 'label' => $template->getName()];
        }

        return $result;
    }

    /**
     * @param string $firstElement
     *
     * @return array
     */
    public function toFormValues($firstElement = '')
    {

        $options = $this->toOptionArray();

        $return = [];

        if ($firstElement) {
            $return['0'] = __($firstElement);
        }

        foreach ($options as $option) {
            $return[$option['value']] = $option['label'];
        }

        return $return;
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
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {

        return $this->setData('store_id', $storeId);
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
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {

        return $this->setData('name', $name);
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function setContent($content)
    {

        return $this->setData('content', $content);
    }

    /**
     * @param $templateStyles
     *
     * @return $this
     */
    public function setTemplateStyles($templateStyles)
    {

        return $this->setData('template_styles', $templateStyles);
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
     * @param $recipients
     *
     * @return $this
     */
    public function setRecipients($recipients)
    {

        return $this->setData('recipients', $recipients);
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
     * @return mixed
     */
    public function getTemplateId()
    {

        return $this->getData('template_id');
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
    public function getParentId()
    {

        return $this->getData('parent_id');
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
    public function getContent()
    {

        return $this->getData('content');
    }

    /**
     * @return mixed
     */
    public function getTemplateStyles()
    {

        return $this->getData('template_styles');
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
    public function getRecipients()
    {

        return $this->getData('recipients');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
    }
}
