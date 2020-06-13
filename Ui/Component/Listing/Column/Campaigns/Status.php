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
