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

namespace Licentia\Panda\Ui\Component\Listing;

/**
 * Class Columns
 *
 * @package Licentia\Panda\Ui\Component\Listing
 */
class Columns extends \Magento\Ui\Component\Listing\Columns
{

    /**
     * Default columns max order
     */
    const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /**
     * @var array
     */
    protected $filterMap = [
        'default' => 'text',
        'date'    => 'dateRange',
    ];

    /**
     * @var \Licentia\Panda\Ui\Component\ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\ExtraFields\CollectionFactory
     */
    protected $extraFieldsCollection;

    /**
     * Columns constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface      $context
     * @param \Licentia\Panda\Ui\Component\ColumnFactory                        $columnFactory
     * @param \Licentia\Panda\Model\ResourceModel\ExtraFields\CollectionFactory $extraFieldsCollection
     * @param array                                                             $components
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Licentia\Panda\Ui\Component\ColumnFactory $columnFactory,
        \Licentia\Panda\Model\ResourceModel\ExtraFields\CollectionFactory $extraFieldsCollection,
        array $components = [],
        array $data = []
    ) {

        parent::__construct($context, $components, $data);
        $this->columnFactory = $columnFactory;
        $this->extraFieldsCollection = $extraFieldsCollection;
    }

    public function prepare()
    {

        $collection = $this->extraFieldsCollection->create();

        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        foreach ($collection as $attribute) {
            $config = [];
            if (!isset($this->components['field_' . $attribute->getEntryCode()])) {
                $config['sortOrder'] = ++$columnSortOrder;
                $config['add_field'] = false;
                $config['visible'] = true;
                $config['filter'] = $this->getFilterType($attribute->getType());
                $column = $this->columnFactory->create($attribute, $this->getContext(), $config);
                $column->prepare();
                $this->addComponent('field_' . $attribute->getEntryCode(), $column);
            }
        }

        parent::prepare();
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

        return isset($this->filterMap[$frontendInput]) ? $this->filterMap[$frontendInput] : $this->filterMap['default'];
    }
}
