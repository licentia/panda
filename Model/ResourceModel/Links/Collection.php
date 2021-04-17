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

namespace Licentia\Panda\Model\ResourceModel\Links;

/**
 * Class Collection
 *
 * @package Licentia\Panda\Model\ResourceModel\Links
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected string $_idFieldName = 'link_id';

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->_init(\Licentia\Panda\Model\Links::class, \Licentia\Panda\Model\ResourceModel\Links::class);
    }

    /**
     *
     */
    public function toOptionHash()
    {

        parent::_toOptionHash('link_id', 'link');
    }
}
