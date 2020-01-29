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
