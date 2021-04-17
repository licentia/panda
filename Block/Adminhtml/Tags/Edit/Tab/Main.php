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
