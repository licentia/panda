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
 * @modified   29/03/20, 03:20 GMT
 *
 */

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Email
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Sms extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollection;

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
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

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
     * @param \Magento\Cms\Model\Wysiwyg\Config                             $wysiwygConfig
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
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        $this->tagsFactory = $tagsFactory;
        $this->sendersFactory = $sendersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->systemStore = $systemStore;
        $this->groupCollection = $groupCollection;
        $this->formsFactory = $formsFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->wysiwygConfig = $wysiwygConfig;

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
                "values"   => $this->sendersFactory->create()->getSenders('sms'),
                "name"     => "sender_id",
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
                        'draft'   => __("Yes - Leave it alone"),
                        'standby' => __("No - Go ahead, proceed as usual"),
                    ],
                    'note'    => __(
                        "The option to mark a campaign as draft will be removed when the campaign is queued"
                    ),
                ]
            );
        }

        $js = '
        <style type="text/css">#togglemessage{ display:none !important;}</style>
            ';

        $wysiwygConfig = $this->wysiwygConfig->getConfig(
            ['tab_id' => $this->getTabId()]
        );
        $wysiwygConfig->setData('hidden', 1);
        $wysiwygConfig->setData('add_images', false);

        $fieldset->addField(
            'message',
            'editor',
            [
                "label"    => __("Message"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "message",
                'config'   => $wysiwygConfig,
                'wysiwyg'  => true,
            ]
        )
                 ->setAfterElementHtml($js);

        $fieldset->addField(
            'tags',
            'multiselect',
            [
                'label'  => __('Tags'),
                'title'  => __('Tags'),
                'name'   => 'tags',
                'note'   => __('Tag this campaign with these tags'),
                'values' => $this->tagsFactory->create()
                                              ->getAllTagsValues(),
            ]
        );

        $options = $this->segmentsFactory->create()->getOptionArray('Ignore Field');
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
        $form->getElement('segments_ids')
             ->setData('size', count($options) > 7 ? 7 : count($options));

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
                "note"  => __("The maximum number of SMS to send for this campaign"),
            ]
        );

        $fieldset->addField(
            "max_queue_hour",
            "text",
            [
                'class' => 'small_input',
                "label" => __("Max Messages/Hour"),
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

        $fieldset->addField('type', 'hidden', ['name' => 'type', 'value' => 'sms']);

        if (!$current->getData('track') && !$current->getId()) {
            $form->addValues(['track' => 2]);
            $form->addValues(['autologin' => 2]);
            $form->addValues(['max_queue_hour' => 0]);
        }

        return parent::_prepareForm();
    }
}
