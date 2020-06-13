<?php

/**
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

namespace Licentia\Panda\Model\ResourceModel\Splits;

/**
 * Class Collection
 *
 * @package Licentia\Panda\Model\ResourceModel\Splits
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
            \Licentia\Panda\Model\Splits::class,
            \Licentia\Panda\Model\ResourceModel\Splits::class
        );

        $this->addFilterToMap('name', 'main_table.name');
        $this->addFilterToMap('conversions_number', 'main_table.conversions_number');
        $this->addFilterToMap('conversions_amount', 'main_table.conversions_amount');
        $this->addFilterToMap('conversions_average', 'main_table.conversions_average');
        $this->addFilterToMap('is_active', 'main_table.is_active');

    }

    /**
     * @return $this
     */
    public function _initSelect()
    {

        parent::_initSelect();
        $this->getSelect()
             ->joinLeft(
                 ['td' => $this->getResource()->getTable('panda_tags_relations')],
                 "td.entity_id = main_table.split_id AND td.entity_type='splits'",
                 []
             )
             ->joinLeft(
                 ['t' => $this->getResource()->getTable('panda_tags')],
                 't.tag_id = td.tag_id',
                 ['tags' => new \Zend_Db_Expr('GROUP_CONCAT(t.name)')]
             )
             ->group('main_table.split_id');

        $this->addFilterToMap('tags', 't.tag_id');

        return $this;
    }

}
