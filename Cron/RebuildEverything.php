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
 * Class RebuildEverything
 *
 * @package Licentia\Panda\Cron
 */
class RebuildEverything
{

    /**
     * @var \Licentia\Reports\Model\IndexerFactory
     */
    protected \Licentia\Reports\Model\IndexerFactory $indexerFactory;

    /**
     * RebuildEverything constructor.
     *
     * @param \Licentia\Reports\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        \Licentia\Reports\Model\IndexerFactory $indexerFactory
    ) {

        $this->indexerFactory = $indexerFactory;
    }

    /**
     * @inheritdoc
     */
    protected function execute()
    {

        $this->indexerFactory->create()->reindexAll();
    }

}