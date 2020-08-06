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

namespace Licentia\Panda\Block\Adminhtml\Senders\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Senders\Edit\Tab
 */
class Sms extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Framework\Data\FormFactory       $formFactory
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {

        $this->objectManager = $objectManagerInterface;
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

        $model = $this->_coreRegistry->registry('panda_sender');
        $model->setType('sms');

        if ($this->_request->getParam('gateway')) {
            $model->setData('gateway', $this->_request->getParam('gateway'));
        }

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

        $fieldset->addField('type', 'hidden', ['name' => 'type', 'value' => 'sms']);

        if ($model->getId()) {
            $fieldset->addField('sender_id', 'hidden', ['name' => 'id']);
        }

        $gateways = $model->getGateways();

        if ($model->getGateway()) {
            array_shift($gateways);
        }

        $url = $this->getUrl('*/*/*', ['ctype' => 'sms']);
        $fieldset->addField(
            "gateway",
            "select",
            [
                "label"    => __("Gateway"),
                'onchange' => "window.location='{$url}gateway/'+this.value",
                "name"     => "gateway",
                'options'  => $gateways,
                'disabled' => $model->getId() ? true : false,
                'note'     => __("New SMS gateway services are added per request. Please open a support ticket if you need one not listed above"),
            ]
        );

        if ($model->getGateway()) {

            /** @var \Licentia\Panda\Model\Service\Sms\Core $service */
            $service = $this->objectManager->get(
                '\Licentia\Panda\Model\Service\Sms\\' . ucfirst(trim($model->getGateway()))
            );

            foreach ($service->getFields() as $name => $info) {
                $info['name'] = $name;
                $fieldset->addField($name, $info['type'], $info);
            }
        }

        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
