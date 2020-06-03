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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Panda\Model;

use Licentia\Panda\Api\PopupsRepositoryInterface;
use Licentia\Panda\Model\ResourceModel\Popups\CollectionFactory as PopupsCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class PopupsRepository
 *
 * @package Licentia\Panda\Model
 */
class PopupsRepository implements PopupsRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var PopupsFactory
     */
    protected $popupsFactory;

    /**
     * @var PopupsCollectionFactory
     */
    protected $popupsCollectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * PopupsRepository constructor.
     *
     * @param \Licentia\Panda\Helper\Data                       $pandaHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ProductFactory             $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory            $categoryFactory
     * @param \Magento\Customer\Model\Session                   $customerSession
     * @param \Magento\Checkout\Model\Session                   $checkoutSession
     * @param \Magento\Framework\App\RequestInterface           $request
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Customer\Model\CustomerFactory           $customerFactory
     * @param PopupsFactory                                     $popupsFactory
     * @param PopupsCollectionFactory                           $popupsCollectionFactory
     * @param DataObjectHelper                                  $dataObjectHelper
     * @param DataObjectProcessor                               $dataObjectProcessor
     * @param \Magento\Cms\Model\Template\FilterProvider        $filterProvider
     * @param StoreManagerInterface                             $storeManager
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        PopupsFactory $popupsFactory,
        PopupsCollectionFactory $popupsCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        StoreManagerInterface $storeManager
    ) {

        $this->priceCurrency = $priceCurrency;
        $this->filterProvider = $filterProvider;

        $this->pandaHelper = $pandaHelper;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;

        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->registry = $registry;
        $this->customerFactory = $customerFactory;
        $this->popupsFactory = $popupsFactory;
        $this->popupsCollectionFactory = $popupsCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayWindows($zone, $identifier, $customerId = null)
    {

        $result = [];
        parse_str($zone, $result);

        $result = $result['zone'];

        $this->customerSession->loginById($customerId);
        $this->checkoutSession->loadCustomerQuote();

        if (null === $customerId && $this->request->getParam('customerId')) {
            $customerId = $this->request->getParam('customerId');
        }

        $customer = $this->customerFactory->create()->load($customerId);
        $this->registry->register('current_customer', $customer);

        $storeId = $this->storeManager->getStore()->getId();

        $this->pandaHelper->registerCurrentScope();

        $data = [];
        $data['params'] = $result;
        $data['params']['m'] = 'catalog';
        $data['params']['c'] = 'product';
        $data['params']['a'] = 'view';
        $data['params']['uri'] = $this->request->getParam('identifier');
        $data['params']['referer'] = $this->request->getParam('referer');

        $data = array_merge_recursive($result, $data);

        if ($data['params']['m'] == 'catalog' &&
            $data['params']['c'] == 'category' &&
            $data['params']['a'] == 'view' &&
            isset($data['params']['i'])) {
            $category = $this->categoryFactory->create()->load($data['params']['id']);

            $this->registry->register('current_category', $category);
            $this->registry->register('category', $category);
        } elseif ($data['params']['m'] == 'catalog' &&
                  $data['params']['c'] == 'product' &&
                  $data['params']['a'] == 'view' &&
                  isset($data['params']['i'])) {
            $product = $this->productFactory->create()->load($data['params']['i']);

            $this->registry->register('current_product', $product);
            $this->registry->register('product', $product);
        }

        $data['params'] = json_encode($data['params']);

        $collection = $this->popupsFactory->create()->getPopupForDisplay($data, $storeId, $identifier);

        if (is_array($collection)) {

            /** @var Popups $model */
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
                            return '';
                        }
                    }

                    if (stripos($model->getContent(), '{region}') !== false) {
                        $region = $this->pandaHelper->getCountryRegion();
                        $vars['{region}'] = $region;

                        if ($region == '') {
                            return '';
                        }
                    }

                    if (stripos($model->getContent(), '{city}') !== false) {
                        $city = $this->pandaHelper->getCountryCity();
                        $vars['{city}'] = $city;

                        if ($city == '') {
                            return '';
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

                    try {
                        $content = $this->filterProvider->getBlockFilter()
                                                        ->setStoreId($this->storeManager->getStore()->getId())
                                                        ->filter($model->getContent());
                    } catch (\Exception $e) {
                        $content = '';
                    }

                    return $content;
                }
            }
        }

        return '';
    }
}
