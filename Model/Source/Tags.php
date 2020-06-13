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
 * Class Tags
 *
 * @package Licentia\Panda\Model\Source
 */
class Tags implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $collectionFactory;

    /**
     * Tags constructor.
     *
     * @param \Licentia\Panda\Model\ResourceModel\Tags\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Licentia\Panda\Model\ResourceModel\Tags\CollectionFactory $collectionFactory
    ) {

        $this->collectionFactory = $collectionFactory;
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {

        $collection = $this->collectionFactory->create();

        $return = [];

        foreach ($collection as $item) {
            $return[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $return;
    }
}
