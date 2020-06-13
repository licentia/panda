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

namespace Licentia\Panda\Block\Adminhtml\Support\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Support\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Form constructor.
     *
     * @param \Licentia\Panda\Helper\Data             $pandaHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Backend\Model\Session          $session
     * @param array                                   $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Session $session,
        array $data = []
    ) {

        $this->session = $session;
        $this->pandaHelper = $pandaHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('block_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $sender = $this->pandaHelper->getEmailSenderForInternalNotifications();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );

        $form->setHtmlIdPrefix('subscriber_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'name',
            "text",
            [
                "label"    => __('Name'),
                "required" => true,
                "name"     => 'name',
                "value"    => $sender->getName(),
            ]
        );

        $fieldset->addField(
            'email',
            "text",
            [
                "label"    => __('Email'),
                "required" => true,
                "name"     => 'email',
                "value"    => $sender->getEmail(),
            ]
        );

        $eOp = [];
        $eOp[] = ['value' => 'bug', 'label' => __('Bug Report')];
        $eOp[] = ['value' => 'request', 'label' => __('Request')];
        $eOp[] = ['value' => 'other', 'label' => __('Other Information')];

        $fieldset->addField(
            'reason',
            "select",
            [
                "label"    => __('Contact Reason'),
                "required" => true,
                'values'   => $eOp,
                "name"     => 'reason',
            ]
        );

        $fieldset->addField(
            'attach',
            "select",
            [
                "label"    => __('Attach Debug Information'),
                "required" => true,
                "value"    => '1',
                'options'  => ['1' => __('Yes'), '0' => __('No')],
                "name"     => 'attach',
                "note"     => "Contents from the Debug Tab and the Issues data. It's recommended that you attach this file for a quicker issue resolution",
            ]
        );

        $fieldset->addField(
            'message',
            "textarea",
            [
                "label"    => __('Message'),
                "required" => true,
                "name"     => 'message',
                "note"     => __('Please be as descriptive as possible'),
            ]
        );

        $form->addValues($this->session->getFormData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
