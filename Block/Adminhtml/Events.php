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
