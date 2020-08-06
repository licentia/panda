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
