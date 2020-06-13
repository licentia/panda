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

namespace Licentia\Panda\Block\Adminhtml\Splits\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Splits\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param \Magento\Framework\Registry              $coreRegistry
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        $this->registry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('split_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('A/B Campaign Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        $current = $this->registry->registry('panda_split');

        $option = $this->getRequest()->getParam('option');

        $this->addTab(
            "form_section",
            [
                "label"   => __("General"),
                "title"   => __("General"),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab\Form')
                                  ->toHtml(),
            ]
        );

        if ($option || $current->getId()) {
            $this->addTab(
                "emaila_section",
                [
                    "label"   => __("Email"),
                    "title"   => __("Email"),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab\Emaila')
                                      ->toHtml(),
                ]
            );

            $this->addTab(
                "emailb_section",
                [
                    "label"   => __("Testing Options"),
                    "title"   => __("Testing Options"),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab\Emailb')
                                      ->toHtml(),
                ]
            );
        }

        if ($this->getRequest()->getParam('tab_id')) {
            $this->setActiveTab(str_replace($this->getId() . '_', '', $this->getRequest()->getParam('tab_id')));
        }

        return parent::_beforeToHtml();
    }
}
