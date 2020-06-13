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
