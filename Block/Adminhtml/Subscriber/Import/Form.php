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

namespace Licentia\Panda\Block\Adminhtml\Subscriber\Import;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber\Import
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {

        $this->systemStore = $systemStore;
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
            'separator',
            'text',
            [
                'name'     => 'separator',
                'value'    => ',',
                'note'     => 'Use \t for [TAB]',
                'label'    => __('Field Separator'),
                'title'    => __('Field Separator'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name'   => 'store_id',
                'values' => $this->systemStore->getStoreValuesForForm(),
                'label'  => __('Store View'),
                'title'  => __('Store View'),
                'note'   => __('Used if no "store_id" column found in file'),
            ]
        );

        $fieldset->addField(
            'filename',
            "file",
            [
                "label"    => __('File to Import'),
                'required' => true,
                "name"     => 'filename',
                'note'     => '<strong>Possible Columns (Email required):</strong><br><br> ' . implode("<br> ",
                        \Licentia\Panda\Model\Subscribers::AVAILABLE_IMPORT_FIELDS),
            ]
        );

        $form->setAction($this->getUrl('*/*/import'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setEnctype('multipart/form-data');
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
