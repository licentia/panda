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
 * @modified   03/06/20, 17:19 GMT
 *
 */
declare(strict_types=1);

namespace Licentia\Panda\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddPandaTwofactorNumberCustomerAttribute implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetup
     */
    private $customerSetupFactory;

    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory     $customerSetupFactory
     * @param SetFactory               $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    ) {

        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {

        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet Set */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'panda_twofactor_number',
            [
                'label'                 => 'Two-Factor Auth Mobile Number',
                'input'                 => 'text',
                'type'                  => 'varchar',
                'source'                => '',
                'required'              => false,
                'position'              => 333,
                'visible'               => true,
                'system'                => false,
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => false,
                'backend'               => '',
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'panda_twofactor_number');
        $attribute->addData([
            'used_in_forms' => [
                'adminhtml_customer',
                'customer_account_edit',
            ],
        ]);
        $attribute->addData([
            'attribute_set_id'   => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,

        ]);
        $attribute->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {

        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer::ENTITY, 'panda_twofactor_number');

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

