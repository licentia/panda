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
declare(strict_types=1);

namespace Licentia\Panda\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddPandaAgePredictionProductAttribute implements DataPatchInterface, PatchRevertableInterface
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
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {

        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {

        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'panda_age_prediction',
            [
                'type'                    => 'varchar',
                'label'                   => 'Panda Age Prediction',
                'input'                   => 'select',
                'source'                  => \Licentia\Equity\Model\Source\Product\Attribute\Ages::class,
                'frontend'                => '',
                'required'                => false,
                'backend'                 => '',
                'sort_order'              => '400',
                'global'                  => ScopedAttributeInterface::SCOPE_GLOBAL,
                'default'                 => null,
                'visible'                 => true,
                'user_defined'            => true,
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'unique'                  => false,
                'apply_to'                => '',
                'group'                   => 'Panda',
                'used_in_product_listing' => false,
                'is_used_in_grid'         => true,
                'is_visible_in_grid'      => false,
                'is_filterable_in_grid'   => false,
                'option'                  => '',
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {

        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'panda_age_prediction');

        $this->moduleDataSetup->getConnection()->endSetup();
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

