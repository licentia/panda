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
 * Class Queue
 *
 * @package Licentia\Panda\Model\ResourceModel
 */
class Queue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('panda_messages_queue', 'queue_id');
    }

    /**
     * @param                             $count
     * @param                             $uniqueRandomString
     * @param                             $date
     * @param \Licentia\Panda\Helper\Data $helper
     *
     * @return \Zend_Db_Statement_Interface
     */
    public function addProcessId($count, $uniqueRandomString, $date, \Licentia\Panda\Helper\Data $helper)
    {

        $mainTable = $this->getConnection()->quoteIdentifier($this->getMainTable());

        $twoHours = (new \DateTime($helper->gmtDate()))
            ->sub(new \DateInterval('PT2H'))
            ->format('Y-m-d H:i:s');

        $this->getConnection()->update(
            $this->getMainTable(),
            ['process_id' => new \Zend_Db_Expr('NULL')],
            [
                'process_id IS NOT NULL',
                'send_date <=?' => $twoHours,
            ]
        );

        return $this->getConnection()
                    ->query(
                        "UPDATE {$mainTable} SET process_id=? WHERE (send_date IS NULL OR send_date <='$date') AND process_id IS NULL LIMIT " . $count,
                        $uniqueRandomString
                    );
    }
}
