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
 * @modified   27/03/20, 02:38 GMT
 *
 */

namespace Licentia\Panda\Block\Adminhtml\Support\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Support\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Backend\Model\Session          $session
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Session $session,
        array $data = []
    ) {

        $this->session = $session;
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

        $fieldset->addField(
            'first_name',
            "text",
            [
                "label"    => __('First Name'),
                "required" => true,
                "name"     => 'first_name',
            ]
        );

        $fieldset->addField(
            'last_name',
            "text",
            [
                "label"    => __('Last Name'),
                "required" => true,
                "name"     => 'last_name',
            ]
        );

        $fieldset->addField(
            'email',
            "text",
            [
                "label"    => __('Email'),
                "required" => true,
                "name"     => 'email',
            ]
        );

        $eOp = [];
        $eOp[] = ['value' => 'bug', 'label' => __('Bug Report')];
        $eOp[] = ['value' => 'request', 'label' => __('Request')];
        $eOp[] = ['value' => 'other', 'label' => __('Other Information')];

        $fieldset->addField(
            'reason',
            "select",
            [
                "label"    => __('Contact Reason'),
                "required" => true,
                'values'   => $eOp,
                "name"     => 'reason',
            ]
        );

        $fieldset->addField(
            'attach',
            "select",
            [
                "label"    => __('Attach Debug Information'),
                "required" => true,
                "value"    => '1',
                'options'  => ['1' => __('Yes'), '0' => __('No')],
                "name"     => 'attach',
                "note"     => "Contents from the Debug Tab. It's recommended that you attach this file for a quicker issue resolution",
            ]
        );

        $fieldset->addField(
            'message',
            "textarea",
            [
                "label"    => __('Message'),
                "required" => true,
                "name"     => 'message',
                "note"     => __('Please be as descriptive as possible'),
            ]
        );

        $form->addValues($this->session->getFormData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
