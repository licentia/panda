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

namespace Licentia\Panda\Model\Popups\Condition;

/**
 * Class Product
 *
 * @package Licentia\Panda\Model\Popups\Condition
 */
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Rule\Model\Condition\Context                            $context
     * @param \Magento\Backend\Helper\Data                                     $backendData
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Catalog\Model\ProductFactory                            $productFactory
     * @param \Magento\Eav\Model\Config                                        $config
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                  $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product                     $productResource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface                        $localeFormat
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {

        $this->registry = $registry;

        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        if ($this->getAttribute() === 'type_id') {
            return 'select';
        }

        return parent::getInputType();
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {

        if ($this->getAttribute() === 'type_id') {
            return 'select';
        }

        return parent::getValueElementType();
    }

    /**
     * @return $this
     */
    protected function _prepareValueOptions()
    {

        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        $selectOptions = null;
        if ($this->getAttribute() === 'type_id') {
            $selectOptions[] = ['label' => 'Simple Product', 'value' => 'simple'];
            $selectOptions[] = ['label' => 'Downloadable Product', 'value' => 'downloadable'];
            $selectOptions[] = ['label' => 'Virtual Product', 'value' => 'virtual'];
            $selectOptions[] = ['label' => 'Bundle Product', 'value' => 'bundle'];
            $selectOptions[] = ['label' => 'Configurable Product', 'value' => 'configurable'];
            $selectOptions[] = ['label' => 'Grouped Product', 'value' => 'grouped'];
        }
        $this->_setSelectOptions($selectOptions, $selectReady, $hashedReady);

        return parent::_prepareValueOptions();
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        $attributes['type_id'] = __('Current Product Type');
        foreach ($productAttributes as $attribute) {
            /* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            if (!$attribute->isAllowedForRuleCondition() || !$attribute->getDataUsingMethod(
                    $this->_isUsedForRuleProperty
                )
            ) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = __('Current Product') . ' ' . $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Validate Product Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {

        if (!$product = $this->registry->registry('product')) {
            return false;
        }

        return parent::validate($product);
    }
}
