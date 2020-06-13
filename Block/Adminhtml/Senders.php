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
 * Class Senders
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Senders extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_senders';
        $this->_headerText = __('Senders');

        parent::_construct();

        $this->buttonList->remove('add');

        $dataAR = [
            'label'   => __('Add SMS Sender'),
            'class'   => 'primary',
            'onclick' => "setLocation('{$this->getUrl("*/*/new",['ctype'=>'sms'])}')",
        ];

        $this->buttonList->add('add_sms', $dataAR);

        $data = [
            'label'   => __('Add Email Sender'),
            'class'   => 'primary',
            'onclick' => "setLocation('{$this->getUrl("*/*/new", ['ctype' => 'email'])}')",
        ];

        $this->buttonList->add('add_email', $data);
    }
}
