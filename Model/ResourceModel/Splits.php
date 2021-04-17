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
 * Class Splits
 *
 * @package Licentia\Panda\Model\ResourceModel
 */
class Splits extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var string
     */
    protected string $_idFieldName = 'split_id';

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('panda_campaigns_splits', 'split_id');
    }

    /**
     * @param $table
     * @param $data
     * @param $where
     *
     * @return int
     */
    public function updateStatsForMainSplit($table, $data, $where)
    {

        return $this->getConnection()->update($this->getTable($table), $data, $where);
    }
}
