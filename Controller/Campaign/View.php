<?php
/*
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
