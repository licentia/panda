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

namespace Licentia\Panda\Ui\Component\Listing\Column\Campaigns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Status
 *
 * @package Licentia\Panda\Ui\Component\Listing\Column\Campaigns
 */
class Status extends Column
{

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $return = '';

                $value = $item['status'];

                if ($value == "standby") {
                    $return = ' <span class="grid-severity-minor"><span>' . __('Stand By') .
                              '</span></span>';
                }

                if ($value == "queuing") {
                    $return = ' <span class="grid-severity-major"><span>' . __('Queuing') .
                              '</span></span>';
                }

                if ($value == "running") {
                    $return = ' <span class="grid-severity-major"><span>' . __('Running') .
                              '</span></span>';
                }

                if ($value == "finished") {
                    $return = ' <span class="grid-severity-notice"><span>' . __('Finished') .
                              '</span></span>';
                }
                $item[$this->getData('name')] = $return;
            }
        }

        return $dataSource;
    }
}
