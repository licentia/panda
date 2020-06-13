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

namespace Licentia\Panda\Block\Adminhtml;

/**
 * Class Subscriber
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Subscriber extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_subscriber';
        $this->_headerText = __('Subscribers');
        $this->_addButtonLabel = __('New Subscriber');
        parent::_construct();

        $location = $this->getUrl('*/*/import');

        $this->buttonList->add(
            'import',
            [
                "label"   => __("Import Subscribers"),
                "onclick" => "window.location='$location'",
            ],
            -100
        );
    }
}
