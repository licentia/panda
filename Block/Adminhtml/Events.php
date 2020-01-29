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

namespace Licentia\Panda\Block\Adminhtml;

/**
 * Class Events
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Events extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_events';
        $this->_headerText = __('Events');
        parent::_construct();

        $this->buttonList->remove('add');

        $dataAR = [
            'label'   => __('Back to Autoresponders'),
            'class'   => 'back',
            'onclick' => "setLocation('{$this->getUrl("*/autoresponders")}')",
        ];
        $this->buttonList->add('add_ar', $dataAR);

        $data = [
            'label'   => __('View Campaigns'),
            'class'   => '',
            'onclick' => "setLocation('{$this->getUrl("*/*/campaigns", ['_current' => true])}')",
        ];
        $this->buttonList->add('add_sch', $data);
    }
}
