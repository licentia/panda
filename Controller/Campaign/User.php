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

namespace Licentia\Panda\Controller\Campaign;

/**
 * Class User
 *
 * @package Licentia\Panda\Controller\Campaign
 */
class User extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\Service\ServiceAbstractFactory
     */
    protected $serviceAbstract;

    /**
     * @param \Magento\Framework\App\Action\Context                $context
     * @param \Magento\Framework\Registry                          $coreRegistry
     * @param \Licentia\Panda\Helper\Data                          $pandaHelper
     * @param \Licentia\Panda\Model\Service\ServiceAbstractFactory $serviceAbstract
     * @param \Licentia\Panda\Model\SubscribersFactory             $subscribersFactory
     * @param \Licentia\Panda\Model\CampaignsFactory               $campaignsFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\Service\ServiceAbstractFactory $serviceAbstract,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory
    ) {

        parent::__construct($context);

        $this->subscribersFactory = $subscribersFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->registry = $coreRegistry;
        $this->serviceAbstract = $serviceAbstract;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {

        $resultPage = $this->resultFactory->create();

        $campaignId = $this->getRequest()->getParam('c');
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
            $subscriber = new \Magento\Framework\DataObject;
        }

        #$GLOBALS['panda_subscriber'] = $subscriber;
        $this->registry->unregister('panda_campaign');
        $this->registry->register('panda_campaign', $campaign, true);
        $this->registry->register('panda_subscriber', $subscriber, true);

        if ($campaign->getUrl()) {
            $text = $this->pandaHelper->getContentFromUrl($campaign, $subscriber);
        } else {
            $textParse = $this->serviceAbstract->create()->parseDynamicMessageContent($campaign, $subscriber);
            $text = $textParse->getMessage();
        }

        $this->_view->loadLayout('panda_empty');
        $resultPage->getLayout()
                   ->getBlock('panda.empty')
                   ->setData('content', $text);

        return $resultPage;
    }
}
