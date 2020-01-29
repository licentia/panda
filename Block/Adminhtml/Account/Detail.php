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

namespace Licentia\Panda\Block\Adminhtml\Account;

use Magento\Framework\Registry;

/**
 * Class Detail
 *
 * @package Licentia\Panda\Block\Adminhtml\Account
 */
class Detail extends \Magento\Backend\Block\Template
{

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Panda\Helper\Api
     */
    protected $api;

    /**
     * @var array|\Licentia\Panda\Helper\Data
     */
    protected $helper;

    /**
     * Detail constructor.
     *
     * @param \Licentia\Panda\Helper\Api              $api
     * @param \Licentia\Panda\Helper\Data             $helper
     * @param Registry                                $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Api $api,
        \Licentia\Panda\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->api = $api;
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * @return string
     */
    public function getCountry()
    {

        return $this->helper->getCountryCode();
    }

    /**
     * @return  \Licentia\Panda\Helper\Api;
     */
    public function getApi()
    {

        return $this->api;
    }

    /**
     *
     */
    public function _construct()
    {

        $this->setTemplate('account/detail.phtml');
        parent::_construct();
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {

        return $this->_scopeConfig->getValue('panda_general');
    }
}
