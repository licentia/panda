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
