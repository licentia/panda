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

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Class InstallData
 *
 * @package Licentia\Panda\Setup
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Attribute
     */
    private $attributeResource;

    /**
     * InstallData constructor.
     *
     * @param \Licentia\Panda\Helper\Data                     $pandaHelper
     * @param CustomerSetupFactory                            $customerSetupFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory     $categorySetupFactory
     * @param AttributeSetFactory                             $attributeSetFactory
     * @param SalesSetupFactory                               $salesSetupFactory
     * @param EavSetupFactory                                 $eavSetupFactory
     * @param \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        CustomerSetupFactory $customerSetupFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        AttributeSetFactory $attributeSetFactory,
        SalesSetupFactory $salesSetupFactory,
        EavSetupFactory $eavSetupFactory,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    ) {

        $this->salesSetupFactory = $salesSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->pandaHelper = $pandaHelper;
        $this->attributeResource = $attributeResource;
    }

    /**
     * @inheritdoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'panda_segments',
            [
                'type'             => 'decimal',
                'label'            => 'Segment Price',
                'input'            => 'text',
                'backend'          => 'Licentia\Equity\Model\Products\Attribute\Backend\SegmentsPrices',
                'required'         => false,
                'visible_on_front' => false,
                'user_defined'     => false,
                'sort_order'       => 700,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => 'simple',
                'group'            => 'Panda',
            ]
        );

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);

        $groups['Advanced Pricing']['id'] = $eavSetup->getAttributeGroupId(
            $entityTypeId,
            $attributeSetId,
            'Advanced Pricing'
        );

        // update attributes group and sort
        $attributes = [
            'panda_segments' => ['group' => 'Advanced Pricing', 'sort' => 100],
        ];

        foreach ($attributes as $attributeCode => $attributeProp) {
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $groups[$attributeProp['group']]['id'],
                $attributeCode,
                $attributeProp['sort']
            );
        }

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'panda_prices_disabled',
            [
                'type'                    => 'int',
                'backend'                 => '',
                'frontend'                => '',
                'label'                   => 'Disable Customer Prices',
                'input'                   => 'boolean',
                'class'                   => '',
                'source'                  => '',
                'global'                  => 1,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => false,
                'default'                 => null,
                'searchable'              => true,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => true,
                'unique'                  => false,
                'apply_to'                => '',
                'system'                  => 1,
                'group'                   => 'Panda',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'panda_price_expression',
            [
                'type'                    => 'varchar',
                'backend'                 => '',
                'frontend'                => '',
                'label'                   => 'Price Expression',
                'input'                   => 'text',
                'class'                   => '',
                'source'                  => '',
                'global'                  => 1,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => true,
                'default'                 => null,
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => false,
                'unique'                  => false,
                'apply_to'                => '',
                'system'                  => 1,
                'group'                   => 'Panda',
            ]
        );

        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        $salesSetup->addAttribute(Order::ENTITY, 'panda_shipping_cost', ['type' => 'decimal']);

        $salesSetup->addAttribute(Order::ENTITY, 'panda_extra_costs', ['type' => 'decimal']);

        $salesSetup->addAttribute(
            Order::ENTITY,
            "panda_acquisition_campaign",
            [
                'type'         => "varchar",
                'label'        => "Source Campaign",
                'input'        => "text",
                'nullable'     => true,
                'user_defined' => true,
            ]
        );

        /*
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'panda_acquisition_campaign',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'  => 255,
                'comment' => 'Source Campaign',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'panda_shipping_cost',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'comment' => 'Shipping Costs',
            ]
        );
        */

        $setup->endSetup();

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'panda_acquisition_campaign',
            [
                'type'     => 'varchar',
                'label'    => 'Customer Acquisition Campaign',
                'input'    => 'text',
                'source'   => '',
                'required' => false,
                'visible'  => true,
                'position' => 333,
                'system'   => false,
                'backend'  => '',
            ]
        );

        $attribute = $customerSetup->getEavConfig()
                                   ->getAttribute(Customer::ENTITY, 'panda_acquisition_campaign')
                                   ->addData(
                                       [
                                           'used_in_forms' => [
                                               'adminhtml_customer',
                                           ],
                                       ]
                                   );

        $this->attributeResource->save($attribute);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'panda_prices_disabled',
            [
                'type'                  => 'int',
                'label'                 => 'Panda - Disable Customer Prices',
                'input'                 => 'boolean',
                'source'                => '',
                'required'              => false,
                'visible'               => true,
                'is_used_in_grid'       => 0,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0,
                'is_searchable_in_grid' => 0,
                'position'              => 333,
                'system'                => true,
                'user_defined'          => false,
                'backend'               => '',
            ]
        );

        $attribute = $customerSetup->getEavConfig()
                                   ->getAttribute(Customer::ENTITY, 'panda_prices_disabled')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $this->attributeResource->save($attribute);

        $customerSetup->addAttribute(
            'customer',
            'panda_price_expression',
            [
                'type'                  => 'varchar',
                'label'                 => 'Panda - Customer Price Expression',
                'input'                 => 'text',
                'source'                => '',
                'required'              => false,
                'visible'               => true,
                'is_used_in_grid'       => 0,
                'is_visible_in_grid'    => 0,
                'is_filterable_in_grid' => 0,
                'is_searchable_in_grid' => 0,
                'position'              => 333,
                'system'                => true,
                'user_defined'          => false,
                'backend'               => '',
            ]
        );

        $attribute = $customerSetup->getEavConfig()
                                   ->getAttribute(Customer::ENTITY, 'panda_prices_disabled')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $this->attributeResource->save($attribute);

        /*
         * TWo Factor Authentication
         */

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'panda_twofactor_number',
            [
                'type'     => 'varchar',
                'label'    => 'Two Factor Mobile Number',
                'input'    => 'text',
                'source'   => '',
                'required' => false,
                'visible'  => true,
                'position' => 333,
                'system'   => true,
                'backend'  => '',
            ]
        );

        $attribute = $customerSetup->getEavConfig()
                                   ->getAttribute('customer', 'panda_twofactor_number')
                                   ->addData(
                                       [
                                           'used_in_forms' => [
                                               'adminhtml_customer',
                                               'customer_account_edit',
                                           ],
                                       ]
                                   );

        $this->attributeResource->save($attribute);

        /**
         *
         * Customer Prediction
         *
         *
         */

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'panda_gender_prediction',
            [
                'type'         => 'varchar',
                'label'        => 'Panda Gender Prediction',
                'input'        => 'select',
                'source'       => \Licentia\Equity\Model\Source\Product\Attribute\Gender::class,
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'      => true,
                'required'     => false,
                'default'      => null,
                'system'       => true,
                'user_defined' => false,
                'group'        => 'General Information',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'panda_age_prediction',
            [
                'type'         => 'varchar',
                'label'        => 'Panda Age Prediction',
                'input'        => 'multiselect',
                'backend'      => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source'       => \Licentia\Equity\Model\Source\Product\Attribute\Ages::class,
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'      => true,
                'required'     => false,
                'default'      => null,
                'system'       => true,
                'user_defined' => false,
                'group'        => 'General Information',
            ]
        );

        $now = $this->pandaHelper->gmtDate();

        $template = '{{template config_path="design/email/header_template"}}
                    
{MESSAGE}
                    
{{template config_path="design/email/footer_template"}}';

        $name = 'Default';
        $total = $setup->getConnection()->fetchRow(
            $setup->getConnection()->select()->from($setup->getTable('panda_templates_global'))
        );

        if (!$total) {
            $setup->getConnection()
                  ->insert(
                      $setup->getTable('panda_templates_global'),
                      [
                          'name'      => $name,
                          'content'   => $template,
                          'is_active' => 1,
                      ]
                  );
        }

        $setup->getConnection()
              ->insert(
                  $setup->getTable('cron_schedule'),
                  [
                      'job_code'     => 'panda_build_metadata',
                      'status'       => 'pending',
                      'created_at'   => $now,
                      'scheduled_at' => $now,
                  ]
              );

        $setup->getConnection()
              ->insert(
                  $setup->getTable('cron_schedule'),
                  [
                      'job_code'     => 'panda_rebuild_relations',
                      'status'       => 'pending',
                      'created_at'   => $now,
                      'scheduled_at' => new \Zend_Db_Expr("DATE_ADD('$now',INTERVAL 24 HOUR)"),
                  ]
              );

        $setup->getConnection()
              ->insert(
                  $setup->getTable('cron_schedule'),
                  [
                      'job_code'     => 'panda_products_recommendations',
                      'status'       => 'pending',
                      'created_at'   => $now,
                      'scheduled_at' => new \Zend_Db_Expr("DATE_ADD('$now',INTERVAL 24 HOUR)"),
                  ]
              );

        $setup->getConnection()
              ->insert(
                  $setup->getTable('cron_schedule'),
                  [
                      'job_code'     => 'panda_rebuild_products_performance',
                      'status'       => 'pending',
                      'created_at'   => $now,
                      'scheduled_at' => new \Zend_Db_Expr("DATE_ADD('$now',INTERVAL 24 HOUR)"),
                  ]
              );

        $total = $setup->getConnection()->fetchRow(
            $setup->getConnection()->select()->from($setup->getTable('panda_formulas'))
        );

        if (!$total) {
            $setup->getConnection()
                  ->insert(
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
        $indexCampaign = $setup->getConnection()
                               ->fetchOne(
                                   "SHOW INDEX FROM `{$setup->getTable('sales_order')}` WHERE Key_name = 'SALES_ORDER_ACQUISITION_CAMPAIGN'  "
                               );

        if (!$indexCampaign) {
            $setup->run(
                "ALTER TABLE `{$setup->getTable('sales_order')}` ADD INDEX `SALES_ORDER_ACQUISITION_CAMPAIGN` USING BTREE (`panda_acquisition_campaign`) "
            );
        }

        $setup->endSetup();
    }
}
