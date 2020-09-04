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

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit
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
        $this->setId('Campaigns_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Campaign Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->registry->registry('panda_campaign');

        $type = $this->_request->getParam('ctype');

        if ($type == 'sms') {
            $this->addTab(
                'form_section',
                [
                    'label'   => __('Campaign Information'),
                    'title'   => __('Campaign Information'),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Sms')
                                      ->toHtml(),
                ]
            );
        }

        if ($type == 'email') {
            $this->addTab(
                'form_section',
                [
                    'label'   => __('Campaign Information'),
                    'title'   => __('Campaign Information'),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Email')
                                      ->toHtml(),
                ]
            );

            $this->addTab(
                "content_section",
                [
                    "label"   => __("Content"),
                    "title"   => __("Content"),
                    "content" => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Content')
                                      ->toHtml(),
                ]
            );
        }

        $this->addTab(
            "recurring_section",
            [
                "label"   => __("Sending Options"),
                "title"   => __("Sending Options"),
                "content" => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Recurring')
                                  ->toHtml(),
            ]
        );

        if ($campaign->getId() && $campaign->getRecurring() != '0') {
            $this->addTab(
                'children',
                [
                    'label'   => __('Child Campaigns'),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Children')
                                      ->toHtml(),
                ]
            );
        }

        if ($campaign->getId()) {
            $this->addTab(
                'conditions_section',
                [
                    'label'   => __('Segmentation'),
                    'title'   => __('Segmentation'),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Conditions')
                                      ->toHtml(),
                ]
            );
        }

        if ($campaign->getId() && $campaign->getdata('type') == 'email') {

            /** @var \Licentia\Panda\Model\ResourceModel\Followup\Collection $followup */
            $followup = $this->registry->registry('panda_followup_collection');

            if ($followup->getSize() > 0) {
                $this->addTab(
                    'followup_queue',
                    [
                        'label'   => __('Follow Ups Queue'),
                        'title'   => __('Follow Ups Queue'),
                        'content' => $this->getLayout()
                                          ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Followup')
                                          ->toHtml(),
                    ]
                );

                $this->addTab(
                    'followup_sent',
                    [
                        'label'   => __('Follow Ups Sent'),
                        'title'   => __('Follow Ups Sent'),
                        'content' => $this->getLayout()
                                          ->createBlock(
                                              'Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab\Followsent'
                                          )
                                          ->toHtml(),
                    ]
                );
            }

            $this->addTab(
                'conversions',
                [
                    'label' => __('Conversions'),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/gridconv', ['_current' => true]),
                ]
            );

            $this->addTab(
                "links_section",
                [
                    "label" => __("Links"),
                    "title" => __("Links"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/links', ['_current' => true]),
                ]
            );

            $errors = $this->getLayout()->createBlock('Licentia\Panda\Block\Adminhtml\Errors\Grid');
            $resultErrors = $errors->toHtml();
            $totalErrors = $errors->getCollection()->getSize();
            if ($totalErrors > 0) {
                $extraErrors = "<strong class='error'>($totalErrors)</strong>";
            } else {
                $extraErrors = '(0)';
            }
            $this->addTab(
                "error_section",
                [
                    "label"   => __("Messages Errors " . $extraErrors),
                    "title"   => __("Messages Errors " . $extraErrors),
                    "content" => $resultErrors,
                ]
            );
        }

        if ($campaign->getId()) {
            $this->addTab(
                "archive_section",
                [
                    "label" => __("Messages Archive"),
                    "title" => __("Messages Archive"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/archivegrid', ['_current' => true]),
                ]
            );

            $this->addTab(
                "queue_section",
                [
                    "label" => __("Messages Queue"),
                    "title" => __("Messages Queue"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/queuegrid', ['_current' => true]),

                ]
            );
        }


        return parent::_beforeToHtml();
    }
}
