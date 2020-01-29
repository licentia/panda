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

namespace Licentia\Panda\Model\ResourceModel;

/**
 * Class Autoresponders
 *
 * @package Licentia\Panda\Model\ResourceModel
 */
class Autoresponders extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var string
     */
    protected $_idFieldName = 'autoresponder_id';

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('panda_autoresponders', 'autoresponder_id');
    }

    /**
     * @param $autoresponder
     * @param $date
     * @param $subscribersFactory
     *
     * @throws \Exception
     */
    public function clearAbandonedEvents($autoresponder, $date, $subscribersFactory)
    {

        //CLEAR EVENTS
        $connection = $this->getConnection();
        $abandonedDate = new \DateTime($date);
        $abandonedDate->sub(new \DateInterval('PT' . $autoresponder->getAbandonedCartMinutes() . 'M'));
        $real = $abandonedDate->format('Y-m-d H:i:s');

        $collectionQuote = $connection->select()
                                      ->from(['q' => $this->getTable('quote')], [])
                                      ->joinLeft(
                                          ['a' => $this->getTable('quote_address')],
                                          'q.entity_id=a.quote_id AND a.address_type="billing"',
                                          [
                                              'store_id' => 'q.store_id',
                                              'quote_id' => 'q.entity_id',
                                              'email'    => new \Zend_Db_Expr(
                                                  'IF(CHAR_LENGTH(q.customer_email) > 6,q.customer_email, a.email)'
                                              ),
                                          ]
                                      )
                                      ->where('q.items_count>=?', 0)
                                      ->where('q.is_active=?', '1')
                                      ->where('q.subtotal>=?', 0)
                                      ->where('q.updated_at>=?', $real)
                                      ->where('CHAR_LENGTH(q.customer_email)>6 OR CHAR_LENGTH(a.email) > 6')
                                      ->group('q.entity_id');

        $quoteEmails = $connection->fetchAll($collectionQuote);

        $subsIds = $subscribersFactory->create()->getSubscribersIdFromQuote('subscriber_id', $quoteEmails);

        $autorespondersCancel = $connection->select()
                                           ->from(
                                               $this->getTable('panda_autoresponders_cancellation_events'),
                                               ['autoresponder_id']
                                           )
                                           ->where('event=?', 'new_abandoned')
                                           ->distinct();
        $cancelAR = $connection->fetchCol($autorespondersCancel);
        $table = $this->getTable('panda_autoresponders_events');

        if ($subsIds) {
            $connection->delete(
                $table,
                [
                    'autoresponder_id IN(?)' => $cancelAR,
                    'subscriber_id IN (?)'   => $subsIds,
                    'executed=?'             => '0',
                ]
            );
        }

        $quoteIds = [];
        foreach ($quoteEmails as $info) {
            $quoteIds[] = $info['quote_id'];
        }

        $connection->delete(
            $table,
            [
                'event=?'              => 'new_abandoned',
                'data_object_id IN(?)' => $quoteIds,
                'executed=?'           => '0',
            ]
        );
    }

    /**
     * @param $table
     * @param $where
     *
     * @return int
     */
    public function deleteData($table, $where)
    {

        return $this->getConnection()->delete($table, $where);
    }
}
