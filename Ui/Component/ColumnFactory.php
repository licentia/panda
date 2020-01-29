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

namespace Licentia\Panda\Ui\Component;

/**
 * Class ColumnFactory
 *
 * @package Licentia\Panda\Ui\Component
 */
class ColumnFactory
{

    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $componentFactory;

    /**
     * @var array
     */
    protected $jsComponentMap = [
        'text'        => 'Magento_Ui/js/grid/columns/column',
        'select'      => 'Magento_Ui/js/grid/columns/select',
        'multiselect' => 'Magento_Ui/js/grid/columns/select',
        'date'        => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var array
     */
    protected $dataTypeMap = [
        'default'     => 'text',
        'text'        => 'text',
        'boolean'     => 'select',
        'select'      => 'select',
        'multiselect' => 'multiselect',
        'date'        => 'date',
    ];

    /**
     * @param \Magento\Framework\View\Element\UiComponentFactory $componentFactory
     */
    public function __construct(\Magento\Framework\View\Element\UiComponentFactory $componentFactory)
    {

        $this->componentFactory = $componentFactory;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface          $attribute
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param array                                                        $config
     *
     * @return \Magento\Ui\Component\Listing\Columns\ColumnInterface
     */
    public function create($attribute, $context, array $config = [])
    {

        $columnName = 'field_' . $attribute->getEntryCode();
        $config = array_merge(
            [
                'label'     => __($attribute->getName()),
                'dataType'  => $this->getDataType($attribute),
                'add_field' => true,
                'visible'   => true,
                'filter'    => $this->getFilterType($attribute->getType()),
            ],
            $config
        );

        $config['component'] = $this->getJsComponent($config['dataType']);

        $arguments = [
            'data'    => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        return $this->componentFactory->create($columnName, 'column', $arguments);
    }

    /**
     * @param string $dataType
     *
     * @return string
     */
    protected function getJsComponent($dataType)
    {

        return $this->jsComponentMap[$dataType];
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     *
     * @return string
     */
    protected function getDataType($attribute)
    {

        return isset($this->dataTypeMap[$attribute->getType()])
            ? $this->dataTypeMap[$attribute->getType()]
            : $this->dataTypeMap['default'];
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     *
     * @return string
     */
    protected function getFilterType($frontendInput)
    {

        $filtersMap = ['date' => 'dateRange'];
        $result = array_replace_recursive($this->dataTypeMap, $filtersMap);

        return isset($result[$frontendInput]) ? $result[$frontendInput] : $result['default'];
    }
}
