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

namespace Licentia\Panda\Controller\Adminhtml;

/**
 * Autoresponders controller
 */
class Autoresponders extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Panda::autoresponders';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected ?\Magento\Framework\Registry $registry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected \Magento\Framework\View\Result\LayoutFactory $layoutFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Helper\Data                       $pandaHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory       $autorespondersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct($context);

//        $i = 0;
//
//        if ($i) {
//            ini_set('display_errors', 1);
//            error_reporting(-1);
//
//            $manager = \Magento\Framework\App\ObjectManager::getInstance();
//            #$manager->create('\Licentia\Reports\Cron\RebuildSalesStatsForYesterday')->execute();
//            #$manager->get('Licentia\Panda\Model\Splits')->cron();
//
//            /** @var \Magento\Directory\Model\Country $country */
//            $country = $manager->create('\Magento\Directory\Model\Country');
//
//            /** @var \Magento\Sales\Model\Order $order */
//            $ordersCollection = $manager->create('Magento\Sales\Model\Order')
//                                        ->getCollection()
//                                        ->addFieldToFilter('customer_email', ['like' => '%niepoort%'])
//                                        ->setPageSize(5);
//            $csv = [];
//
//            $finalFile = BP . '/var/export/orders.csv';
//
//            $fp = fopen($finalFile, 'w');
//            $fieldSeparator = ';';
//
//            $lines = [];
//            $orders = [];
//            foreach ($ordersCollection as $order) {
//
//                /** @var \Magento\Sales\Model\Order\Item $item */
//                foreach ($order->getAllVisibleItems() as $item) {
//                    $lines[] = [
//                        'Artigo'     => $item->getSku(),
//                        'Quantidade' => $item->getQtyOrdered(),
//                        'Preco'      => $item->getPrice(),
//                        'TotalLinha' => $item->getBaseRowTotal(),
//                        'TotalIEC'   => 1,
//                    ];
//                }
//
//                $street = implode(' ', explode("\n", $order->getShippingAddress()->getData('street')));
//                $orders[] = [
//                    'NumEncMagento'   => $order->getIncrementId(),
//                    'Data'            => $order->getCreatedAt(),
//                    'TotalMercadoria' => $order->getBaseSubtotal() + $order->getBaseShippingAmount(),
//                    'TotalDocumento'  => $order->getBaseGrandTotal(),
//                    'TotalIva'        => $order->getBaseTaxAmount(),
//                    'NIF'             => $order->getBillingAddress()->getVatId(),
//                    'Cliente'         => $order->getCustomerName(),
//                    'Morada'          => $street,
//                    'CodPostal'       => $order->getShippingAddress()->getPostcode(),
//                    'Localidade'      => $order->getShippingAddress()->getCity(),
//                    'Pais'            => $country->loadByCode($order->getShippingAddress()->getCountryId())->getName(),
//                    'Telefone'        => $order->getShippingAddress()->getTelephone(),
//                    'Email'           => $order->getCustomerEmail(),
//                    'linhas'          => json_encode($lines),
//                ];
//
//            }
//
//            fputcsv($fp, array_keys($orders[0]), $fieldSeparator);
//
//            foreach ($orders as $line) {
//                fputcsv($fp, $line, $fieldSeparator);
//            }
//
//            fclose($fp);
//            echo "<pre>";
//            print_r($orders);
//            echo "</pre>";
//
//            die();
//
//            #$manager->get('Licentia\Panda\Model\Products\Relations')->rebuildAvgDays();
//            #$order = $manager->create('Magento\Sales\Model\Order')->load(55);
//            #$manager->get('Licentia\Panda\Model\Autoresponders')->newOrder($order);
//            #$manager->get('Licentia\Panda\Model\Autoresponders')->cron();
//            #$manager->get('Licentia\Panda\Model\Followup')->cron();
//            #$manager->get('Licentia\Panda\Model\Campaigns')->queueCampaigns();
//            #$manager->get('Licentia\Panda\Model\Service\Smtp')->sendEmail();
//            #$manager->get('Licentia\Panda\Model\Service\Sms')->sendSms();
//            #$manager->get('Licentia\Panda\Model\Metadata')->activityRelated();
//            #$manager->get('Licentia\Panda\Model\Bounces')->processBounces();
//            #$manager->get('Licentia\Panda\Model\Stats')->randomStats();
//            #$manager->get('Licentia\Panda\Model\Followup')->getSubscribersObject();
//            #$manager->get('Licentia\Panda\Model\Metadata')->rebuildCustomerMetadata();
//
//            die(__METHOD__);
//        }
//

        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     *
     */
    public function execute()
    {

        $model = $this->autorespondersFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        if ($data = $this->_getSession()->getFormData(true)) {
            $model->addData($data);
        }

        if ($id) {
            $model->setId($id);
        }

        $this->registry->register('panda_autoresponder', $model, true);
    }

}
