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

namespace Licentia\Panda\Block\Adminhtml\Popups\Edit\Tab;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\Popups\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected \Magento\Store\Model\System\Store $systemStore;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected \Licentia\Equity\Model\SegmentsFactory $segmentsFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param \Licentia\Equity\Model\SegmentsFactory  $segmentsFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        $this->systemStore = $systemStore;
        $this->segmentsFactory = $segmentsFactory;
        $this->wysiwygConfig = $wysiwygConfig;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        /** @var \Licentia\Panda\Model\Popups $model */
        $model = $this->_coreRegistry->registry('panda_popup');

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
        $fieldset = $form->addFieldset('params_fieldset', ['legend' => __('General')]);

        $fieldset->addField('type', 'hidden', ['name' => 'type', 'value' => $model->getType()]);

        $fieldset->addField(
            "name",
            "text",
            [
                "label"    => __("Name"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "name",
            ]
        );

        $wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $fieldset->addField(
            'content',
            'editor',
            [
                'label'    => __($model->getTypeName() . ' Content'),
                'name'     => 'content',
                'style'    => 'height:10em',
                'required' => true,
                'config'   => $wysiwygConfig,
            ]
        );

        if ($model->getType() == 'block') {
            $fieldset->addField(
                "identifier",
                "text",
                [
                    "label" => __(" Identifier"),
                    "name"  => "identifier",
                    "note"  => __(
                        'You can use this field to insert multiple Inline Info Blocks in the same place. Just give them the same identifier'
                    ),
                ]
            );
        }

        if ($model->getType() != 'block') {
            if ($model->getType() != 'sitebar') {
                $fieldset->addField(
                    "width",
                    "text",
                    [
                        "label"     => __($model->getTypeName() . " Width"),
                        "name"      => "width",
                        'maxlength' => 25,
                        'required'  => true,
                        'class'     => 'required-entry validate-css-length',
                    ]
                );
            }

            $fieldset->addField(
                "height",
                "text",
                [
                    "label"     => __($model->getTypeName() . " Height"),
                    "name"      => "height",
                    'maxlength' => 25,
                    'required'  => true,
                    'class'     => 'required-entry validate-css-length',
                ]
            );
        }

        $fieldset->addField(
            'display_to',
            "select",
            [
                "label"    => __('Display To'),
                'required' => true,
                "options"  => [
                    'both'      => __('Customers & Guests'),
                    'customers' => __('Customers'),
                    'guests'    => __('Guests'),
                ],
                "name"     => 'display_to',
            ]
        );

        $fieldset->addField(
            'avoid_subscribers',
            "select",
            [
                "label"    => __('Try to avoid Subscribers'),
                'required' => true,
                'value'    => 0,
                "options"  => [
                    '0' => __('No'),
                    '1' => __('Yes'),
                ],
                "name"     => 'avoid_subscribers',
                'note'     => __('Try to avoid displaying to a customer who is already a newsletter subscriber.'),
            ]
        );

        if ($model->getType() != 'block') {
            $fieldset->addField(
                'hide_for',
                "select",
                [
                    "label"   => __('When to Display'),
                    "name"    => 'hide_for',
                    'class'   => 'small_input',
                    "options" => [
                        '1'    => __('Show at most one time per session'),
                        '0'    => __('Possibly on every page view'),
                        '3650' => __('No more than once in lifetime'),
                        '7'    => __('No more than once a week'),
                        '14'   => __('No more than once every two weeks'),
                        '31'   => __('No more than once a month'),
                    ],
                ]
            );

            if ($model->getType() == 'sitebar') {
                $fieldset->addField(
                    'position',
                    "select",
                    [
                        "label"   => __("Position"),
                        "options" => [
                            'top'    => __('Top'),
                            'bottom' => __('Bottom'),
                        ],
                        "name"    => 'position',
                    ]
                );
            } else {
                $fieldset->addField(
                    'position',
                    "select",
                    [
                        "label"   => __("Position"),
                        "options" => [
                            'top_left'      => __('Top Left'),
                            'top_center'    => __('Top Center'),
                            'top_right'     => __('Top Right'),
                            'center_left'   => __('Center Left'),
                            'center_center' => __('Center'),
                            'center_right'  => __('Center Right'),
                            'bottom_left'   => __('Bottom Left'),
                            'bottom_center' => __('Bottom Center'),
                            'bottom_right'  => __('Bottom Right'),
                        ],
                        "name"    => 'position',
                    ]
                );
            }

            if ($model->getType() != 'sitebar') {
                $fieldset->addField(
                    'effect',
                    "select",
                    [
                        "label"   => __("Display Effect"),
                        "options" => [
                            'fadeIn'       => __('Fade In'),
                            'fadeInDown'   => __('Fade In Down'),
                            'fadeInLeft'   => __('Fade In Left'),
                            'fadeInRight'  => __('Fade In Right'),
                            'fadeInUp'     => __('Fade In Up'),
                            'slideInDown'  => __('Slide In Down'),
                            'slideInLeft'  => __('Slide In Left'),
                            'slideInRight' => __('Slide In Right'),
                            'slideInUp'    => __('Slide In Up'),
                            'tada'         => __('TAAADAAA!!!!'),
                            'flash'        => __('Flash'),
                            'lightSpeedIn' => __('lightSpeedIn'),
                        ],
                        "name"    => 'effect',
                    ]
                );
            }
        }

        $fieldset->addField(
            'platform',
            "select",
            [
                "label"   => __("Display On..."),
                "options" => [
                    'both'    => __('Desktop & Mobile'),
                    'desktop' => __('Desktop Only'),
                    'mobile'  => __('Mobile Only'),
                ],
                "name"    => 'platform',
            ]
        );

        $options = $this->segmentsFactory->create()->getOptionArray('All / Ignore');
        $fieldset->addField(
            'segments_ids',
            'multiselect',
            [
                'name'     => 'segments_ids[]',
                'label'    => __('Segment'),
                'title'    => __('Segment'),
                'required' => true,
                'values'   => $options,
            ]
        );

        $form->getElement('segments_ids')
             ->setData('size', count($options) > 7 ? 7 : count($options));

        $fieldset->addField(
            'segments_options',
            'select',
            [
                'name'     => 'segments_options',
                'label'    => __('Segment Rule'),
                'title'    => __('Segment Rule'),
                'required' => true,
                'values'   => [
                    'include_any'   => __('For Customers in ANY selected segments'),
                    'include_exact' => __('For Customers in ALL selected segments'),
                    'exclude_all'   => __('Exclude Customers who are in ALL selected segments'),
                    'exclude_any'   => __('Exclude Customers who are in ANY selected segments'),
                ],
            ]
        );

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

        $fieldset->addField(
            "useragent",
            "text",
            [
                "label" => __("User Agent"),
                "name"  => "useragent",
                'class' => 'small_input',
            ]
        );

        $fieldset->addField(
            'useragent_filter',
            "select",
            [
                "label"   => __('User Agent Filter'),
                "name"    => 'useragent_filter',
                'class'   => 'small_input',
                "options" => [
                    'contains' => __('Contains the expression above (if any)'),
                    'not'      => __('Does not contain the expression above (if any)'),
                ],
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

        $dateFormat = $this->_localeDate->getDateFormat();

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name'        => 'from_date',
                "class"       => "required-entry",
                "required"    => true,
                'date_format' => $dateFormat,
                'label'       => __('Start Date'),
            ]
        );

        $fieldset->addField(
            'to_date',
            'date',
            [
                'name'        => 'to_date',
                "class"       => "required-entry",
                "required"    => true,
                'date_format' => $dateFormat,
                'label'       => __('End Date'),
            ]
        );

        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
