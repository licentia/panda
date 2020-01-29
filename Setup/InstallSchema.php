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

namespace Licentia\Panda\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallSchema constructor.
     *
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param EavSetupFactory                         $eavSetupFactory
     */
    public function __construct(
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        EavSetupFactory $eavSetupFactory
    ) {

        $this->deploymentConfig = $deploymentConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $tablePrefix = $this->deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX);

        $sql = file_get_contents(dirname(__FILE__) . '/install.sql');

        if ($tablePrefix) {
            $sql = str_replace("`TABLE_PREFIX`.", "`$tablePrefix`.", $sql);
        } else {
            $sql = str_replace('`TABLE_PREFIX`.', '', $sql);
        }

        $queries = explode(';', $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $setup->getConnection()->multiQuery($query);
            }
        }

        $trigger = $setup->getConnection()
                         ->fetchOne(
                             "SHOW TRIGGERS WHERE `Trigger` LIKE 'tg_panda_messages_archive_update_insert'"
                         );
        if (!$trigger) {
            $setup->getConnection()
                  ->multiQuery(
                      "
            CREATE TRIGGER `tg_panda_messages_archive_update_insert` 
          AFTER INSERT ON `{$setup->getTable('panda_messages_archive')}` 
             FOR EACH ROW 
                 BEGIN
	                 DECLARE parent_campaign_id integer;
	                 SET @parent_campaign_id :=(select parent_id FROM {$setup->getTable('panda_campaigns')} where campaign_id=NEW.campaign_id LIMIT 1);
 	                 UPDATE {$setup->getTable('panda_campaigns')} set unsent = unsent - 1, sent = sent + 1 where {$setup->getTable('panda_campaigns')}.campaign_id = NEW.campaign_id OR {$setup->getTable('panda_campaigns')}.campaign_id = @parent_campaign_id;
	                 UPDATE {$setup->getTable('panda_subscribers')} set sent = sent + 1, last_message_sent_at=NOW() where {$setup->getTable('panda_subscribers')}.subscriber_id = NEW.subscriber_id;
                 END
            "
                  );
        }

        $trigger = $setup->getConnection()
                         ->fetchOne(
                             "SHOW TRIGGERS WHERE `Trigger` LIKE 'tg_panda_messages_error_update_insert'"
                         );
        if (!$trigger) {
            $setup->getConnection()
                  ->multiQuery(
                      "
            CREATE TRIGGER `tg_panda_messages_error_update_insert` 
          AFTER INSERT ON `{$setup->getTable('panda_messages_error')}` 
             FOR EACH ROW 
                 BEGIN
	                 DECLARE parent_campaign_id integer;
	                 SET @parent_campaign_id :=(select parent_id FROM {$setup->getTable('panda_campaigns')} where campaign_id=NEW.campaign_id LIMIT 1);
	                 UPDATE {$setup->getTable('panda_campaigns')} set unsent = unsent + 1, `errors` = `errors` + 1 where {$setup->getTable('panda_campaigns')}.campaign_id = NEW.campaign_id OR {$setup->getTable('panda_campaigns')}.parent_id = @parent_campaign_id;
                 END
            "
                  );
        }

        $trigger = $setup->getConnection()
                         ->fetchOne(
                             "SHOW TRIGGERS WHERE `Trigger` LIKE 'tg_panda_messages_queue_update_insert'"
                         );

        if (!$trigger) {
            $setup->getConnection()
                  ->multiQuery(
                      "
            CREATE TRIGGER `tg_panda_messages_queue_update_insert` 
          AFTER INSERT ON `{$setup->getTable('panda_messages_queue')}` 
             FOR EACH ROW 
                 BEGIN
	                 DECLARE parent_campaign_id integer;
	                 SET @parent_campaign_id :=(select parent_id FROM {$setup->getTable('panda_campaigns')} where campaign_id=NEW.campaign_id LIMIT 1);
	                 UPDATE {$setup->getTable('panda_campaigns')} set total_messages = total_messages + 1, unsent = unsent + 1 where {$setup->getTable('panda_campaigns')}.campaign_id = NEW.campaign_id OR {$setup->getTable('panda_campaigns')}.campaign_id = @parent_campaign_id;
                 END  
            "
                  );
        }

        $column = $setup->getConnection()
                        ->fetchOne(
                            "SHOW COLUMNS FROM `{$setup->getTable('salesrule')}` LIKE 'customer_id' "
                        );

        if (!$column) {
            $setup->run(
                "ALTER TABLE `{$setup->getTable('salesrule')}` ADD COLUMN `customer_id` int(10) UNSIGNED DEFAULT NULL"
            );
        }

        $setup->endSetup();
    }
}
