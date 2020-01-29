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

namespace Licentia\Panda\Block\Adminhtml\Reports\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Reports\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    protected function _construct()
    {

        parent::_construct();
        $this->setId('report_detail_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Report Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        $this->addTab(
            'general_section',
            [
                'label'   => __('Report Information'),
                'title'   => __('Report Information'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab\General')
                                  ->toHtml(),
            ]
        );
        $this->addTab(
            'countries_section',
            [
                'label'   => __('Report Countries'),
                'title'   => __('Report Countries'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab\Countries')
                                  ->toHtml(),
            ]
        );
        $this->addTab(
            'cities_section',
            [
                'label'   => __('Report Regions'),
                'title'   => __('Report Regions'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab\Cities')
                                  ->toHtml(),
            ]
        );
        $this->addTab(
            'dates_section',
            [
                'label'   => __('Report Dates'),
                'title'   => __('Report Dates'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab\Dates')
                                  ->toHtml(),
            ]
        );
        $this->addTab(
            'applications_section',
            [
                'label'   => __('Report Applications'),
                'title'   => __('Report Applications'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab\Applications')
                                  ->toHtml(),
            ]
        );
        $this->addTab(
            'links_section',
            [
                'label'   => __('Report Links'),
                'title'   => __('Report Links'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab\Links')
                                  ->toHtml(),
            ]
        );
        $this->addTab(
            'conversions_section',
            [
                'label'   => __('Report Conversions'),
                'title'   => __('Report Conversions'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab\Conversions')
                                  ->toHtml(),
            ]
        );

        return parent::_beforeToHtml();
    }
}
