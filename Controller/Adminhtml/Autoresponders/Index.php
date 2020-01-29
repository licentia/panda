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

namespace Licentia\Panda\Controller\Adminhtml\Autoresponders;

/**
 * Class Index
 *
 * @package Licentia\Panda\Controller\Adminhtml\Autoresponders
 */
class Index extends \Licentia\Panda\Controller\Adminhtml\Autoresponders
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected $sendersCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory
     */
    protected $templatesCollection;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory                      $resultPageFactory
     * @param \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection
     * @param \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory   $sendersCollection
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Licentia\Panda\Helper\Data                                     $pandaHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory                     $autorespondersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory               $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory                    $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection,
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $autorespondersFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->templatesCollection = $templatesCollection;
        $this->sendersCollection = $sendersCollection;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();

        $count = $this->sendersCollection->create()->getSize();

        if ($count == 0) {
            $this->messageManager->addWarningMessage(
                __("You haven't added any Sender. You won't be able to create Autoresponders without Senders")
            );
        }

        $templates = $this->templatesCollection->create()
                                               ->addFieldToFilter('is_active', 1)
                                               ->addFieldToFilter('parent_id', ['null' => true])
                                               ->getSize();

        if ($templates == 0) {
            $this->messageManager->addWarningMessage(
                __("You haven't added any Template. You won't be able to create Autoresponders without Templates")
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::autoresponders');
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Autoresponders'));
        $resultPage->addBreadcrumb(__('Sales Automation'), __('Sales Automation'));
        $resultPage->addBreadcrumb(__('Autoresponders'), __('Autoresponders'));

        return $resultPage;
    }
}
