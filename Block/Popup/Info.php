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

namespace Licentia\Panda\Block\Popup;

/**
 * Class Info
 *
 * @package Licentia\Panda\Block\Popup
 */
class Info extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected $popupsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Info constructor.
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Checkout\Model\Session                   $checkoutSession
     * @param \Licentia\Panda\Model\PopupsFactory               $popupsFactory
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Cms\Model\Template\FilterProvider        $filterProvider
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Licentia\Panda\Helper\Data                       $helper
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Licentia\Panda\Model\PopupsFactory $popupsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \Licentia\Panda\Helper\Data $helper,
        array $data = []
    ) {

        \Magento\Framework\View\Element\Template::__construct($context, $data);

        $this->priceCurrency = $priceCurrency;
        $this->checkoutSession = $checkoutSession;
        $this->pandaHelper = $helper;
        $this->storeManager = $storeManager;
        $this->popupsFactory = $popupsFactory;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {

        $popupId = $this->getData('popup_id');

        if ($this->getData('identifier')) {
            $popupId = $this->getData('identifier');
        }

        $class = 'info_' . $popupId;

        $this->setTemplate('empty.phtml');
        $jsContent = '';

        if (!$this->pandaHelper->isCacheEnabled()) {
            $this->pandaHelper->registerCurrentScope();

            /** @var \Licentia\Panda\model\Popups $popup */
            $collection = $this->popupsFactory->create()
                                              ->getPopupForDisplay(
                                                  [
                                                      'params'    => $this->getRequest()->getParam('params'),
                                                      'uri'       => $this->getRequest()->getServer('REQUEST_URI'),
                                                      'referer'   => $this->getRequest()->getParam('referer'),
                                                      'useragent' => $this->getRequest()->getServer('HTTP_USER_AGENT'),
                                                  ],
                                                  $this->storeManager->getStore()->getId(),
                                                  $popupId
                                              );
            if ($collection) {
                /** @var \Licentia\Panda\Model\Popups $model */
                foreach ($collection as $model) {
                    if ($model->isActive()) {
                        $vars = [];
                        if (stripos($model->getContent(), '{cart_total}') !== false) {
                            $total = $this->priceCurrency->format(
                                $this->checkoutSession->getQuote()->getSubtotal(),
                                true,
                                \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
                                $this->storeManager->getStore()
                            );

                            $vars['{cart_total}'] = $total;
                        }

                        if (stripos($model->getContent(), '{country}') !== false) {
                            $country = $this->pandaHelper->getCountryName();
                            $vars['{country}'] = $country;

                            if ($country == 'Unknown') {
                                $jsContent = '';
                                break;
                            }
                        }

                        if (stripos($model->getContent(), '{region}') !== false) {
                            $region = $this->pandaHelper->getCountryRegion();
                            $vars['{region}'] = $region;

                            if ($region == '') {
                                $jsContent = '';
                                break;
                            }
                        }

                        if (stripos($model->getContent(), '{city}') !== false) {
                            $city = $this->pandaHelper->getCountryCity();
                            $vars['{city}'] = $city;

                            if ($city == '') {
                                $jsContent = '';
                                break;
                            }
                        }

                        if (stripos($model->getContent(), '{customer_name}') !== false) {
                            $vars['{customer_name}'] = $this->checkoutSession->getQuote()->getCustomerFirstname();
                        }

                        if (stripos($model->getContent(), '{customer_email}') !== false) {
                            $vars['{customer_email}'] = $this->checkoutSession->getQuote()->getCustomerEmail();
                        }

                        if (count($vars)) {
                            $model->setContent(str_replace(array_keys($vars), $vars, $model->getContent()));
                        }

                        $content = $this->filterProvider->getBlockFilter()
                                                        ->setStoreId($this->storeManager->getStore()->getId())
                                                        ->filter($model->getContent());

                        $jsContent = "<div id='panda_{$class}'>" . $content . "</div> ";
                    }
                }
            }

            $this->setContent($jsContent);

        } else {
            $params = [
                'c' => $this->getRequest()
                            ->getControllerName(),
                'a' => $this->getRequest()
                            ->getActionName(),
                'm' => $this->getRequest()
                            ->getModuleName(),
                'i' => $this->getRequest()->getParam('id', 0),
            ];

            $paramsEncode = json_encode($params);

            $url = $this->getUrl('panda/block/get');

            $jsContent = "<script type='text/javascript'>
                        require(['jquery', 'domReady!'], function ($) {";

            if ($this->pandaHelper->delayAjaxLoads()) {
                $jsContent .= "      var timePopupBlock = setInterval(function () {
                                if (localStorage['mage-cache-storage'] !== '{}') {
                                    clearInterval(timePopupBlock);";
            }

            $jsContent .= "         $.ajax({
                                        url: '{$url}',
                                        type: 'POST',
                                        context: document.body,
                                        success: function (responseText) {
                                            if (responseText.length > 0) {
                                                $('#panda_{$class}').html(responseText).show(200);
                                            }
                                        },
                                        data: {
                                            'id': '{$popupId}',
                                            'params': '$paramsEncode',
                                            'identifier': window.location.pathname,
                                            'referer': document.referrer
                                        }
                                    });";
            if ($this->pandaHelper->delayAjaxLoads()) {
                $jsContent .= "  }
                            }, 500);";
            }
            $jsContent .= "         });
                    </script>
                    <div id='panda_{$class}'></div> ";

            $this->setContent($jsContent);
        }

        return parent::_toHtml();
    }
}
