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

namespace Licentia\Panda\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Test
 *
 * @package Licentia\Panda\Block\Adminhtml\System\Config\Form\Field
 */
class Sms extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {

        $url = $this->getUrl('panda/support/validateSmsEnvironment');

        return '<button  onclick="window.location=\'' . $url .
               'email/\'+$F(\'panda_test_sms\')" class="scalable" type="button" ><span><span><span>' .
               __('Test Now') . '</span></span></span></button>';
    }
}
