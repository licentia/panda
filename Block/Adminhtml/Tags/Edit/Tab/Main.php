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

namespace Licentia\Panda\Block\Adminhtml\Tags\Edit\Tab;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\Tags\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Licentia\Panda\Model\TagsFactory       $tagsFactory
     * @param \Licentia\Panda\Helper\Data             $pandaHelper
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->tagsFactory = $tagsFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry('panda_tag');

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
                "label" => __("Description"),
                "name"  => "description",
            ]
        );

        $fieldset->addField(
            "is_active",
            "select",
            [
                "label"    => __("Is Active"),
                "options"  => ['1' => __('Yes'), '0' => __('No')],
                "required" => true,
                "name"     => "is_active",
            ]
        );

        $form->addValues($current->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
