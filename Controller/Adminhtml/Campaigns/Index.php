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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

/**
 * Class Index
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Index extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();

        $count = $this->sendersFactory->create()->getCollection()->getSize();

        if ($count == 0) {
            $this->messageManager->addWarningMessage(
                __("You haven't added any Sender. You won't be able to create campaigns without Senders")
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::campaigns');
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Campaigns'));
        $resultPage->addBreadcrumb(__('Sales Automation'), __('Sales Automation'));
        $resultPage->addBreadcrumb(__('Campaigns'), __('Campaigns'));

        #$campaign = $this->_campaignsFactory->create();
        #$campaign->queueEmailCampaigns();
        #$campaign->sendEmails();
        return $resultPage;
    }
}
