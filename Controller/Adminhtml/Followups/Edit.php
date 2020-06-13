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

namespace Licentia\Panda\Controller\Adminhtml\Followups;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Followups
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\Followups
{

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {

        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::followups')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Manage Follow Ups'), __('Manage Follow Ups'));

        return $resultPage;
    }

    /**
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Helper\Data                       $pandaHelper
     * @param \Licentia\Panda\Model\FollowupFactory             $followupFactory
     * @param \Licentia\Panda\Model\CampaignsFactory            $campaignsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     *
     * @internal param \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\FollowupFactory $followupFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $followupFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->campaignsFactory = $campaignsFactory;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');
        $cid = $this->getRequest()->getParam('cid');
        $model = $this->registry->registry('panda_followup');

        if ($cid || $model->getCampaignId()) {
            /** @var \Licentia\Panda\Model\Campaigns $campaign */
            $campaign = $this->campaignsFactory->create()
                                               ->load(
                                                   isset($cid) ? $cid :
                                                       $model->getCampaignId()
                                               );
        } else {
            $campaign = new \Magento\Framework\DataObject;
        }

        $this->registry->register('panda_campaign', $campaign, true);

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Followup no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Followup') : __('New Followup'),
            $id ? __('Edit Followup') : __('New Followup')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Follow Ups'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New Follow Up'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Followups\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Followups\Edit\Tabs')
                   );

        return $resultPage;
    }
}
