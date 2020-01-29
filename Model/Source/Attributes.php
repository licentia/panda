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

namespace Licentia\Panda\Model\Source;

/**
 * Class Attributes
 *
 * @package Licentia\Panda\Model\Source
 */
class Attributes
{

    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory
     */
    protected $typeFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $attributeCollection;

    /**
     * @param \Magento\Eav\Model\Entity\TypeFactory                               $typeFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollection
     */
    public function __construct(
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollection
    ) {

        $this->typeFactory = $typeFactory;
        $this->attributeCollection = $attributeCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $type = $this->typeFactory->create()->loadByCode('catalog_product');
        $attributes = $this->attributeCollection->create()->setEntityTypeFilter($type);
        $return = [];
        $return[0] = ['value' => 0, 'label' => __('-- Ignore --')];

        foreach ($attributes as $attribute) {
            if (strlen($attribute['frontend_label']) == 0) {
                continue;
            }
            $return[] = ['value' => $attribute['attribute_code'], 'label' => $attribute['frontend_label']];
        }

        return $return;
    }
}
