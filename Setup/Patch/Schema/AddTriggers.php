<?php

declare(strict_types=1);

namespace Licentia\Panda\Setup\Patch\Schema;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 *
 */
class AddTriggers implements SchemaPatchInterface
{

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * EnableSegmentation constructor.
     *
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {

        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {

        $this->schemaSetup->startSetup();
        $setup = $this->schemaSetup;

        $connection = $setup->getConnection();

        $trigger = $connection->fetchOne(
            "SHOW TRIGGERS WHERE `Trigger` LIKE 'tg_panda_messages_archive_update_insert'"
        );
        if (!$trigger) {
            $connection->multiQuery(
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

        $trigger = $connection->fetchOne(
            "SHOW TRIGGERS WHERE `Trigger` LIKE 'tg_panda_messages_error_update_insert'"
        );
        if (!$trigger) {
            $connection->multiQuery(
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

        $trigger = $connection->fetchOne(
            "SHOW TRIGGERS WHERE `Trigger` LIKE 'tg_panda_messages_queue_update_insert'"
        );

        if (!$trigger) {
            $connection->multiQuery(
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

        $this->schemaSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {

        return [];
    }
}
