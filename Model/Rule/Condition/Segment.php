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

namespace Licentia\Panda\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Segment
 *
 * @package Licentia\Panda\Model\Rule\Condition
 */
class Segment extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Licentia\Panda\Helper\Data            $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory $segmentsFactory
     * @param Context                                $context
     * @param array                                  $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->segmentsFactory = $segmentsFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        return 'select';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        return 'select';
    }

    /**
     * @return string
     */
    public function getAttributeName()
    {

        return 'Customer Segment';
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {

        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $attributes = [
            'customer_segment' => __('Customer Segment'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {

        if (!$this->hasData('value_select_options')) {
            $options = $this->segmentsFactory->create()->getOptionArray(false);
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {

        $object->setData(
            $this->getAttribute(),
            $this->pandaHelper->isCustomerInSegment($this->getValueParsed())
        );

        return parent::validate($object);
    }
}
