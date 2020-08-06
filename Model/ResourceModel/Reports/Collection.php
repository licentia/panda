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

namespace Licentia\Panda\Model\ResourceModel\Reports;

/**
 * Class Collection
 *
 * @package Licentia\Panda\Model\ResourceModel\Reports
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
            \Licentia\Panda\Model\Reports::class,
            \Licentia\Panda\Model\ResourceModel\Reports::class
        );
    }

    /**
     * @return $this
     */
    public function delete()
    {

        foreach ($this->getItems() as $k => $item) {
            $item->delete();
            unset($this->_items[$k]);
        }

        return $this;
    }
}
