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

namespace Licentia\Panda\Ui\Component\Listing\Column\Goals;

use Licentia\Panda\Model\GoalsFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Types implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var GoalsFactory
     */
    protected $goalsFactory;

    /**
     * Types constructor.
     *
     * @param GoalsFactory $goalsFactory
     */
    public function __construct(GoalsFactory $goalsFactory)
    {

        $this->goalsFactory = $goalsFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        if ($this->options === null) {
            $types = \Licentia\Panda\Model\Goals::getGoalTypes();

            $result = [];
            foreach ($types as $key => $type) {
                $result[] = ['label' => $type, 'value' => $key];
            }

            $this->options = $result;
        }

        return $this->options;
    }
}
