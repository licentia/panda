<?php

/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Recurring
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Recurring extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {

        return __('Sending Options');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {

        return __('Sending Options');
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
     * Recurring constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Licentia\Panda\Model\CampaignsFactory  $campaignsFactory
     * @param \Licentia\Panda\Model\TemplatesFactory  $templatesFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->campaignsFactory = $campaignsFactory;
        $this->templatesFactory = $templatesFactory;

        $this->setTemplate('campaigns/recurring.phtml');
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry("panda_campaign");

        if ($current->getId()) {
            $extraRun =
                __('This campaign has already run for %1 time(s)', $current->getRunTimes());
        } else {
            $extraRun = '';
        }

        $campaignModel = $this->campaignsFactory->create();

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

        $fieldset = $form->addFieldset('recurring_fieldset', ['legend' => __('Recurring Profile')]);

        $dateFormat = $this->_localeDate->getDateFormat();
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::SHORT
        );

        $fieldset->addField(
            'subscriber_time',
            'select',
            [
                'name'    => 'subscriber_time',
                'type'    => 'options',
                'options' => [0 => __('No'), 1 => __('Yes')],
                'label'   => __('Dynamic Deployment?'),
                'value'   => '1',
                'note'    => __(
                    'Use this option if you want to maximize conversion rates. This will send messages at hours that users are most likely to view them. There is a span of up to 24 hours with this option enabled for message delivery'
                ),
            ]
        );

        $fieldset->addField(
            "recurring",
            "select",
            [
                "label"    => __("Recurring Campaign?"),
                "class"    => "required-entry",
                "onchange" => "recurringControl.load()",
                "required" => true,
                "values"   => $campaignModel::getCronList(),
                "name"     => "recurring",
            ]
        );

        $fieldset->addField(
            'deploy_at',
            'date',
            [
                'name'         => 'deploy_at',
                'date_format'  => $dateFormat,
                'time_format'  => $timeFormat,
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT,
                'label'        => __('Send Date'),
                "note"         => __(
                    "Start queuing this message at the indicated time. Please note, a campaign will not be queued if the status is 'Draft'"
                ),
            ]
        );

        $fieldset->addField(
            "recurring_daily",
            "multiselect",
            [
                "label"  => __("In Which days?"),
                "values" => $campaignModel::getDaysList(),
                "name"   => "recurring_daily",
            ]
        );

        $fieldset->addField(
            "recurring_unique",
            "select",
            [
                "label"  => __("Unique Recipient?"),
                "note"   => __(
                    "Use this option if you do not want this campaign to be sent to the same recipient more than once. Tip: useful to use in conjunction with segments. That means, no matter how many times this recurring campaign runs, no subscriber will receive it twice."
                ),
                "values" => ['0' => __('No'), '1' => __('Yes')],
                "name"   => "recurring_unique",
            ]
        );

        $fieldset->addField(
            "recurring_day",
            "select",
            [
                "label"  => __("In Which day?"),
                "values" => $campaignModel::getDaysList(),
                "name"   => "recurring_day",
            ]
        );

        $fieldset->addField(
            "recurring_monthly",
            "select",
            [
                "label"  => __("In which day?"),
                "values" => $campaignModel::getDaysMonthsList(),
                "name"   => "recurring_monthly",
            ]
        );

        $fieldset->addField(
            "recurring_month",
            "select",
            [
                "label"  => __("In which month?"),
                "values" => $campaignModel::getMonthsList(),
                "name"   => "recurring_month",
            ]
        );

        $fieldset->addField(
            "recurring_time",
            "select",
            [
                "label"  => __("Run around"),
                "class"  => "required-entry",
                "values" => $campaignModel::getRunAroundList(),
                "name"   => "recurring_time",
                "note"   => __("Please choose the start hour for this profile"),
            ]
        );

        $fieldset->addField(
            'recurring_first_run',
            'date',
            [
                'name'        => 'recurring_first_run',
                'date_format' => $dateFormat,
                'required'    => true,
                'label'       => __('First Run'),
                "note"        => __("First time this campaign should run"),
            ]
        );

        $fieldset->addField(
            'run_until',
            'date',
            [
                'name'        => 'run_until',
                'date_format' => $dateFormat,
                'label'       => __('End Date'),
                "note"        => __("How long should this campaign run."),
            ]
        );

        $fieldset->addField(
            "run_times",
            "text",
            [
                "label" => __("Running times"),
                "name"  => "run_times",
                "note"  => __("How many times should this campaign run. " . $extraRun),
            ]
        );

        $fieldset->addField(
            "end_method",
            "select",
            [
                "label"  => __("Stop sending when"),
                "name"   => "end_method",
                'values' => [
                    'both'      => __('We reach "End Date" AND "Running Times"'),
                    'run_until' => __('We reach "End Date"'),
                    'number'    => __('We reach "Running Times"'),
                    'any'       => __('We reach "End Date" OR "Running Times"'),
                ],
            ]
        );

        $this->setForm($form);

        if ($current->getData()) {
            $form->addValues($current->getData());

            if ($current->getData('recurring_first_run')) {
                $form->getElement('recurring_first_run')
                     ->setValue($this->_localeDate->date(new \DateTime($current->getData('recurring_first_run'))));
            }
        } else {
            $form->setValues(['deploy_at' => $this->_localeDate->date()->format('Y-m-d H:i:s')]);
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
