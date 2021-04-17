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

namespace Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab;

/**
 * Class Emailb
 *
 * @package Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab
 */
class Emailb extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig;

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected \Licentia\Panda\Model\SendersFactory $sendersFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected \Licentia\Panda\Model\TemplatesFactory $templatesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Licentia\Panda\Model\TemplatesFactory  $templatesFactory
     * @param \Licentia\Panda\Model\SendersFactory    $sendersFactory
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        $this->wysiwygConfig = $wysiwygConfig;
        $this->sendersFactory = $sendersFactory;
        $this->templatesFactory = $templatesFactory;

        parent::__construct($context, $registry, $formFactory, $data);

        $current = $registry->registry('panda_split');

        $options = [];
        if ($current->getId()) {
            $options = explode('_', $current->getTesting());
        }
        if (in_array('message', $options)) {
            $this->setTemplate('campaigns/edita.phtml');
        }
    }

    /**
     * @return string
     */
    public function getTemplateField()
    {

        return 'message_b';
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $option = $this->getRequest()->getParam('option');
        $options = explode('_', $option);
        $current = $this->_coreRegistry->registry('panda_split');

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
        $fieldset = $form->addFieldset('content_fieldset', ['legend' => __('Content')]);

        if ($current->getId()) {
            $options = explode('_', $current->getTesting());
        }

        if (in_array('subject', $options)) {
            $fieldset->addField(
                'subject_b',
                'text',
                [
                    'name'     => 'subject_b',
                    'label'    => __('Subject B'),
                    'title'    => __('Subject B'),
                    "required" => true,
                ]
            );
        }

        if (in_array('sender', $options)) {
            $fieldset->addField(
                "sender_id_b",
                "select",
                [
                    "label"    => __("Sender B"),
                    "class"    => "required-entry",
                    "required" => true,
                    "values"   => $this->sendersFactory->create()
                                                       ->getSenders('email'),
                    "name"     => "sender_id_b",
                ]
            );
        }

        if (in_array('message', $options)) {
            $wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

            $contentField = $fieldset->addField(
                'message_b',
                'editor',
                [
                    'name'     => 'message_b',
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

        $this->setForm($form);

        if ($current) {
            $form->addValues($current->getData());
        }

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getTemplateOptions()
    {

        return $this->templatesFactory->create()->getOptionArray();
    }
}
