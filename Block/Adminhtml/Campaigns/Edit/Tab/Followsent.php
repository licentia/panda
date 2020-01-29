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

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Followsent
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Followsent extends \Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Children
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
