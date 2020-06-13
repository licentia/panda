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
 * Class Help
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Help extends \Magento\Backend\Block\Template
{

    const PANDA_HELP_BASE_URL = 'https://www.greenflyingpanda.com/redir/?';

    /**
     * @return string
     */
    public function getPandaUrl()
    {

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        $section = $this->getRequest()->getParam('section');

        $url['controller'] = $controller;
        $url['action'] = $action;
        $url['m'] = $module;

        if ($this->getRequest()->getParam(
            'type',
            $this->getRequest()->getParam('ctype')
        )) {
            $type = $this->getRequest()->getParam(
                'type',
                $this->getRequest()->getParam('ctype')
            );

            $url['type'] = $type;
        }

        if ($this->getRequest()->getParam('event')) {
            $event = 'event/' . $this->getRequest()->getParam('event');

            $url['event'] = $event;
        }

        $urlFinal = '';
        foreach ($url as $key => $value) {
            $urlFinal .= $key . '/' . $value . '/';
        }

        if (substr($module, 0, 5) == 'panda') {
            return self::PANDA_HELP_BASE_URL . 'panda/' . $urlFinal;
        }

        if ($module == 'admin' && $action == 'edit' && stripos($section, 'panda') !== false) {
            return self::PANDA_HELP_BASE_URL . 'panda/config/' . $section;
        }

        return '';
    }

    /**
     * @return \Magento\Backend\Block\Template
     */
    public function _beforeToHtml()
    {

        if (!empty($this->getPandaUrl())) {
            $this->setTemplate('help/help.phtml');
        }

        return parent::_beforeToHtml();
    }
}
