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
 * @modified   03/06/20, 16:31 GMT
 *
 */

namespace Licentia\Panda\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Filesystem;

/**
 * Class Debug
 *
 * @package Licentia\Panda\Helper
 */
class Debug extends \Magento\Framework\App\Helper\AbstractHelper
{

    const SUPPORT_EMAIL = 'support@greenflyingpanda.com';

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \Licentia\Panda\Model\ExceptionsFactory
     */
    protected $exceptionsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deployment;

    /**
     * Debug constructor.
     *
     * @param \Magento\Framework\Module\ModuleListInterface        $moduleList
     * @param Filesystem                                           $filesystem
     * @param \Magento\Framework\App\DeploymentConfig              $config
     * @param Context                                              $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Licentia\Panda\Model\ExceptionsFactory              $exceptionsFactory
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        Filesystem $filesystem,
        \Magento\Framework\App\DeploymentConfig $config,
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Licentia\Panda\Model\ExceptionsFactory $exceptionsFactory
    ) {

        parent::__construct($context);

        $this->moduleList = $moduleList;
        $this->deployment = $config;
        $this->timezone = $timezone;
        $this->exceptionsFactory = $exceptionsFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {

        return $this->moduleList->getOne('Licentia_Panda')['setup_version'];
    }

    /**
     * @param $table
     *
     * @return mixed
     */
    private function getTable($table)
    {

        return $this->exceptionsFactory->create()
                                       ->getResource()->getTable($table);
    }

    /**
     * @return array
     */
    public function getDebugInfo()
    {

        $result = $this->deployment->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_NAME
        );

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->exceptionsFactory->create()->getResource()->getConnection();

        $global = $connection->fetchCol("SHOW TABLES FROM `$result` WHERE `Tables_in_$result` LIKE '%panda_%'");

        $totalRecords = [
            'Total Customers' => ['table' => 'customer_entity'],
            'Total Orders'    => ['table' => 'sales_order'],
            'Total Quotes'    => ['table' => 'quote', 'conditions' => ['is_active' => '1']],

            'Total SMS Senders'   => ['table' => 'panda_senders', 'conditions' => ['type' => 'sms']],
            'Total Email Senders' => ['table' => 'panda_senders', 'conditions' => ['type' => 'email']],

            'Total SMS Autoresponders Events Queued' => [
                'table'      => 'panda_autoresponders_events',
                'conditions' => [
                    'executed' => '0',
                ],
            ],

            'Total Email Campaigns' => ['table' => 'panda_campaigns', 'conditions' => ['type' => 'email']],
            'Total SMS Campaigns'   => ['table' => 'panda_campaigns', 'conditions' => ['type' => 'sms']],
        ];

        foreach (['standby', 'finished', 'running', 'queuing', 'paused', 'canceled', 'draft'] as $status) {
            $totalRecords['Total Email Campaigns ' . ucwords($status)] = [
                'table'      => 'panda_campaigns',
                'conditions' => [
                    'type'   => 'email',
                    'status' => $status,
                ],
            ];
            $totalRecords['Total SMS Campaigns ' . ucwords($status)] = [
                'table'      => 'panda_campaigns',
                'conditions' => [
                    'type'   => 'sms',
                    'status' => $status,
                ],
            ];
        }

        foreach ($global as $table) {
            $totalRecords['Total records in table ' . $table] = [
                'table' => $table,
            ];
        }

        $final = [];
        $final['MYSQL Version'] = $connection->fetchOne('SELECT VERSION()');
        $final['PHP Version'] = phpversion();
        $final['Extension Version'] = $this->getVersion();

        foreach ($totalRecords as $key => $record) {
            $select = $connection->select()->from($this->getTable($record['table']), []);
            $select->columns(['total' => new \Zend_Db_Expr('COUNT(*)')]);

            if (isset($record['conditions'])) {
                foreach ($record['conditions'] as $field => $condition) {
                    $select->where($field . ' = ?', $condition);
                }
            }

            try {
                $final[$key] = $connection->fetchOne($select);
            } catch (\Exception $e) {
            }
        }

        $errorsCron = $connection->fetchAll(
            $connection->select()
                       ->from($this->getTable('cron_schedule'))
                       ->where('job_code LIKE ?', '%panda%')
                       ->order('schedule_id desc')
                       ->limit(100)
        );

        foreach ($errorsCron as $error) {
            $final['Entries in Cron ' . $error['schedule_id']] = implode(' --- ', $error);
        }

        $configData = $connection->fetchAll(
            $connection->select()
                       ->from($this->getTable('core_config_data'))
                       ->where('path LIKE ?', '%panda%')
        );

        foreach ($configData as $data) {
            $final['Entries in Config Data ' . $data['config_id']] = implode(' --- ', $data);
        }

        $exceptionsEntries = $this->exceptionsFactory->create()->getCollection()->setPageSize(500)->getData();

        $final['exceptions_entries'] = $exceptionsEntries;

        return $final;
    }

    /**
     * @return string
     */
    public function getCreateDumpFile()
    {

        return json_encode($this->getDebugInfo());
    }
}
