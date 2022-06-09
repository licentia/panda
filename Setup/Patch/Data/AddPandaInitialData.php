<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Licentia\Panda\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddPandaInitialData implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {

        $this->pandaHelper = $pandaHelper;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {

        $this->moduleDataSetup->getConnection()->startSetup();

        $setup = $this->moduleDataSetup;
        $connection = $setup->getConnection();

        $now = $this->pandaHelper->gmtDate();

        $template = '{{template config_path="design/email/header_template"}}
                    
{MESSAGE}
                    
{{template config_path="design/email/footer_template"}}';

        $name = 'Default';
        $total = $connection->fetchRow(
            $connection->select()->from($setup->getTable('panda_templates_global'))
        );

        if (!$total) {
            $connection
                ->insert(
                    $setup->getTable('panda_templates_global'),
                    [
                        'name'      => $name,
                        'content'   => $template,
                        'is_active' => 1,
                    ]
                );
        }

        $connection
            ->insert(
                $setup->getTable('cron_schedule'),
                [
                    'job_code'     => 'panda_build_metadata',
                    'status'       => 'pending',
                    'created_at'   => $now,
                    'scheduled_at' => $now,
                ]
            );

        $connection
            ->insert(
                $setup->getTable('cron_schedule'),
                [
                    'job_code'     => 'panda_rebuild_relations',
                    'status'       => 'pending',
                    'created_at'   => $now,
                    'scheduled_at' => new \Zend_Db_Expr("DATE_ADD('$now',INTERVAL 24 HOUR)"),
                ]
            );

        $connection
            ->insert(
                $setup->getTable('cron_schedule'),
                [
                    'job_code'     => 'panda_products_recommendations',
                    'status'       => 'pending',
                    'created_at'   => $now,
                    'scheduled_at' => new \Zend_Db_Expr("DATE_ADD('$now',INTERVAL 24 HOUR)"),
                ]
            );

        $connection
            ->insert(
                $setup->getTable('cron_schedule'),
                [
                    'job_code'     => 'panda_rebuild_products_performance',
                    'status'       => 'pending',
                    'created_at'   => $now,
                    'scheduled_at' => new \Zend_Db_Expr("DATE_ADD('$now',INTERVAL 24 HOUR)"),
                ]
            );

        $total = $connection->fetchRow(
            $connection->select()->from($setup->getTable('panda_formulas'))
        );

        if (!$total) {
            $connection->insert(
                $setup->getTable('panda_formulas'),
                [
                    'formula_id'      => '1',
                    'formula_1_name'  => 'Formula 1',
                    'formula_2_name'  => 'Formula 2',
                    'formula_3_name'  => 'Formula 3',
                    'formula_4_name'  => 'Formula 4',
                    'formula_5_name'  => 'Formula 5',
                    'formula_6_name'  => 'Formula 6',
                    'formula_7_name'  => 'Formula 7',
                    'formula_8_name'  => 'Formula 8',
                    'formula_9_name'  => 'Formula 9',
                    'formula_10_name' => 'Formula 10',
                ]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
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

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {

        return [

        ];
    }
}