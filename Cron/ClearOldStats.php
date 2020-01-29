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
     * @var \Licentia\Panda\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Licentia\Panda\Model\StatsFactory
     */
    protected $statsFactory;

    /**
     * ImportCustomers constructor.
     *
     * @param \Licentia\Panda\Model\StatsFactory                 $statsFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Licentia\Panda\Logger\Logger                      $pandaLogger
     */
    public function __construct(
        \Licentia\Panda\Model\StatsFactory $statsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Panda\Logger\Logger $pandaLogger
    ) {

        $this->statsFactory = $statsFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaLogger = $pandaLogger;
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
            $this->pandaLogger->warning($e->getMessage());
        }

        return $this;
    }
}
