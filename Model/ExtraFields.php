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
 * ExtraFields model
 *
 */
class ExtraFields extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_extra';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'extra';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Panda\Model\ResourceModel\ExtraFields::class);
    }

    /**
     * @return bool
     */
    public function usesSource()
    {

        return false;
    }

    /**
     * @param bool $first
     *
     * @return array
     */
    public function getOptionArray($first = false)
    {

        $extraFields = $this->getCollection();

        $return = [];

        if ($first) {
            $return[] = ['value' => '0', 'label' => $first];
        }

        foreach ($extraFields as $extraField) {
            $return[] = ['value' => $extraField->getId(), 'label' => $extraField->getName()];
        }

        return $return;
    }

    /**
     * @param bool $first
     *
     * @return array
     */
    public function toOptionHash($first = false)
    {

        $options = $this->getOptionArray($first);

        $return = [];

        foreach ($options as $option) {
            $return[$option['value']] = $option['label'];
        }

        return $return;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterDelete()
    {

        $entryCode = $this->getEntryCode();

        $this->getResource()
             ->getConnection()
             ->update(
                 $this->getResource()
                      ->getTable('panda_subscribers'),
                 ['field_' . $entryCode => new \Zend_Db_Expr('NULL')]
             );

        return parent::afterDelete();
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function beforeSave()
    {

        if (!$this->getId()) {
            $collection = $this->getCollection();

            $elements = $collection->setOrder('entry_code', 'ASC')
                                   ->getData();

            $fields = array_combine(
                range(1, Subscribers::MAX_NUMBER_EXTRA_FIELDS),
                range(1, Subscribers::MAX_NUMBER_EXTRA_FIELDS)
            );
            foreach ($elements as $element) {
                unset($fields[$element['entry_code']]);
            }

            $entryCode = reset($fields);

            $this->setData('entry_code', $entryCode);
        }

        return parent::beforeSave();
    }

    /**
     * @param $extraId
     *
     * @return $this
     */
    public function setExtraId($extraId)
    {

        return $this->setData('extra_id', $extraId);
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
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {

        return $this->setData('type', $type);
    }

    /**
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {

        return $this->setData('options', $options);
    }

    /**
     * @param $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {

        return $this->setData('default_value', $defaultValue);
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
     * @param $entryCode
     *
     * @return $this
     */
    public function setEntryCode($entryCode)
    {

        return $this->setData('entry_code', $entryCode);
    }

    /**
     * @return mixed
     */
    public function getExtraId()
    {

        return $this->getData('extra_id');
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
    public function getType()
    {

        return $this->getData('type');
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {

        return $this->getData('options');
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {

        return $this->getData('default_value');
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
    public function getEntryCode()
    {

        return $this->getData('entry_code');
    }
}
