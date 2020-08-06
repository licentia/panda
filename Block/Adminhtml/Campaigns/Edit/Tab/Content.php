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

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Content
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Content extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {

        return __('Content');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {

        return __('Content');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {

        return $this->hasData('can_show_tab') ? $this->getData('can_show_tab') : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {

        return false;
    }

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Content constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Licentia\Panda\Model\TemplatesFactory  $templatesFactory
     * @param \Licentia\Panda\Helper\Data             $pandaHelper
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->templatesFactory = $templatesFactory;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->pandaHelper = $pandaHelper;

        $this->setTemplate('campaigns/edit.phtml');
    }

    /**
     * @return string
     */
    public function getTemplateField()
    {

        return 'message';
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry('panda_campaign');

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

        $fieldset2 = $form->addFieldset('url_fieldset', ['legend' => __('External Content')]);

        $fieldset2->addField(
            "url",
            "text",
            [
                "label" => __("URL Content"),
                "note"  => 'If set, content will be fetched from this URL and the text field bellow will be ignored.  {subscriberId} and {campaignId} tags in the URL will be replaced with real data',
                "name"  => "url",
            ]
        );

        $fieldset2->addField(
            "template_file",
            "text",
            [
                "label" => __("Parse Template File"),
                "note"  => 'Example: My_Company::campaign/content.phtml . You can access the "$campaign" and "$subscriber" vars. To get Campaign Content, use "$campaign->getMessage()" method',
                "name"  => "template_file",
            ]
        );

        $widgetFilters = ['is_email_compatible' => 1];
        $wysiwygConfig = $this->wysiwygConfig->getConfig(
            ['tab_id' => $this->getTabId(), 'widget_filters' => $widgetFilters]
        );
        $contentField = $fieldset2->addField(
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

        $this->setForm($form);

        if ($current->getData()) {
            $form->setValues($current->getData());
        }

        if (strlen(
                $form->getElement('message')
                     ->getEscapedValue()
            ) < 10) {
            $form->addValues(
                ['message' => '<br><br><br><!-- This tag is for unsubscribe link  --><a href="{{var subscriber.getUnsubscriptionLink()}}">Click here to unsubscribe</a>']
            );
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
