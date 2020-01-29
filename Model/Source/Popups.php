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

namespace Licentia\Panda\Model\Source;

/**
 * Class Popups
 *
 * @package Licentia\Panda\Model\Source
 */
class Popups implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected $popupFactory;

    /**
     * Popups constructor.
     *
     * @param \Licentia\Panda\Model\PopupsFactory $popupsFactory
     */
    public function __construct(
        \Licentia\Panda\Model\PopupsFactory $popupsFactory
    ) {

        $this->popupFactory = $popupsFactory;
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {

        $collection = $this->popupFactory->create()
                                         ->getCollection()->addFieldToFilter('type', 'block');

        $return = [];

        /** @var \Licentia\Panda\Model\Popups $item */
        foreach ($collection as $item) {
            $return[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $return;
    }
}
