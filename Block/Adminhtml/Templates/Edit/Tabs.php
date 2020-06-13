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

namespace Licentia\Panda\Block\Adminhtml\Templates\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Templates\Edit
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
        $this->setId('subscriber_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Template Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        $current = $this->registry->registry('panda_template');

        $this->addTab(
            'form_section',
            [
                'label'   => __('Template Information'),
                'title'   => __('Template Information'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Templates\Edit\Tab\Form')
                                  ->toHtml(),
            ]
        );

        if (($current->getId() && $current->getParentId()) || $this->getRequest()->getParam('tid')) {
            $this->addTab(
                "variations_section",
                [
                    "label"   => __("Template Variation"),
                    "title"   => __("Template Variation"),
                    "content" => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Templates\Edit\Tab\Variations')
                                      ->toHtml(),
                ]
            );
        }

        /*
                $this->addTab(
                    'help_section',
                    [
                        'label'   => __('Autoresponders Variables'),
                        'title'   => __('Autoresponders Variables'),
                        'content' => $this->getLayout()
                                          ->createBlock('Licentia\Panda\Block\Adminhtml\Templates\Edit\Tab\Help')
                                          ->toHtml(),
                    ]
                );
        */

        return parent::_beforeToHtml();
    }
}
