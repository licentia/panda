<?php

/**
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

namespace Licentia\Panda\Block\Adminhtml\ExtraFields\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\ExtraFields\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

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

        $current = $this->_coreRegistry->registry('panda_extra');

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

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'title'    => __('Name'),
                'required' => true,
            ]
        );

        $html = '
                <script type="text/javascript">
                require(["jquery"],function ($){
                toggleControlsType = {
                    run: function() {
                        if($("#type").val() == "options"){
                            $("#options").parent().parent().show();
                        }else{
                            $("#options").parent().parent().hide();
                        }
                    }
                }
                window.toggleControlsType = toggleControlsType;
                $(function() {
                    toggleControlsType.run();
                });

                });
                </script>
                ';

        $fieldset->addField(
            'type',
            "select",
            [
                "label"    => __('Type'),
                'onchange' => 'toggleControlsType.run()',
                "options"  => [
                    'text'    => __('Text'),
                    'options' => __('Multiple Options'),
                    'number'  => __('Number'),
                    'date'    => __('Date'),
                ],
                "name"     => 'type',
            ]
        )
                 ->setAfterElementHtml($html);

        $fieldset->addField(
            'options',
            "textarea",
            [
                "label" => __('Field Options'),
                "name"  => 'options',
                'note'  => __("One Per Line"),
            ]
        );

        $fieldset->addField(
            'default_value',
            'text',
            [
                'name'  => 'default_value',
                'label' => __('Default Value'),
                'title' => __('Default Value'),
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

        $form->addValues($current->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
