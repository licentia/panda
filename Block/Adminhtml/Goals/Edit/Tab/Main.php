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

namespace Licentia\Panda\Block\Adminhtml\Goals\Edit\Tab;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\Goals\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected \Licentia\Panda\Model\CampaignsFactory $campaignsFactory;

    /**
     * @var \Licentia\Panda\Model\GoalsFactory
     */
    protected \Licentia\Panda\Model\GoalsFactory $goalsFactory;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected \Licentia\Equity\Model\SegmentsFactory $segmentsFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected \Licentia\Forms\Model\FormsFactory $formsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Licentia\Panda\Model\GoalsFactory      $goalsFactory
     * @param \Licentia\Panda\Model\CampaignsFactory  $campaignsFactory
     * @param \Licentia\Forms\Model\FormsFactory      $formsFactory
     * @param \Licentia\Panda\Helper\Data             $pandaHelper
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Licentia\Equity\Model\SegmentsFactory  $segmentsFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Licentia\Panda\Model\GoalsFactory $goalsFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->goalsFactory = $goalsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->campaignsFactory = $campaignsFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->formsFactory = $formsFactory;
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry('panda_goal');
        $location =
            $this->getUrl('*/*/*', ['_current' => true, 'goal_type' => false]) . 'goal_type/';

        $goal = $this->getRequest()->getParam('goal_type');

        if ($current->getGoalType()) {
            $goal = $current->getGoalType();
        } else {
            $current->setData('goal_type', $goal);
            $current->setStatus(3);
        }

        $types = \Licentia\Panda\Model\Goals::getGoalTypes();

        if (!$current->getData('goal_type')) {
            array_unshift($types, __('Please Select'));
        }

        $show = false;

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

        $fieldset->addField(
            "goal_type",
            "select",
            [
                "label"    => __("Goal Type"),
                "class"    => "required-entry",
                "required" => true,
                "options"  => $types,
                "name"     => "goal_type",
                "disabled" => $current->getId() ? true : false,
                "onchange" => "window.location='$location'+this.value",
            ]
        );

        if (stripos($goal, 'forms') !== false) {
            $show = 1;

            $fieldset->addField(
                "goal_type_option_id",
                "select",
                [
                    "label"    => __("Form"),
                    "class"    => "required-entry",
                    "required" => true,
                    "options"  => $options = $this->formsFactory->create()
                                                                ->toFormValues(),
                    "name"     => "goal_type_option_id",
                    "disabled" => ($current->getStatus() <= 2) ? true : false,
                ]
            );
        }

        if (stripos($goal, 'segment_') !== false) {
            $show = 1;

            $fieldset->addField(
                "goal_type_option_id",
                "select",
                [
                    "label"    => __("Segment"),
                    "class"    => "required-entry",
                    "required" => true,
                    "options"  => $options = $this->segmentsFactory->create()
                                                                   ->toFormValues(),
                    "name"     => "goal_type_option_id",
                    "disabled" => ($current->getStatus() <= 2) ? true : false,
                ]
            );
        }

        if (stripos($goal, 'global') !== false) {
            $show = 2;
        }

        if (stripos($goal, 'campaign_') !== false) {
            $show = 1;
            $options = $this->campaignsFactory->create()
                                              ->toFormValuesNonAuto(
                                                  $current->getData('goal_type_option_id')
                                              );

            $fieldset->addField(
                "goal_type_option_id",
                "select",
                [
                    "label"    => __("Campaign"),
                    "class"    => "required-entry",
                    "required" => true,
                    "options"  => $options,
                    "disabled" => $current->getId() ? true : false,
                    "name"     => "goal_type_option_id",
                ]
            );
        }

        if ($show) {
            $fieldset->addField(
                "variation",
                "text",
                [
                    "label"    => __("Variation"),
                    "class"    => "required-entry",
                    "required" => true,
                    "name"     => "variation",
                    "note"     => "Valid values:"
                                  . "<br>10 – Expects final number to be 10"
                                  . "<br>+15 – Expects final number to be increased by 15"
                                  . "<br>-20 – Expects final number to be decreased by 20"
                                  . "<br>+25 – Expects final number to be increased by 25%"
                                  . "<br>-20% - Expects final number to be decreased by 20%",
                ]
            );

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

            $fieldset->addField(
                "description",
                "textarea",
                [
                    "label"    => __("Description"),
                    "class"    => "required-entry",
                    "required" => true,
                    "name"     => "description",
                ]
            );

            $dateFormat = $this->_localeDate->getDateFormat();

            $fieldset->addField(
                'from_date',
                'date',
                [
                    'name'        => 'from_date',
                    "class"       => "required-entry",
                    "disabled"    => ($current->getId() && $current->getStatus() <= 2) ? true : false,
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
        }

        $form->addValues($current->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
