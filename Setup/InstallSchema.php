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
