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

namespace Licentia\Panda\Ui\Component\Listing\Column\Splits;

use Licentia\Panda\Model\SplitsFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Winners implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var SplitsFactory
     */
    protected $splitsFactory;

    /**
     * Options constructor.
     *
     * @param SplitsFactory $splitsFactory
     */
    public function __construct(SplitsFactory $splitsFactory)
    {

        $this->splitsFactory = $splitsFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        if ($this->options === null) {
            $result = $this->splitsFactory->create()->getWinnerOptions();

            foreach ($result as $key => $value) {
                $this->options[] = ['label' => $value, 'value' => $key];
            }
        }

        return $this->options;
    }
}
