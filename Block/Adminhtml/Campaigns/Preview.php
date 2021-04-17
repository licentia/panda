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

namespace Licentia\Panda\Block\Adminhtml\Campaigns;

/**
 * Class Preview
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns
 */
class Preview extends \Magento\Backend\Block\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected ?\Magento\Framework\Registry $registry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCampaign()
    {

        return $this->registry->registry('panda_campaign');
    }

    protected function _construct()
    {

        $this->setTemplate('campaigns/preview.phtml');
    }
}
