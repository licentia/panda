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
 * Class Email
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Email extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollection;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {

        return __('Campaign Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {

        return __('Campaign Information');
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
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected $sendersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesGlobalFactory
     */
    protected $templatesGlobalFactory;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * @param \Licentia\Panda\Model\TagsFactory                             $tagsFactory
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Licentia\Forms\Model\FormsFactory                            $formsFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Licentia\Panda\Helper\Data                                   $pandaHelper
     * @param \Magento\Store\Model\System\Store                             $systemStore
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection
     * @param \Licentia\Panda\Model\SendersFactory                          $sendersFactory
     * @param \Licentia\Panda\Model\ExtraFieldsFactory                      $extraFieldsFactory
     * @param \Licentia\Equity\Model\SegmentsFactory                        $segmentsFactory
     * @param \Licentia\Panda\Model\TemplatesGlobalFactory                  $templatesGlobalFactory
     * @param array                                                         $data
     */
    public function __construct(
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory,
        array $data = []
    ) {

        $this->tagsFactory = $tagsFactory;
        $this->sendersFactory = $sendersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->systemStore = $systemStore;
        $this->groupCollection = $groupCollection;
        $this->segmentsFactory = $segmentsFactory;
        $this->formsFactory = $formsFactory;
        $this->templatesGlobalFactory = $templatesGlobalFactory;

        parent::__construct($context, $registry, $formFactory, $data);
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

        $fieldset = $form->addFieldset("panda_form", ["legend" => __("Campaign Information")]);

        $fieldset->addField(
            "internal_name",
            "text",
            [
                "label"    => __("Internal Name"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "internal_name",
                "note"     => __(
                    "This name will be used for better identifying the campaign. Is only used internally and won't be displayed to subscribers"
                ),
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
                                                   ->getSenders(),
                "name"     => "sender_id",
                "note"     => __("The campaign sender. When subscribers receive a campaign, this is the 'From' value"),
            ]
        );

        $fieldset->addField(
            "subject",
            "text",
            [
                "label"    => __("Subject"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "subject",
                "note"     => __("The campaign email subject that will be sent to subscribers"),
            ]
        );

        if (!$current->getId() || $current->getStatus() == 'draft' || $current->getStatus() == 'standby') {
            $fieldset->addField(
                'status',
                'select',
                [
                    'name'    => 'status',
                    'type'    => 'options',
                    'label'   => __('Is this a Draft?'),
                    'options' => [
                        'draft'   => __("Yes - Do not schedule or run this campaign"),
                        'standby' => __("No - Go ahead, proceed as expected"),
                    ],
                    'note'    => __(
                        "The option to mark a campaign as draft will be removed once the campaign is queued"
                    ),
                ]
            );
        }

        $tags = $this->tagsFactory->create()->getAllTagsValues();
        if ($tags) {
            $fieldset->addField(
                'tags',
                'multiselect',
                [
                    'label'  => __('Tags'),
                    'title'  => __('Tags'),
                    'name'   => 'tags',
                    'note'   => __('Tag this campaign with theses tags'),
                    'values' => $tags,
                ]
            );
        }

        $options = $this->segmentsFactory->create()->getOptionArray('Ignore Field');
        if (count($options) > 1) {
            $fieldset->addField(
                'segments_ids',
                'multiselect',
                [
                    'name'     => 'segments_ids[]',
                    'label'    => __('Segment'),
                    'title'    => __('Segment'),
                    'required' => true,
                    'values'   => $options,
                    "note"     => __(
                        "Only send this campaign to subscribers that are in any of the selected segments above"
                    ),
                ]
            );
            $form->getElement('segments_ids')->setData('size', count($options) > 7 ? 7 : count($options));
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            $options = $this->systemStore->getStoreValuesForForm();
            array_unshift($options, ['label' => __('-- Ignore Field --'), 'value' => 0]);
            $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name'     => 'store_id[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $options,
                    "note"     => __(
                        "Only send this campaign to subscribers that are in any of the selected Store Views above"
                    ),
                ]
            );
        }
        $fieldset->addField(
            'track',
            'select',
            [
                'name'    => 'track',
                'type'    => 'options',
                'options' => [2 => __('Use Config'), 1 => __('Yes'), 0 => __('No')],
                'label'   => __('Track Stats?'),
                'note'    => __('If you want to track stats/conversions in this campaigns'),
            ]
        );

        $fieldset->addField(
            'autologin',
            'select',
            [
                'name'    => 'autologin',
                'type'    => 'options',
                'options' => [2 => __('Use Config'), 1 => __('Yes'), 0 => __('No')],
                'label'   => __('Auto-Login?'),
                'note'    => __(
                    'If customers should be logged in automatically in your store when they click in a campaign link'
                ),
            ]
        );

        $templates = $this->templatesGlobalFactory->create()->toFormValues();
        $templates = ['0' => __('Use From Message Template')] + $templates;
        $fieldset->addField(
            'global_template_id',
            'select',
            [
                'name'    => 'global_template_id',
                'type'    => 'options',
                'options' => $templates,
                'label'   => __('Design Template'),
                "note"    => __("Override the message template for this message."),
            ]
        );

        if ($this->_scopeConfig->getValue('panda_nuntius/info/customer_list')) {
            $fieldset->addField(
                'previous_customers',
                "select",
                [
                    "label"   => __('Previous Customers'),
                    "name"    => 'previous_customers',
                    "options" => ['0' => __('No'), '1' => __('Yes')],
                    "note"    => __(
                        'If this campaign should be sent to previous customers, instead of to current subscribers.'
                    ),
                ]
            );
        }

        $fieldset->addField(
            "number_recipients",
            "text",
            [
                'class' => 'small_input',
                "label" => __("Max Messages to Send"),
                "name"  => "number_recipients",
                "note"  => __('The maximum number of emails to send for this campaign'),
            ]
        );

        $fieldset->addField(
            "max_queue_hour",
            "text",
            [
                'class' => 'small_input',
                "label" => __("Max Messages to Send Per Hour"),
                "name"  => "max_queue_hour",
                "note"  => __(
                    'If you don not want to send campaign messages ASAP, you can define how many messages can be sent per hour. Please note that customer send time will override this option. 0 for unlimited'
                ),
            ]
        );

        $this->setForm($form);

        if ($current->getData()) {
            $form->addValues($current->getData());
        }

        if (!$current->getData('track') && !$current->getId()) {
            $form->addValues(['track' => 2]);
            $form->addValues(['autologin' => 2]);
            $form->addValues(['max_queue_hour' => 0]);
        }

        return parent::_prepareForm();
    }
}
