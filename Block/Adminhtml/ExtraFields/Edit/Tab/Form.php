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
