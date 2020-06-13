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

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Followsent
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Followsent extends Children
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Magento\Framework\Registry                                     $coreRegistry
     * @param \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                     $registry
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $backendHelper,
            $collectionFactory,
            $registry,
            $data
        );

        $this->registry = $coreRegistry;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        parent::_prepareCollection();
        /** @var \Licentia\Panda\Model\ResourceModel\Followup\Collection $followup */
        $followup = $this->registry->registry('panda_followup_collection');

        $collection = $this->collectionFactory->create()
                                              ->addFieldToFilter('followup_id', ['in' => $followup->getAllIds()]);

        $this->setCollection($collection);

        return $this;
    }
}
