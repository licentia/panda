<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
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
