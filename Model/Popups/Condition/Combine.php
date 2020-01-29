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

use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 *
 * @package Licentia\Panda\Model\Popups\Condition
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Equity\Model\Segments\Condition\ActivityFactory
     */
    protected $activityFactory;

    /**
     * @var \Licentia\Equity\Model\Segments\Condition\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var GeneralFactory
     */
    protected $generalFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Address
     */
    protected $conditionAddress;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Address           $conditionAddress
     * @param \Magento\Framework\Registry                               $registry
     * @param \Licentia\Panda\Helper\Data                               $pandaHelper
     * @param GeneralFactory                                            $generalFactory
     * @param \Licentia\Equity\Model\Segments\Condition\CustomerFactory $customerFactory
     * @param \Licentia\Equity\Model\Segments\Condition\ActivityFactory $activityFactory
     * @param ProductFactory                                            $productFactory
     * @param Context                                                   $context
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        GeneralFactory $generalFactory,
        \Licentia\Equity\Model\Segments\Condition\CustomerFactory $customerFactory,
        \Licentia\Equity\Model\Segments\Condition\ActivityFactory $activityFactory,
        ProductFactory $productFactory,
        Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->setType('Licentia\Panda\Model\Popups\Condition\Combine');

        $this->pandaHelper = $pandaHelper;
        $this->generalFactory = $generalFactory;
        $this->conditionAddress = $conditionAddress;
        $this->customerFactory = $customerFactory;
        $this->activityFactory = $activityFactory;
        $this->productFactory = $productFactory;

        $this->registry = $registry;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getNewChildSelectOptions()
    {

        $conditions = parent::getNewChildSelectOptions();

        $addressAttributes = $this->conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Licentia\Panda\Model\Popups\Condition\Combine',
                    'label' => __('Conditions combination'),
                ],
                ['label' => __('Cart Attribute'), 'value' => $attributes],
            ]
        );

        $searches = $this->generalFactory->create();
        $searchAttributes = $searches->loadAttributeOptions()->getAttributeOption();
        $cAttributes = [];
        foreach ($searchAttributes as $code => $label) {
            $cAttributes[] = [
                'value' => 'Licentia\Panda\Model\Popups\Condition\General|' . $code,
                'label' => $label,
            ];
        }
        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Store Information'), 'value' => $cAttributes],
            ]
        );

        $productCondition = $this->customerFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        foreach ($productAttributes as $code => $label) {
            $pAttributes[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Customer|' . $code,
                'label' => $label,
            ];
        }
        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Current Customer Attribute'), 'value' => $pAttributes],
            ]
        );

        $productCondition = $this->productFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        foreach ($productAttributes as $code => $label) {
            $pAttributes[] = [
                'value' => 'Licentia\Panda\Model\Popups\Condition\Product|' . $code,
                'label' => $label,
            ];
        }
        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Current Product Attribute'), 'value' => $pAttributes],
            ]
        );

        $addressActivity = $this->activityFactory->create();
        $activityAttributes = $addressActivity->loadAttributeOptions()->getAttributeOption();
        $attributesActivity = [];
        foreach ($activityAttributes as $code => $label) {
            $attributesActivity[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Activity|' . $code,
                'label' => $label,
            ];
        }

        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Customer Activity'), 'value' => $attributesActivity],
            ]
        );

        return $conditions;
    }

    /**
     * @param $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {

        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
