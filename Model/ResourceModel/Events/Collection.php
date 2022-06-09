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

namespace Licentia\Panda\Model\ResourceModel\Events;

/**
 * Class Collection
 *
 * @package Licentia\Panda\Model\ResourceModel\Events
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->_init(
            \Licentia\Panda\Model\Events::class,
            \Licentia\Panda\Model\ResourceModel\Events::class
        );
    }

    /**
     * @param bool $field
     *
     * @return array
     */
    public function getAllIds($field = false)
    {

        if (!$field) {
            return parent::getAllIds();
        }

        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Licentia\Panda\Helper\Data::DB_SELECT_ORDER);
        $idsSelect->reset(\Licentia\Panda\Helper\Data::DB_SELECT_LIMIT_COUNT);
        $idsSelect->reset(\Licentia\Panda\Helper\Data::DB_SELECT_LIMIT_OFFSET);
        $idsSelect->reset(\Licentia\Panda\Helper\Data::DB_SELECT_COLUMNS);
        $idsSelect->columns($field, 'main_table');

        return $this->getConnection()->fetchCol($idsSelect);
    }
}
