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
 * Class Tags
 *
 * @package Licentia\Panda\Model
 */
class Tags extends \Magento\Framework\Model\AbstractModel
{

    const TAG_ENTITY_TYPES = ['subscribers', 'campaigns', 'autoresponders', 'splits'];

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_tags';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'tags';

    /**
     * @var TagsRelationsFactory
     */
    protected $tagsRelationsFactory;

    /**
     * Tags constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param TagsRelationsFactory                                         $tagsRelationsFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        TagsRelationsFactory $tagsRelationsFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->tagsRelationsFactory = $tagsRelationsFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Panda\Model\ResourceModel\Tags::class);
    }

    /**
     * @param $entityType
     * @param $entityId
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getTagsRelations($entityType, $entityId)
    {

        /** @var \Magento\Framework\Data\Collection\AbstractDb $collection */
        $collection = $this->tagsRelationsFactory->create()
                                                 ->getCollection()
                                                 ->addFieldToFilter('entity_type', $entityType)
                                                 ->addFieldToFilter('entity_id', $entityId);

        $collection->getSelect()
                   ->joinLeft(
                       [
                           'tags_table' => $collection->getResource()
                                                      ->getTable('panda_tags'),
                       ],
                       'main_table.tag_id = tags_table.tag_id'
                   );

        return $collection;
    }

    /**
     *
     * @return array
     */
    public function getAllTagsHash()
    {

        $return = [];

        $tags = $this->getCollection()
                     ->addFieldToFilter('is_active', 1)
                     ->setOrder('name', 'asc');

        foreach ($tags as $tag) {
            $return[$tag->getId()] = $tag->getName();
        }

        return $return;
    }

    /**
     *
     * @return array
     */
    public function getAllTagsValues()
    {

        $return = [];

        $tags = $this->getCollection()
                     ->addFieldToFilter('is_active', 1)
                     ->setOrder('name', 'asc');

        foreach ($tags as $tag) {
            $return[] = ['value' => $tag->getId(), 'label' => $tag->getName()];
        }

        return $return;
    }

    /**
     * @param $entityType
     * @param $model
     * @param $tags
     *
     * @return mixed
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateTags($entityType, $model, $tags)
    {

        if (!in_array($entityType, self::TAG_ENTITY_TYPES)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Tag Type: %1', $entityType));
        }

        if ($model->getSkipTags()) {
            return $model;
        }

        #Delete old tags
        $tagsToRemove = $this->getTagsRelations($entityType, $model->getId());

        foreach ($tagsToRemove as $remove) {
            $this->load($remove->getTagId())
                 ->setData($entityType, new \Zend_Db_Expr($entityType . ' - 1'))
                 ->save();
            $remove->delete();
        }

        if (count($tags) == 0 || (count($tags) == 1 && isset($tags[0]) && $tags[0] == 0)) {
            return $model;
        }

        foreach ($tags as $tag) {
            $data = [];
            $data['tag_id'] = $tag;
            $data['entity_type'] = $entityType;
            $data['entity_id'] = $model->getId();

            $this->tagsRelationsFactory->create()
                                       ->setData($data)
                                       ->save();

            $this->load($tag)
                 ->setData($entityType, new \Zend_Db_Expr($entityType . ' + 1'))
                 ->save();
        }

        return $model;
    }

    /**
     * @param $entityType
     * @param $entityId
     *
     * @return array
     */
    public function getTagsHash($entityType, $entityId)
    {

        $tags = $this->getTagsRelations($entityType, $entityId);

        $return = [];
        /** @var \Licentia\Panda\Model\Tags $tag */
        foreach ($tags as $tag) {
            $return[$tag->getTagId()] = $tag->getName();
        }

        return $return;
    }

    /**
     * @param $entityType
     * @param $entityId
     *
     * @return array
     */
    public function getTagsValues($entityType, $entityId)
    {

        $tags = $this->getTagsRelations($entityType, $entityId);

        $return = [];
        /** @var \Licentia\Panda\Model\Tags $tag */
        foreach ($tags as $tag) {
            $return[] = ['value' => $tag->getTagId(), 'label' => $tag->getName()];
        }

        return $return;
    }

    /**
     * @param $tagId
     *
     * @return $this
     */
    public function setTagId($tagId)
    {

        return $this->setData('tag_id', $tagId);
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
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {

        return $this->setData('description', $description);
    }

    /**
     * @param $conversionsNumber
     *
     * @return $this
     */
    public function setConversionsNumber($conversionsNumber)
    {

        return $this->setData('conversions_number', $conversionsNumber);
    }

    /**
     * @param $conversionsAmount
     *
     * @return $this
     */
    public function setConversionsAmount($conversionsAmount)
    {

        return $this->setData('conversions_amount', $conversionsAmount);
    }

    /**
     * @param $conversionsAverage
     *
     * @return $this
     */
    public function setConversionsAverage($conversionsAverage)
    {

        return $this->setData('conversions_average', $conversionsAverage);
    }

    /**
     * @param $cost
     *
     * @return $this
     */
    public function setCost($cost)
    {

        return $this->setData('cost', $cost);
    }

    /**
     * @param $subscribers
     *
     * @return $this
     */
    public function setSubscribers($subscribers)
    {

        return $this->setData('subscribers', $subscribers);
    }

    /**
     * @param $campaigns
     *
     * @return $this
     */
    public function setCampaigns($campaigns)
    {

        return $this->setData('campaigns', $campaigns);
    }

    /**
     * @param $autoresponders
     *
     * @return $this
     */
    public function setAutoresponders($autoresponders)
    {

        return $this->setData('autoresponders', $autoresponders);
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
    public function getTagId()
    {

        return $this->getData('tag_id');
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
    public function getDescription()
    {

        return $this->getData('description');
    }

    /**
     * @return mixed
     */
    public function getConversionsNumber()
    {

        return $this->getData('conversions_number');
    }

    /**
     * @return mixed
     */
    public function getConversionsAmount()
    {

        return $this->getData('conversions_amount');
    }

    /**
     * @return mixed
     */
    public function getConversionsAverage()
    {

        return $this->getData('conversions_average');
    }

    /**
     * @return mixed
     */
    public function getCost()
    {

        return $this->getData('cost');
    }

    /**
     * @return mixed
     */
    public function getSubscribers()
    {

        return $this->getData('subscribers');
    }

    /**
     * @return mixed
     */
    public function getCampaigns()
    {

        return $this->getData('campaigns');
    }

    /**
     * @return mixed
     */
    public function getAutoresponders()
    {

        return $this->getData('autoresponders');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
    }
}
