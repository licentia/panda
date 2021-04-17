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
 * Class ClearOldStats
 *
 * @package Licentia\Panda\Cron
 */
class ClearOldStats
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\StatsFactory
     */
    protected $statsFactory;

    /**
     * ImportCustomers constructor.
     *
     * @param \Licentia\Panda\Model\StatsFactory                 $statsFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     */
    public function __construct(
        \Licentia\Panda\Model\StatsFactory $statsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Panda\Helper\Data $pandaHelper
    ) {

        $this->statsFactory = $statsFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return $this
     */
    public function execute()
    {

        $days = (int) $this->scopeConfig->getValue(
            'panda_nuntius/info/stats',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $connection = $this->statsFactory->create()->getResource()->getConnection();
        $table = $this->statsFactory->create()->getResource()->getTable('panda_identifiers');
        $connection->delete(
            $table,
            [
                'created_at <= DATE_SUB(NOW(),INTERVAL 4 WEEK)',
                '(updated_at IS NULL or updated_at <=  DATE_SUB(NOW(),INTERVAL 2 WEEK))',
            ]
        );

        if ($days == 0) {
            return $this;
        }
        try {
            $date = (new \DateTime())->sub(new \DateInterval('P' . $days . 'D'))
                                     ->format('Y-m-d');

            $stats = $this->statsFactory->create()
                                        ->getCollection()->addFieldToFilter('event_at', ['lt' => $date]);

            $stats->walk('delete');
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return $this;
    }
}
