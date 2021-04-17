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

namespace Licentia\Panda\Cron;

/**
 * Class ImportCustomers
 *
 * @package Licentia\Panda\Cron
 */
class ImportCustomers
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * ImportCustomers constructor.
     *
     * @param \Licentia\Panda\Model\SubscribersFactory           $subscribersFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     */
    public function __construct(
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Panda\Helper\Data $pandaHelper
    ) {

        $this->subscribersFactory = $subscribersFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return bool
     */
    public function execute()
    {

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/customer_list')) {
            return false;
        }

        $resource = $this->subscribersFactory->create()->getResource();
        $connection = $resource->getConnection();

        do {
            $select = $connection->select()
                                 ->from(
                                     $resource->getTable('sales_order'),
                                     [
                                         'customer_firstname',
                                         'customer_lastname',
                                         'customer_email',
                                         'customer_id',
                                         'store_id',
                                     ]
                                 )
                                 ->where(
                                     'customer_email NOT IN (?)',
                                     $connection->select()
                                                ->from($resource->getTable('panda_subscribers'), 'email')
                                 )
                                 ->limit(500);

            $orders = $connection->fetchAll($select);

            foreach ($orders as $order) {
                $subscriber = $this->subscribersFactory->create();

                $data['customer_id'] = $order['customer_id'];
                $data['email'] = $order['customer_email'];
                $data['firstname'] = $order['customer_firstname'];
                $data['lastname'] = $order['customer_lastname'];
                $data['store_id'] = $order['store_id'];
                $data['previous_customer'] = 1;
                $data['status'] = 0;

                try {
                    $subscriber->setData($data)->save();
                } catch (\Exception $e) {
                    $this->pandaHelper->logWarning($e);
                }
            }
        } while (count($orders) > 0);

        return true;
    }
}
