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

namespace Licentia\Panda\Block\Adminhtml;

/**
 * Class Goals
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Goals extends \Magento\Backend\Block\Widget\Container
{

    protected function _construct()
    {

        parent::_construct();

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_goals';
        $this->_headerText = __('Goals');

        $location = $this->getUrl('*/*/refresh');
        $this->buttonList->add(
            'refresh',
            [
                "label"   => __("Refresh Current Values"),
                "onclick" => "window.location='$location'",
            ],
            -100
        );
    }

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {

        $addButtonProps = [
            'id'           => 'add_new_popup',
            'label'        => __('Select Goal to Add'),
            'class'        => 'add',
            'button_class' => '',
            'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options'      => $this->_getAddProductButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {

        $options = [];

        $types = \Licentia\Panda\Model\Goals::getGoalTypes();

        foreach ($types as $key => $store) {
            $options[] = [
                'label'   => __($store),
                'onclick' => "window.location='" . $this->getUrl(
                        '*/*/new',
                        [
                            'goal_type' => $key,
                        ]
                    ) . "'",
                'default' => false,
            ];
        }

        return $options;
    }
}
