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
 * Class Templates
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class TemplatesGlobal extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_templatesGlobal';
        $this->_headerText = __('Templates');
        $this->_addButtonLabel = __('New Design Template');

        $varUrl = $this->getUrl('*/templates');

        $this->buttonList->add(
            'messages',
            [
                'label'   => __('Message Templates'),
                'onclick' => "window.location = '$varUrl'; return false;",
            ]
        );

        parent::_construct();
    }
}
