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

namespace Licentia\Panda\Block\Adminhtml\Templates\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Templates\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected $sendersFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Licentia\Panda\Model\TemplatesGlobalFactory
     */
    protected $templatesGlobalFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context      $context
     * @param \Magento\Framework\Registry                  $registry
     * @param \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory
     * @param \Licentia\Panda\Model\SendersFactory         $sendersFactory
     * @param \Magento\Framework\Data\FormFactory          $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config            $wysiwygConfig
     * @param array                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        $this->wysiwygConfig = $wysiwygConfig;
        $this->sendersFactory = $sendersFactory;
        $this->templatesGlobalFactory = $templatesGlobalFactory;
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

        $model = $this->_coreRegistry->registry('panda_template');

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

        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('template_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Internal Name'),
                'title'    => __('Internal Name'),
                "required" => true,
                "disabled" => ((int) $model->getCampaignId() > 0) ? true : false,
            ]
        );

        $fieldset->addField(
            'recipients',
            'text',
            [
                'name'  => 'recipients',
                'label' => __('Send test Emails to'),
                'title' => __('Send test Emails to'),
                'note'  => __('Only used when testing this template. Separate multiple emails with a comma'),
            ]
        );

        $fieldset->addField(
            "sender_id",
            "select",
            [
                "label"    => __("Sender"),
                "class"    => "required-entry",
                "required" => true,
                "values"   => $this->sendersFactory->create()
                                                   ->getSenders('email'),
                "name"     => "sender_id",
                'note'     => __('Only used when testing this template'),
            ]
        );

        $fieldset->addField(
            "template_file",
            "text",
            [
                "label" => __("Parse Template File"),
                "note"  => 'Example: My_Company::campaign/content.phtml . You can access the "$campaign" and "$subscriber" vars. To get Campaign Content, use the "$campaign->getMessage()" method',
                "name"  => "template_file",
            ]
        );

        $fieldset->addField(
            "global_template_id",
            "select",
            [
                "class"    => "required-entry",
                "required" => true,
                "label"    => __("Design Template"),
                "values"   => $this->templatesGlobalFactory->create()
                                                           ->toFormValues(),
                "name"     => "global_template_id",
                'note'     => __('Can be changed when adding campaigns'),
            ]
        );

        if (!$model->getCampaignId()) {
            $fieldset->addField(
                'is_active',
                "select",
                [
                    "label"   => __('Status'),
                    "options" => ['1' => __('Active'), '0' => __('Inactive')],
                    "name"    => 'is_active',
                ]
            );

            $wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

            $contentField = $fieldset->addField(
                'message',
                'editor',
                [
                    'name'     => 'message',
                    'style'    => 'height:36em;',
                    'required' => true,
                    'config'   => $wysiwygConfig,
                ]
            );

            // Setting custom renderer for content field to remove label column
            $renderer = $this->getLayout()
                             ->createBlock(
                                 'Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element'
                             )
                             ->setTemplate(
                                 'Magento_Cms::page/edit/form/renderer/content.phtml'
                             );
            $contentField->setRenderer($renderer);
        }
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
