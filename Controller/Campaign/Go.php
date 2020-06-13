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

namespace Licentia\Panda\Controller\Campaign;

/**
 * Class Go
 *
 * @package Licentia\Panda\Controller\Campaign
 */
class Go extends \Magento\Framework\App\Action\Action
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
     * @var \Licentia\Panda\Model\Session
     */
    protected $pandaSession;

    /**
     * @var \Licentia\Panda\Model\UrlsFactory
     */
    protected $urlsFactory;

    /**
     * @var \Licentia\Panda\Model\StatsFactory
     */
    protected $statsFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $checkoutCart;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeInterface;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $decoderInterface;

    /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Framework\Url\DecoderInterface    $decoderInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Licentia\Panda\Helper\Data                $pandaHelper
     * @param \Licentia\Panda\Model\Session              $session
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Cart               $cart
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
     * @param \Licentia\Panda\Model\SubscribersFactory   $subscribersFactory
     * @param \Licentia\Panda\Model\StatsFactory         $statsFactory
     * @param \Licentia\Panda\Model\UrlsFactory          $urlsFactory
     * @param \Licentia\Panda\Model\CampaignsFactory     $campaignsFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Url\DecoderInterface $decoderInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Registry $coreRegistry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\Session $session,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\StatsFactory $statsFactory,
        \Licentia\Panda\Model\UrlsFactory $urlsFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory
    ) {

        parent::__construct($context);

        $this->subscribersFactory = $subscribersFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->registry = $coreRegistry;
        $this->pandaSession = $session;
        $this->customerSession = $customerSession;
        $this->statsFactory = $statsFactory;
        $this->urlsFactory = $urlsFactory;
        $this->checkoutCart = $cart;
        $this->customerFactory = $customerFactory;
        $this->storeInterface = $storeManagerInterface;
        $this->decoderInterface = $decoderInterface;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $request = $this->getRequest();
        $subscriberId = $request->getParam('u');
        $campaignId = $request->getParam('c');

        $url = $this->decoderInterface->decode($request->getParam('url'));
        $this->registry->register('panda_open_url', $url);

        if (!$campaignId && !$subscriberId) {
            header('LOCATION:' . $url);

            return;
        }

        $session = $this->pandaSession;
        $session->start();
        $session->setPandaConversion(true);
        $session->setPandaConversionCampaign($campaignId);
        $session->setPandaConversionSubscriber($subscriberId);
        $session->setPandaConversionUrl($url);

        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->campaignsFactory->create()->load($campaignId);

        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadById($subscriberId);
        $session->setPandaSubscriber($subscriberId);

        if ($subscriber->getId()) {
            $this->pandaHelper->addIdentifierValueFromArea('subscriber_code', $subscriber->getCode());
        }

        $this->_eventManager->dispatch(
            'panda_click_link',
            [
                'link'       => $url,
                'campaign'   => $campaign,
                'subscriber' => $subscriber,
            ]
        );

        $this->statsFactory->create()->logClicks($campaign, $subscriber);
        $this->urlsFactory->create()->logUrl($campaign, $subscriber, $url);

        $request->setParam('u', null);
        $request->setParam('c', null);

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            //0 - id //1 - email
            $csv = $this->pandaHelper->decrypt($id, true);
            $customerInfo = explode(';', $csv);
            $customer = $this->customerFactory->create()
                                              ->setWebsiteId(
                                                  $this->storeInterface->getWebsite()
                                                                       ->getId()
                                              )
                                              ->load($customerInfo[0]);

            if ($customer->getId() && isset($customerInfo[1]) && $customer->getEmail() == trim($customerInfo[1])) {
                if ($this->customerSession->isLoggedIn() &&
                    $customer->getId() != $this->customerSession->getCustomerId()
                ) {
                    $this->customerSession->logout();
                    $this->customerSession->regenerateId();
                    $this->customerSession->setCustomerAsLoggedIn($customer);
                } elseif (!$this->customerSession->isLoggedIn()) {
                    $this->customerSession->regenerateId();
                    $this->customerSession->setCustomerAsLoggedIn($customer);
                }
            } else {
                $this->loadCart($campaign, $subscriber);
            }
        }

        return $this->resultRedirectFactory->create()->setUrl($url);
    }

    /**
     * @param \Licentia\Panda\Model\Campaigns   $campaign
     * @param \Licentia\Panda\Model\Subscribers $subscriber
     */
    public function loadCart(\Licentia\Panda\Model\Campaigns $campaign, \Licentia\Panda\Model\Subscribers $subscriber)
    {

        if (!$campaign->getId() || !$subscriber->getId() || !$campaign->getAutoresponderId()) {
            return;
        }

        $cart = $this->checkoutCart;
        //$cart->init();
        if ($cart->getItemsCount() > 0) {
            return;
        }

        $quote = $this->pandaHelper->getSubscriberQuote($subscriber->getEmail());

        if (!$quote) {
            return; //No products
        }

        $cart->setQuote($quote);
        $cart->save();
    }
}
