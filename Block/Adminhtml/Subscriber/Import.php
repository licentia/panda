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

namespace Licentia\Panda\Block\Adminhtml\Subscriber;

/**
 * Class Import
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber
 */
class Import extends \Magento\Backend\Block\Widget\Form\Container
{

    public function _construct()
    {

        parent::_construct();
        $this->_objectId = "id";
        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_subscriber';
        $this->_mode = 'import';
        $this->buttonList->update('save', 'label', __('Import'));

        $this->buttonList->remove('reset');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        return __('Import');
    }
}
