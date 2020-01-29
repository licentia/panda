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

namespace Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * Form constructor.
     *
     * @param \Licentia\Panda\Model\TagsFactory        $tagsFactory
     * @param \Licentia\Equity\Model\SegmentsFactory   $segmentsFactory
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Registry              $registry
     * @param \Magento\Framework\Data\FormFactory      $formFactory
     * @param \Magento\Store\Model\System\Store        $systemStore
     * @param \Licentia\Panda\Model\ExtraFieldsFactory $extraFields
     * @param array                                    $data
     */
    public function __construct(
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFields,
        array $data = []
    ) {

        $this->tagsFactory = $tagsFactory;
        $this->systemStore = $systemStore;
        $this->segmentsFactory = $segmentsFactory;
        $this->extraFieldsFactory = $extraFields;
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

        $model = $this->_coreRegistry->registry('panda_subscriber');

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

        $form->setHtmlIdPrefix('subscriber_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('subscriber_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'email',
            'text',
            [
                'name'     => 'email',
                'class'    => 'validate-email',
                'label'    => __('Email'),
                'title'    => __('Email'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'cellphone',
            'text',
            [
                'name'  => 'cellphone',
                'label' => __('Cellphone'),
                'title' => __('Cellphone'),
                'note'  => __('Please use the format: countryCode-number. Eg: 1-555555555'),
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Status'),
                'name'     => 'status',
                'required' => true,
                'options'  => [
                    \Licentia\Panda\Model\Subscribers::STATUS_SUBSCRIBED   => __('Subscribed'),
                    \Licentia\Panda\Model\Subscribers::STATUS_UNSUBSCRIBED => __('Unsubscribed'),
                    \Licentia\Panda\Model\Subscribers::STATUS_NOT_ACTIVE   => __('Not Active'),
                    \Licentia\Panda\Model\Subscribers::STATUS_UNCONFIRMED  => __('Not Confirmed'),
                ],
            ]
        );

        $fieldset->addField(
            'firstname',
            'text',
            [
                'name'  => 'firstname',
                'label' => __('First Name'),
                'title' => __('First Name'),
            ]
        );

        $fieldset->addField(
            'lastname',
            'text',
            [
                'name'  => 'lastname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
            ]
        );

        $fieldset->addField(
            'segments',
            'multiselect',
            [
                'label'  => __('Assign to Segments'),
                'title'  => __('Assign to Segments'),
                'name'   => 'segments[]',
                'values' => $this->segmentsFactory->create()->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'tags',
            'multiselect',
            [
                'label'  => __('Tags'),
                'title'  => __('Tags'),
                'name'   => 'tags',
                'note'   => __('Tag this subscriber with theses tags'),
                'values' => $this->tagsFactory->create()
                                              ->getAllTagsValues(),
            ]
        );

        /* Check is single store mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'select',
                [
                    'name'     => 'store_id',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->systemStore->getStoreOptionHash(),
                ]
            );
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name'  => 'store_id',
                    'value' => $this->_storeManager->getStore(true)
                                                   ->getId(),
                ]
            );
            $model->setStoreId(
                $this->_storeManager->getStore(true)
                                    ->getId()
            );
        }

        $fieldset->addField(
            'previous_customer',
            "select",
            [
                "label"   => __('Previous Customer'),
                "name"    => 'previous_customer',
                "options" => ['1' => __('Yes'), '0' => __('No')],
                "note"    => __('If this subscriber should receive communications intended for previous customers'),
            ]
        );

        $extraFields = $this->extraFieldsFactory->create()->getCollection()->addFieldToFilter('is_active', 1);

        if ($extraFields->getSize() > 0) {
            $fieldset2 = $form->addFieldset(
                'extra_fieldset',
                ['legend' => __('Extra Fields'), 'class' => 'fieldset-wide']
            );

            foreach ($extraFields as $extraField) {

                $default = $extraField->getData('default_value');

                $class = '';

                if ($extraField->getType() == 'number') {
                    $class = 'validate - digits';
                }

                if ($extraField->getType() == 'options' && $extraField->getOptions()) {
                    $options = str_getcsv($extraField->getOptions());
                    $options = array_filter($options);
                    $values = [];
                    foreach ($options as $option) {
                        $values[] = ['label' => trim($option), 'value' => trim($option)];
                    }

                    $fieldset2->addField(
                        'field_' . $extraField->getEntryCode(),
                        'multiselect',
                        [
                            'values' => $values,
                            'value'  => $default,
                            'label'  => $extraField->getName(),
                            'title'  => $extraField->getName(),
                            'name'   => 'field_' . $extraField->getEntryCode(),
                        ]
                    );
                } elseif ($extraField->getType() == 'date') {

                    $dateFormat = $this->_localeDate->getDateFormat();

                    $fieldset2->addField(
                        'field_' . $extraField->getEntryCode(),
                        'date',
                        [
                            'value'       => $default,
                            'label'       => $extraField->getName(),
                            'title'       => $extraField->getName(),
                            'date_format' => $dateFormat,
                            'name'        => 'field_' . $extraField->getEntryCode(),
                        ]
                    );
                } else {

                    $fieldset2->addField(
                        'field_' . $extraField->getEntryCode(),
                        'text',
                        [
                            'value' => $default,
                            'class' => $class,
                            'label' => $extraField->getName(),
                            'title' => $extraField->getName(),
                            'name'  => 'field_' . $extraField->getEntryCode(),
                        ]
                    );
                }
            }
        }

        if ($model->getId()) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        if (!$model->getId()) {
            $this->getForm()->addValues(['status' => '1']);
        }

        return parent::_prepareForm();
    }
}
