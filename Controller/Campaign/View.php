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
 * Class View
 *
 * @package Licentia\Panda\Controller\Campaign
 */
class View extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Framework\App\Action\Context    $context
     * @param \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
     * @param \Licentia\Panda\Model\CampaignsFactory   $campaignsFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory
    ) {

        parent::__construct($context);

        $this->subscribersFactory = $subscribersFactory;
        $this->campaignsFactory = $campaignsFactory;
    }

    /**
     *
     */
    public function execute()
    {

        $resultPage = $this->resultFactory->create();
        $this->_view->loadLayout('panda_empty');

        $campaignId = $this->getRequest()->getParam('c');
        $u = $this->getRequest()->getParam('u');
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->campaignsFactory->create()->load($campaignId);
        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadByCode($u);

        if (!$campaign->getId() || !$subscriber->getId()) {
            header("HTTP/1.0 404 Not Found");
            $text = __('Campaign Not Found');
            $resultPage->getLayout()
                       ->getBlock('panda.empty')
                       ->setData('content', $text);

            return $resultPage;
        }

        $text = $campaign->getMessageForSubscriber($subscriber);

        if (!$text) {
            header("HTTP/1.0 404 Not Found");
            $text = __('Campaign Not Found');
        }

        $resultPage->getLayout()
                   ->getBlock('panda.empty')
                   ->setData('content', $text);

        return $resultPage;
    }
}
