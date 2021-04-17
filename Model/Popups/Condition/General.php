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

namespace Licentia\Panda\Model\Popups\Condition;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Rule\Model\Condition\Context;

/**
 * Class General
 *
 * @package Licentia\Panda\Model\Popups\Condition
 */
class General extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var RedirectInterface
     */
    protected $redirectInterface;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $countryCollection;

    /**
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected $popupsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * General constructor.
     *
     * @param \Magento\Framework\Event\ManagerInterface                 $eventDispatcher
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Licentia\Panda\Model\PopupsFactory                       $popupsFactory
     * @param \Magento\Checkout\Model\Session                           $checkoutSession
     * @param RedirectInterface                                         $redirect
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     * @param Session                                                   $customerSession
     * @param \Magento\Framework\UrlInterface                           $urlInterface
     * @param \Magento\Framework\Registry                               $registry
     * @param \Magento\Store\Model\System\Store                         $systemStore
     * @param \Licentia\Panda\Helper\Data                               $helper
     * @param Context                                                   $context
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventDispatcher,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Licentia\Panda\Model\PopupsFactory $popupsFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        RedirectInterface $redirect,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        Session $customerSession,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\System\Store $systemStore,
        \Licentia\Panda\Helper\Data $helper,
        Context $context,
        array $data = []
    ) {

        $this->storeManager = $storeManager;
        $this->popupsFactory = $popupsFactory;
        $this->countryCollection = $countryCollection;
        $this->checkoutSession = $checkoutSession;
        $this->redirectInterface = $redirect;
        $this->urlInterface = $urlInterface;
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        $this->systemStore = $systemStore;
        $this->pandaHelper = $helper;
        $this->eventManager = $eventDispatcher;

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $attributes = [
            'url'           => (string) __('Current Relative URL (no /)'),
            'views'         => (string) __('Current Pages Views'),
            'section'       => (string) __('Current Store Section'),
            'referer'       => (string) __('Current Referrer Url'),
            'useragent'     => (string) __('Current User Agent'),
            'day'           => (string) __('Current Day of the Week'),
            'hour'          => (string) __('Current Hour of the Day'),
            'country'       => (string) __('Current Geo-Location - Country Name'),
            'region'        => (string) __('Current Geo-Location - Region Name'),
            'region_code'   => (string) __('Current Geo-Location - Region Code'),
            'global_price'  => (string) __('Current Product Price + Shopping Cart Subtotal'),
            'global_weight' => (string) __('Current Product Weight + Shopping Cart Weight'),
            'cart_price'    => (string) __('Shopping Cart Subtotal'),
            'cart_weight'   => (string) __('Shopping Cart Weight'),
            'registry'      => (string) __('Magento - Registry keys exist (bool evaluation)'),
        ];

        asort($attributes);

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        switch ($this->getAttribute()) {
            case 'section':
            case 'day':
            case 'country':
            case 'hour':
                return 'select';
        }

        return 'text';
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {

        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country':
                    $options = $this->getCountryList();
                    break;
                case 'day':
                    $options = [
                        0 => (string) __('Sunday'),
                        1 => (string) __('Monday'),
                        2 => (string) __('Tuesday'),
                        3 => (string) __('Wednesday'),
                        4 => (string) __('Thursday'),
                        5 => (string) __('Friday'),
                        6 => (string) __('Saturday'),
                    ];
                    break;
                case 'hour':
                    $options = array_combine(range(1, 24), range(1, 24));
                    break;
                case 'section':
                    $options = [
                        'homepage'         => (string) __('Homepage'),
                        'products'         => (string) __('Products'),
                        'categories'       => (string) __('Categories'),
                        'cms'              => (string) __('CMS Pages'),
                        'checkout_process' => (string) __('Checkout - Process'),
                        'checkout_cart'    => (string) __('Checkout - Cart'),
                        'search'           => (string) __('Search Results'),
                        'wishlist'         => (string) __('Wish List'),
                        'address_book'     => (string) __('Account Address Book'),
                        'customer_reviews' => (string) __('Account Reviews'),
                        'account_edit'     => (string) __('Account Edit'),
                        'orders_history'   => (string) __('Account Order History'),
                    ];
                    break;
                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {

        $infoData = $this->registry->registry('panda_popup_data');

        if (!isset($infoData['params']['c'])) {
            $infoData['params']['c'] = '';
        }

        if (!isset($infoData['params']['m'])) {
            $infoData['params']['m'] = '';
        }

        if (!isset($infoData['params']['a'])) {
            $infoData['params']['a'] = '';
        }

        if ($this->getAttribute() == 'global_price') {

            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->registry->registry('current_product');

            if (!$product || !$product->getPrice()) {
                return false;
            }

            $amount = $this->checkoutSession->getQuote()->getBaseSubtotal() + $product->getPrice();

            $model->setData('global_price', $amount);
        }

        if ($this->getAttribute() == 'global_weight') {

            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->registry->registry('current_product');

            if (!$product || $product->getPrice()) {
                return false;
            }

            $weight = $this->checkoutSession->getQuote()->getShippingAddress()->getWeight() + $product->getWeight();

            $model->setData('global_weight', $weight);
        }

        if ($this->getAttribute() == 'cart_weight') {
            $model->setData(
                'cart_weight',
                $this->checkoutSession->getQuote()
                                      ->getShippingAddress()
                                      ->getWeight()
            );
        }
        if ($this->getAttribute() == 'cart_total') {
            $model->setData(
                'cart_total',
                $this->checkoutSession->getQuote()
                                      ->getBaseGrandTotal()
            );
        }

        if ($this->getAttribute() == 'day') {
            $model->setData('day', $this->pandaHelper->gmtDate('w'));
        }

        if ($this->getAttribute() == 'hour') {
            $model->setData('hour', $this->pandaHelper->gmtDate('H'));
        }

        if ($this->getAttribute() == 'registry') {
            $model->setData('registry', (bool) $this->registry->registry($this->getValueParsed()));
        }

        if ($this->getAttribute() == 'referer') {
            $model->setData('referer', isset($infoData['referer']) ? $infoData['referer'] : null);
        }

        if ($this->getAttribute() == 'url') {
            $model->setData('url', trim($infoData['uri'], '/'));
        }

        if ($this->getAttribute() == 'useragent') {
            $model->setData('useragent', $infoData['useragent']);
        }

        if ($this->getAttribute() == 'views') {
            $model->setData('views', $this->customerSession->getData('panda_popups_everywhere'));
        }

        if ($this->getAttribute() == 'country') {
            $model->setData('country', $this->pandaHelper->getCountryName());
        }

        if ($this->getAttribute() == 'region') {
            $this->setValueParsed(strtolower($this->getValueParsed()));
            $model->setData('region', strtolower($this->pandaHelper->getCountryRegion()));
        }

        if ($this->getAttribute() == 'region_code') {
            $this->setValueParsed(strtolower($this->getValueParsed()));
            $model->setData('region_code', strtolower($this->pandaHelper->getCountryRegionCode()));
        }

        if ($this->getAttribute() == 'section') {
            $location = '';

            if ($infoData['params']['m'] == 'checkout' &&
                $infoData['params']['c'] == 'index') {
                $location = 'checkout_process';
            } elseif ($infoData['params']['m'] == 'checkout' &&
                      $infoData['params']['c'] == 'cart' &&
                      $infoData['params']['a'] == 'index') {
                $location = 'checkout_cart';
            } elseif ($infoData['params']['m'] == 'catalogsearch') {
                $location = 'search';
            } elseif ($infoData['params']['m'] == 'cms' &&
                      $infoData['params']['c'] == 'index' &&
                      $infoData['params']['a'] == 'index') {
                $location = 'homepage';
            } else {
                if ($infoData['params']['m'] == 'catalog' &&
                    $infoData['params']['c'] == 'category' &&
                    $infoData['params']['a'] == 'view') {
                    $location = 'categories';
                } elseif ($infoData['params']['m'] == 'catalog' &&
                          $infoData['params']['c'] == 'product' &&
                          $infoData['params']['a'] == 'view') {
                    $location = 'products';
                } elseif ($infoData['params']['m'] == 'wishlist' &&
                          $infoData['params']['c'] == 'index') {
                    $location = 'wishlist';
                } elseif ($infoData['params']['m'] == 'customer' &&
                          $infoData['params']['c'] == 'address') {
                    $location = 'address_book';
                } elseif ($infoData['params']['m'] == 'review' &&
                          $infoData['params']['c'] == 'customer') {
                    $location = 'customer_reviews';
                } elseif ($infoData['params']['m'] == 'customer' &&
                          $infoData['params']['c'] == 'account' &&
                          $infoData['params']['a'] == 'edit') {
                    $location = 'account_edit';
                } elseif ($infoData['params']['m'] == 'sales' &&
                          $infoData['params']['c'] == 'order' &&
                          $infoData['params']['a'] == 'history') {
                    $location = 'orders_history';
                } elseif ($infoData['params']['m'] == 'cms') {
                    $location = 'cms';
                }
            }

            $model->setData('section', $location);
        }

        $return = new \Magento\Framework\DataObject();
        $this->eventManager->dispatch(
            'panda_popups_condition_general',
            [
                'checkout_session' => $this->checkoutSession,
                'product'          => $this->registry->registry('current_product'),
                'condition'        => $this,
                'model'            => $model,
                'info_data'        => $this->registry->registry('panda_popup_data'),
                'return'           => $return,
            ]
        );

        if ($return->hasData('valid') && $return->getValid() === false) {
            return false;
        }

        return parent::validate($model);
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        if (in_array($this->getAttribute(), ['registry'])) {
            return 'boolean';
        }
        if (in_array($this->getAttribute(), ['section', 'day', 'hour', 'global_price', 'global_weight'])) {
            return 'numeric';
        }
        if (in_array($this->getAttribute(), ['section', 'country'])) {
            return 'select';
        }

        return 'string';
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {

        $attribute = $this->getAttribute();
        $attributes = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function getCountryList()
    {

        $data = [];
        $cList = $this->countryCollection->loadData()->toOptionArray(false);
        foreach ($cList as $key => $value) {
            $data[$value['label']] = $value['label'];
        }

        ksort($data);

        return $data;
    }
}
