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

namespace Licentia\Panda\Ui\Component\Listing\Column\Autoresponders;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Queue
 *
 * @package Licentia\Panda\Ui\Component\Listing\Column\Autoresponders
 */
class Queue extends Column
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Autoresponders\CollectionFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory
     */
    protected $eventsFactory;

    /**
     * Queue constructor.
     *
     * @param \Licentia\Panda\Model\ResourceModel\Autoresponders\CollectionFactory $autorespondersFactory
     * @param \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory         $eventsFactory
     * @param \Magento\Framework\UrlInterface                                      $urlInterface
     * @param ContextInterface                                                     $context
     * @param UiComponentFactory                                                   $uiComponentFactory
     * @param array                                                                $components
     * @param array                                                                $data
     */
    public function __construct(
        \Licentia\Panda\Model\ResourceModel\Autoresponders\CollectionFactory $autorespondersFactory,
        \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory $eventsFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {

        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->eventsFactory = $eventsFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (!$item['autoresponder_id']) {
                    return $dataSource;
                }

                $total = $this->eventsFactory->create()
                                             ->addFieldToFilter('executed', 0)
                                             ->addFieldToFilter('autoresponder_id', $item['autoresponder_id'])
                                             ->getSize();

                $item[$this->getData('name')]['views'] = [
                    'href'   => $this->urlInterface->getUrl(
                        'panda/events/index',
                        ['id' => $item['autoresponder_id']]
                    ),
                    'label'  => __('Queue') . " (" . $total . ")",
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
