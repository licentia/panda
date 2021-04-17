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

namespace Licentia\Panda\Block\Adminhtml\Subscriber\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected \Magento\Framework\Registry $registry;

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
        $this->setTitle(__('Subscriber Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->registry->registry('panda_subscriber');

        $this->addTab(
            'form_section',
            [
                'label'   => __('Subscriber Information'),
                'title'   => __('Subscriber Information'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab\Form')
                                  ->toHtml(),
            ]
        );

        if ($subscriber->getId()) {
            $this->addTab(
                "conversions_section",
                [
                    "label" => __("Conversions"),
                    "title" => __("Conversions"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/gridconv', ['_current' => true]),
                ]
            );

            $this->addTab(
                "archive_section",
                [
                    "label" => __("Emails Sent"),
                    "title" => __("Emails Sent"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/archivegrid', ['_current' => true]),
                ]
            );

            $this->addTab(
                "archive_sms_section",
                [
                    "label" => __("SMS Sent"),
                    "title" => __("SMS Sent"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/archivesmsgrid', ['_current' => true]),
                ]
            );

            $this->addTab(
                "forms_section",
                [
                    "label"   => __("Forms"),
                    "title"   => __("Forms"),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab\Forms')
                                      ->toHtml(),
                ]
            );

            $this->addTab(
                "bounces_section",
                [
                    "label" => __("Bounces"),
                    "title" => __("Bounces"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/bouncesgrid', ['_current' => true]),
                ]
            );
        }

        return parent::_beforeToHtml();
    }
}
