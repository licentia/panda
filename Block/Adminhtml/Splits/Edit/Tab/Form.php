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
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

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
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollection;

    /**
     * @var \Licentia\Panda\Model\SplitsFactory
     */
    protected $splitsFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

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
     * @param \Licentia\Panda\Model\SplitsFactory                           $splitsFactory
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
        \Licentia\Panda\Model\SplitsFactory $splitsFactory,
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
        $this->splitsFactory = $splitsFactory;
        $this->formsFactory = $formsFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->templatesGlobalFactory = $templatesGlobalFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        /** @var \Licentia\Panda\Model\Splits $current */
        $current = $this->_coreRegistry->registry('panda_split');
        $option = $this->getRequest()->getParam('option');

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
        $fieldset = $form->addFieldset('params_fieldset', ['legend' => __('Settings')]);

        $fieldset->addField('testing', 'hidden', ['name' => 'testing', 'value' => $option]);

        $fieldset->addField(
            'name',
            "text",
            [
                "label"    => __("Internal Name"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "name",
            ]
        );

        $fieldset->addField(
            "is_active",
            "select",
            [
                "label"    => __("Active"),
                "class"    => "required-entry",
                "required" => true,
                "values"   => ['0' => __('No'), '1' => __('Yes')],
                "name"     => "is_active",
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat();
        $timeFormat = $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT);

        $fieldset->addField(
            'deploy_at',
            'date',
            [
                'name'         => 'deploy_at',
                'date_format'  => $dateFormat,
                'time_format'  => $timeFormat,
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT,
                'label'        => __('Send Date'),
            ]
        );

        $fieldset->addField(
            'days',
            'select',
            [
                'name'     => 'days',
                'label'    => __('Send General Campaign after X days'),
                'title'    => __('Send General Campaign after X days'),
                'required' => true,
                'options'  => array_combine(range(1, 10), range(1, 10)),
            ]
        );

        $fieldset->addField(
            'percentage',
            'select',
            [
                'name'     => 'percentage',
                'label'    => __('Percentage Emails Send Test'),
                'title'    => __('Percentage Emails Send Test'),
                'required' => true,
                'options'  => array_combine(range(5, 40, 5), range(5, 40, 5)),
            ]
        );

        $fieldset->addField(
            'winner',
            'select',
            [
                'name'     => 'winner',
                'label'    => __('How to determine winner'),
                'title'    => __('How to determine winner'),
                'required' => true,
                'options'  => $this->splitsFactory->create()
                                                  ->getWinnerOptions(),
            ]
        );

        $tags = $this->tagsFactory->create()->getAllTagsValues();
        if ($tags) {
            $fieldset->addField(
                'tags',
                'multiselect',
                [
                    'label'  => __('Tags'),
                    'title'  => __('Tags'),
                    'name'   => 'tags',
                    'note'   => __('Tag this A/B campaign with these tags'),
                    'values' => $tags,
                ]
            );
        }

        $options = $this->segmentsFactory->create()->getOptionArray('Any');

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
                    "class"    => 'small_input',
                ]
            );
            $form->getElement('segments_ids')->setData('size', count($options) > 7 ? 7 : count($options));
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            $options = $this->systemStore->getStoreValuesForForm();
            array_unshift($options, ['label' => __('-- Any --'), 'value' => 0]);
            $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name'     => 'store_id[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $options,
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
                'note'    => __('If you want to track stats/conversions in this campaign'),
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
                'note'    => __('If customers should be logged in automatically after opening a campaign link'),
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
                        'If this A/B campaign should be sent to previous customers instead of current subscribers'
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
                "note"  => __(
                    'The maximum number of emails to send for this general campaign (test campaign not included)'
                ),
            ]
        );

        $this->setForm($form);

        if ($current->getData()) {
            $form->addValues($current->getData());

        }

        if (!$current->getAutologin() && !$current->getId()) {
            $form->addValues(
                [
                    'autologin' => 2,
                    'track'     => 2,
                ]
            );
        }

        return parent::_prepareForm();
    }
}
