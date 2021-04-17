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

namespace Licentia\Panda\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Number
 *
 * @package Licentia\Panda\Ui\Component\Listing\Column\Reports
 */
class Tags extends Column
{

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * Tags constructor.
     *
     * @param \Licentia\Panda\Model\TagsFactory $tagsFactory
     * @param ContextInterface                  $context
     * @param UiComponentFactory                $uiComponentFactory
     * @param array                             $components
     * @param array                             $data
     */
    public function __construct(
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {

        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->tagsFactory = $tagsFactory;
    }

}
