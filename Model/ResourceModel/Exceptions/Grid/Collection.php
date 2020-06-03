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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Panda\Model\ResourceModel\Exceptions\Grid;

use Licentia\Panda\Model\ResourceModel\Exceptions\Collection as ExceptionsCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;

/**
 * Class Collection
 */
class Collection extends ExceptionsCollection implements SearchResultInterface
{

    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param                                                              $mainTable
     * @param                                                              $eventPrefix
     * @param                                                              $eventObject
     * @param                                                              $resourceModel
     * @param string                                                       $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = '\Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );

        $this->registry = $registry;
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);

    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {

        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     *
     * @return $this|void
     */
    public function setAggregations($aggregations)
    {

        $this->aggregations = $aggregations;
    }

    /**
     * @return null
     */
    public function getSearchCriteria()
    {

        return null;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return $this
     */
    public function setSearchCriteria(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ) {

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {

        return $this->getSize();
    }

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount)
    {

        return $this;
    }

    /**
     * @param array|null $items
     *
     * @return $this
     */
    public function setItems(array $items = null)
    {

        return $this;
    }
}
