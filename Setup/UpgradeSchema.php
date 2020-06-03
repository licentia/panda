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
 * @modified   02/06/20, 21:54 GMT
 *
 */

namespace Licentia\Panda\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 *
 * @package Licentia\Panda\Setup
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        if (version_compare($context->getVersion(), '3.5.0', '<')) {

            try {

                $setup->run(
                    "ALTER TABLE `{$setup->getTable('panda_two_factor_auth')}` ADD COLUMN `remember_hash` varchar(255) DEFAULT NULL"
                );

                $setup->run(
                    "ALTER TABLE `{$setup->getTable('admin_user')}` ADD COLUMN `panda_twofactor_number` varchar(50) DEFAULT NULL"
                );

                $setup->run(
                    "ALTER TABLE `{$setup->getTable('panda_two_factor_auth')}` ADD COLUMN `remember_until` date DEFAULT NULL"
                );

                $setup->run(
                    "ALTER TABLE `{$setup->getTable('panda_two_factor_auth')}` ADD UNIQUE `PANDA_TWO_FACTOR_HASH` (`remember_hash`) "
                );

                $setup->run(
                    "CREATE TABLE `{$setup->getTable('panda_two_factor_auth_admin')}` (
                      `auth_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) unsigned DEFAULT NULL,
                      `user_name` varchar(255) DEFAULT NULL,
                      `user_email` varchar(255) DEFAULT NULL,
                      `phone` varchar(255) DEFAULT NULL,
                      `message` varchar(255) DEFAULT NULL,
                      `sent_at` datetime DEFAULT NULL,
                      `used` smallint(6) DEFAULT NULL,
                      `is_active` smallint(6) DEFAULT NULL,
                      `store_id` smallint(5) unsigned DEFAULT NULL,
                      `code` varchar(255) DEFAULT NULL,
                      `used_at` datetime DEFAULT NULL,
                      `remember_hash` varchar(255) DEFAULT NULL,
                      `remember_until` date DEFAULT NULL,
                      PRIMARY KEY (`auth_id`),
                      UNIQUE KEY `PANDA_TWO_FACTOR_HASH` (`remember_hash`),
                      KEY `PANDA_TWO_FACTOR_AUTH_CODE` (`code`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Panda - '"
                );

                $setup->run(
                    "CREATE TABLE `{$setup->getTable('panda_two_factor_attempts_admin')}` (
                      `attempt_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) unsigned DEFAULT NULL,
                      `attempt_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`attempt_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Panda - '"
                );
            } catch (\Exception $e) {

            }
        }

    }
}