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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

use Magento\Backend\App\Action;

/**
 * Class Preview
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Preview extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ServiceFactory
     */
    protected $service;

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

        return $resultPage;
    }

    /**
     *
     * @param \Magento\Backend\App\Action\Context                                                            $context
     * @param \Magento\Framework\View\Result\PageFactory                                                     $resultPageFactory
     * @param \Magento\Framework\Registry                                                                    $registry
     * @param \Licentia\Panda\Model\CampaignsFactory                                                         $campaignsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory                                                       $subscribersFactory
     * @param \Licentia\Panda\Model\ServiceFactory                                                           $serviceFactory
     * @param \Licentia\Panda\Model\SendersFactory                                                           $sendersFactory
     * @param \Licentia\Panda\Helper\Data                                                                    $pandaHelper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory|\Magento\Framework\Controller\Result\Forward $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory                                               $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory                                                   $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\ServiceFactory $serviceFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $sendersFactory,
            $registry,
            $campaignsFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->pandaHelper = $pandaHelper;
        $this->subscribersFactory = $subscribersFactory;
        $this->service = $serviceFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    function execute()
    {

        parent::execute();

        try {
            $resultPage = $this->_initAction();

            $campaignId = $this->getRequest()->getParam('id');
            $u = $this->getRequest()->getParam('u');
            $sid = $this->getRequest()->getParam('sid');
            /** @var \Licentia\Panda\Model\Campaigns $campaign */
            $campaign = $this->campaignsFactory->create()->load($campaignId);

            if (!$campaign->getId()) {
                return;
            }

            $model = $this->subscribersFactory->create();

            if ($sid) {
                if (filter_var($sid, FILTER_VALIDATE_EMAIL)) {
                    /** @var \Licentia\Panda\Model\Subscribers $subscriber */
                    $subscriber = $model->loadByEmail($sid);
                } else {
                    /** @var \Licentia\Panda\Model\Subscribers $subscriber */
                    $subscriber = $model->loadById($sid);
                }
            } elseif ($u) {
                /** @var \Licentia\Panda\Model\Subscribers $subscriber */
                $subscriber = $model->loadById($u);
            } else {
                /** @var \Licentia\Panda\Model\Subscribers $subscriber */
                $subscriber = new \Magento\Framework\DataObject;
            }

            $this->registry->register('panda_campaign', $campaign, true);
            $this->registry->unregister('panda_subscriber');
            $this->registry->register('panda_subscriber', $subscriber, true);

            if ($campaign->getUrl()) {
                if (!$subscriber->getId()) {
                    $subscriber = $model->getCollection()->getFirstItem();
                }
                $text = $this->pandaHelper->getContentFromUrl($campaign, $subscriber);
            } else {
                $textParse = $this->service->create()
                                           ->getEmailService()->parseDynamicMessageContent($campaign, $subscriber);
                $text = $textParse->getMessage();
            }

            if ($this->getRequest()->getParam('c')) {
                $resultPage->getLayout()
                           ->getBlock('preview_campaign')
                           ->setData('content', $text)
                           ->setTemplate('campaigns/content.phtml');
            }
            $this->registry->register('panda_result', $text, true);

            $resultPage->getConfig()
                       ->getTitle()->prepend(__('Campaigns'));
            $resultPage->getConfig()
                       ->getTitle()->prepend(__('Preview'));
        } catch (\Exception $e) {
            $text = __("Can't preview templates with widgets");

            $this->_view->loadLayout('panda_empty');
            $resultPage->getLayout()
                       ->getBlock('panda.empty')
                       ->setData('content', $text);
        }

        return $resultPage;
    }
}
