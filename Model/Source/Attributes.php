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
