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
 * Class Splits
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Splits extends \Magento\Backend\Block\Widget\Container
{

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_splits';
        $this->_headerText = __('A/B Campaigns');
        parent::_construct();

        $this->buttonList->update('add', 'label', __('New A/B Campaign'));
    }

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {

        $addButtonProps = [
            'id'           => 'add_new_split',
            'label'        => __('New A/B Campaign. Test &raquo;'),
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

        $types = \Licentia\Panda\Model\Splits::getTestingOptions();

        foreach ($types as $key => $store) {
            $options[] = [
                'label'   => __($store),
                'onclick' => "window.location='" . $this->getUrl(
                        '*/*/new',
                        [
                            'option' => $key,
                        ]
                    ) . "'",
                'default' => true,
            ];
        }

        return $options;
    }
}
