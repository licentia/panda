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

namespace Licentia\Panda\Controller\Block;

/**
 * Class Get
 *
 * @package Licentia\Panda\Controller
 */
class Get extends \Magento\Framework\App\Action\Action
{

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
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Get constructor.
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Checkout\Model\Session                   $checkoutSession
     * @param \Licentia\Panda\Helper\Data                       $helper
     * @param \Licentia\Panda\Model\PopupsFactory               $popupsFactory
     * @param \Magento\Framework\App\Action\Context             $context
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Cms\Model\Template\FilterProvider        $filterProvider
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Licentia\Panda\Helper\Data $helper,
        \Licentia\Panda\Model\PopupsFactory $popupsFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {

        parent::__construct($context);

        $this->priceCurrency = $priceCurrency;
        $this->checkoutSession = $checkoutSession;
        $this->pandaHelper = $helper;
        $this->storeManager = $storeManager;
        $this->popupsFactory = $popupsFactory;
        $this->filterProvider = $filterProvider;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        $id = $this->getRequest()->getParam('id');

        $this->pandaHelper->registerCurrentScope();

        /** @var \Licentia\Panda\model\Popups $popup */
        $collection = $this->popupsFactory->create()
                                          ->getPopupForDisplay(
                                              [
                                                  'params'    => $this->getRequest()->getParam('params'),
                                                  'uri'       => $this->getRequest()->getParam('identifier'),
                                                  'referer'   => $this->getRequest()->getParam('referer'),
                                                  'useragent' => $this->getRequest()->getServer('HTTP_USER_AGENT'),
                                              ],
                                              $this->storeManager->getStore()->getId(),
                                              $id
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
                            return $result->setData('');
                        }
                    }

                    if (stripos($model->getContent(), '{region}') !== false) {
                        $region = $this->pandaHelper->getCountryRegion();
                        $vars['{region}'] = $region;

                        if ($region == '') {
                            return $result->setData('');
                        }
                    }

                    if (stripos($model->getContent(), '{city}') !== false) {
                        $city = $this->pandaHelper->getCountryCity();
                        $vars['{city}'] = $city;

                        if ($city == '') {
                            return $result->setData('');
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

                    return $result->setData($content);
                }
            }
        }

        return $result->setData([]);
    }
}
