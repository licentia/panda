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
