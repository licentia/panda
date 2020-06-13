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

namespace Licentia\Panda\Model\ResourceModel\Links\Grid;

use Licentia\Panda\Model\ResourceModel\Links\Collection as LinksCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;

/**
 * Class Collection
 */
class Collection extends LinksCollection implements SearchResultInterface
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
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Licentia\Panda\Helper\Data                                  $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param mixed|null                                                   $mainTable
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $eventPrefix
     * @param mixed                                                        $eventObject
     * @param mixed                                                        $resourceModel
     * @param string                                                       $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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

        if ($this->registry->registry('panda_campaign')) {
            $this->addFieldToFilter(
                'campaign_id',
                $this->registry->registry('panda_campaign')
                               ->getId()
            );
        }
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
