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

namespace Licentia\Panda\Block\Adminhtml\TemplatesGlobal\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\TemplatesGlobal\Edit\Tab
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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Licentia\Panda\Model\SendersFactory    $sendersFactory
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        $this->wysiwygConfig = $wysiwygConfig;
        $this->sendersFactory = $sendersFactory;
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
        $this->setTitle(__('Design Template'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('panda_template_global');

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

        if (!$model->getParentId() && !$this->getRequest()->getParam('parent_id')) {
            $fieldset->addField(
                'name',
                'text',
                [
                    'name'     => 'name',
                    'label'    => __('Internal Name'),
                    'title'    => __('Internal Name'),
                    "required" => true,
                ]
            );
        }

        $fieldset->addField(
            'recipients',
            'text',
            [
                'name'  => 'recipients',
                'label' => __('Send Test Emails to'),
                'title' => __('Send Test Emails to'),
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
            'is_active',
            "select",
            [
                "label"   => __('Status'),
                "options" => ['1' => __('Active'), '0' => __('Inactive')],
                "name"    => 'is_active',
            ]
        );
        $fieldset->addField(
            'template_styles',
            'textarea',
            [
                'name'         => 'template_styles',
                'label'        => __('Template Styles'),
                'container_id' => 'field_template_styles',
            ]
        );
        $wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $contentField = $fieldset->addField(
            'content',
            'editor',
            [
                'name'     => 'content',
                'label'    => __('Template'),
                'style'    => 'height:36em;',
                'required' => true,
                'config'   => $wysiwygConfig,
            ]
        )
                                 ->setAfterElementHtml(
                                     '<em>' .
                                     __(
                                         'Please make sure you add the {MESSAGE} tag, so it can be replaced by the campaign message or the message template'
                                     ) . '</em>'
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

        $form->addValues($model->getData());

        if ($model->getParentId() || $this->getRequest()->getParam('parent_id')) {
            $fieldset->addField('parent_id', 'hidden', ['name' => 'parent_id']);
            $fieldset->addField('store_id', 'hidden', ['name' => 'store_id']);

            if ($this->getRequest()->getParam('parent_id')) {
                $form->addValues(
                    [
                        'parent_id' => $this->getRequest()->getParam('parent_id'),
                        'store_id'  => $this->getRequest()->getParam('store_id'),
                    ]
                );
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
