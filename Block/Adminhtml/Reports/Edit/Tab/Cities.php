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

namespace Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab;

/**
 * Class Cities
 *
 * @package Licentia\Panda\Block\Adminhtml\Reports\Edit\Tab
 */
class Cities extends \Magento\Backend\Block\Widget\Form\Generic
{

    public function _construct()
    {

        parent::_construct();
        $this->setTemplate('report/cities.phtml');
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getRegistry()
    {

        return $this->_coreRegistry;
    }

    /**
     * @return mixed
     */
    public function getGoogleApi()
    {

        return $this->_scopeConfig->getValue(
            'panda_nuntius/info/google_api',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}
