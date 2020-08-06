<?php

/*
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
