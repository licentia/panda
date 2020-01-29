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

namespace Licentia\Panda\Block\Adminhtml\Reports;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Reports
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    protected function _construct()
    {

        $this->_objectId = 'id';
        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_reports';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');

        if ($this->getRequest()->getParam('campaign_id')) {
            $locationReturn = $this->getUrl(
                '*/campaigns/edit',
                [
                    'id' => $this->getRequest()->getParam('id'),
                ]
            );

            $this->buttonList->update('back', 'onclick', "setLocation('{$locationReturn}')");
            $this->buttonList->update('back', 'label', __('Go to Campaign'));

            $location = $this->getUrl('*/campaigns', []);

            $this->buttonList->add(
                "campaigns",
                [
                    "label"   => __("Go to Campaigns Listing"),
                    "onclick" => "window.location='$location'",
                ],
                -100
            );
        } else {
            $this->buttonList->remove('back');
        }
    }
}
