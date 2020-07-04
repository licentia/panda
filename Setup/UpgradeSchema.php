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

                $setup->run(
                    " CREATE TABLE `{$setup->getTable('panda_exceptions_report')}` (
                      `exception_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `message` varchar(255) DEFAULT NULL,
                      `file` varchar(255) DEFAULT NULL,
                      `line` varchar(255) DEFAULT NULL,
                      `trace` text,
                      PRIMARY KEY (`exception_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 "
                );
            } catch (\Exception $e) {

            }
        }

        if (version_compare($context->getVersion(), '3.6.0', '<')) {
            try {

                $setup->run(
                    " CREATE TABLE `{$setup->getTable('panda_customer_prices')}` (
                      `price_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `customer_id` int(10) unsigned NOT NULL,
                      `product_id` int(10) unsigned NOT NULL,
                      `website_id` smallint(5) unsigned NOT NULL,
                      `price` decimal(12,4) NOT NULL,
                      PRIMARY KEY (`price_id`),
                      UNIQUE KEY `IDX_PANDA_CUSTOMER_PRICES_UNIQUE` (`customer_id`,`product_id`,`website_id`),
                      KEY `IDX_PANDA_CUSTOMER_PRICES_CUSTID` (`customer_id`),
                      KEY `IDX_PANDA_CUSTOMER_PRICES_PRODID` (`product_id`),
                      KEY `IDX_PANDA_CUSTOMER_PRICES_WEBSITEID` (`website_id`),
                      CONSTRAINT `FK_PANDA_CUSTOMER_PRICES_CUSTID` FOREIGN KEY (`customer_id`) REFERENCES `{$setup->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      CONSTRAINT `FK_PANDA_CUSTOMER_PRICES_PRODID` FOREIGN KEY (`product_id`) REFERENCES `{$setup->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      CONSTRAINT `FK_PANDA_CUSTOMER_PRICES_WEBSITEID` FOREIGN KEY (`website_id`) REFERENCES `{$setup->getTable('store_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
                    "
                );

                $setup->run(
                    " CREATE TABLE `{$setup->getTable('panda_segments_products')}` (
                      `record_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `segment_id` int(10) unsigned NOT NULL,
                      `product_id` int(10) unsigned NOT NULL,
                      PRIMARY KEY (`record_id`),
                      UNIQUE KEY `IDX_PANDA_SEGMENTS_PRODS_UNIQUE` (`product_id`,`segment_id`) USING BTREE,
                      KEY `IDX_PANDA_SEGMENTS_PRODS_SEGID` (`segment_id`) USING BTREE,
                      KEY `IDX_PANDA_SEGMENTS_PRODS_PRODID` (`product_id`) USING BTREE,
                      CONSTRAINT `FK_PANDA_GROUPS_PRODS_PRODID` FOREIGN KEY (`product_id`) REFERENCES `{$setup->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      CONSTRAINT `FK_PANDA_GROUPS_PRODS_SEGID` FOREIGN KEY (`segment_id`) REFERENCES `{$setup->getTable('panda_segments')}` (`segment_id`) ON DELETE CASCADE ON UPDATE CASCADE
                      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
                    "
                );

                $setup->run(
                    "ALTER TABLE `{$setup->getTable('panda_subscribers')}`ADD COLUMN `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `cellphone`"
                );

                $setup->run("DROP TABLE `{$setup->getTable('panda_segments_prices_idx')}");

                $setup->run("ALTER TABLE `{$setup->getTable('panda_segments_prices')}` ADD UNIQUE `PANDA_SEGMENTS_PRICES_UNIQUE` (`product_id`, `segment_id`, `website_id`) ");

                $setup->run("ALTER TABLE `{$setup->getTable('panda_segments')}` ADD COLUMN `code` varchar(255) DEFAULT NULL  AFTER `name`");

                $setup->run("ALTER TABLE `{$setup->getTable('panda_segments')}` ADD COLUMN `number_products` smallint UNSIGNED DEFAULT '0' AFTER `websites_ids`");

                $setup->run("ALTER TABLE `{$setup->getTable('panda_segments')}` ADD COLUMN `use_as_catalog` tinyint UNSIGNED DEFAULT 0 AFTER `websites_ids`");

            } catch (\Exception $e) {

            }
        }

    }
}