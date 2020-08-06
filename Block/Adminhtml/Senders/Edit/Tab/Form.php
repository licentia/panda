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
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Licentia\Panda\Model\Service\Source\Auth
     */
    protected $authOptions;

    /**
     * @var \Licentia\Panda\Model\Service\Source\Ssl
     */
    protected $sslOptions;

    /**
     * @param \Licentia\Panda\Model\Service\Source\AuthFactory $auth
     * @param \Licentia\Panda\Model\Service\Source\SslFactory  $ssl
     * @param \Magento\Backend\Block\Template\Context          $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\Data\FormFactory              $formFactory
     * @param \Magento\Store\Model\System\Store                $systemStore
     * @param array                                            $data
     */
    public function __construct(
        \Licentia\Panda\Model\Service\Source\AuthFactory $auth,
        \Licentia\Panda\Model\Service\Source\SslFactory $ssl,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {

        $this->authOptions = $auth;
        $this->sslOptions = $ssl;
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

        $model = $this->_coreRegistry->registry('panda_sender');
        $model->setType('email');

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
            $fieldset->addField('sender_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );

        $fieldset->addField(
            "email",
            "text",
            [
                "label"    => __("Email"),
                "class"    => "required-entry validate-email",
                "required" => true,
                "name"     => "email",
                "note"     => __(
                    'Use a email domain name associated with your SMTP server, otherwise your email will most likely be considered SPAM'
                ),
            ]
        );

        $fieldset->addField(
            "reply_to",
            "text",
            [
                "label"    => __("Reply To"),
                "class"    => "validate-email",
                "required" => true,
                "name"     => "reply_to",
            ]
        );

        $fieldset2 = $form->addFieldset("nuntius_server", ["legend" => __("Server Information")]);

        $fieldset2->addField(
            "server",
            "text",
            [
                "label"    => __("Server Address"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "server",
            ]
        );

        $fieldset2->addField(
            "username",
            "text",
            [
                "label"    => __("Username"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "username",
            ]
        );

        $fieldset2->addField(
            "password",
            "password",
            [
                "label"    => __("Password"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "password",
            ]
        );

        $fieldset2->addField(
            "port",
            "text",
            [
                "label"    => __("Port"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "port",
            ]
        );

        $fieldset2->addField(
            "auth",
            "select",
            [
                "label"    => __("Authentication"),
                "class"    => "required-entry",
                "required" => true,
                'values'   => $this->authOptions->create()->toOptionArray(),
                "name"     => "auth",
            ]
        );

        $fieldset2->addField(
            "ssl",
            "select",
            [
                "label"    => __("SSL"),
                "class"    => "required-entry",
                "required" => true,
                'values'   => $this->sslOptions->create()->toOptionArray(),
                "name"     => "ssl",
            ]
        );
        $fieldset2->addField(
            "headers",
            "textarea",
            [
                "label" => __("Message Headers"),
                'note'  => 'One per line "name|value". Ex:</br>X-Mailer | PHP</br> Dynamic vars: {campaignId} {fromEmail} {fromName} {toEmail} {toName} {campaignName} {subject}',
                "name"  => "headers",
            ]
        );

        $fieldset3 =
            $form->addFieldset("nuntius_bounces", ["legend" => __("Bounces Configuration")]);

        $fieldset3->addField(
            "bounces_email",
            "text",
            [
                "label" => __("Bounce Email Reception"),
                "name"  => "bounces_email",
                "note"  => 'Please insert an email to receive campaigns bounces. <br><font color="red">WARNING: DO NOT use this email for anything else. We strongly recommend you to create an email account just for this purpose. Emails in this inbox will be removed automatically</font>',
            ]
        );

        $fieldset3->addField(
            "bounces_server",
            "text",
            [
                "label" => __("Server Address"),
                "name"  => "bounces_server",
            ]
        );

        $fieldset3->addField(
            "bounces_username",
            "text",
            [
                "label" => __("Username"),
                "name"  => "bounces_username",
                "note"  => 'Usually the same as the bounce email',
            ]
        );

        $fieldset3->addField(
            "bounces_password",
            "password",
            [
                "label" => __("Password"),
                "name"  => "bounces_password",
            ]
        );

        $fieldset3->addField(
            "bounces_port",
            "text",
            [
                "label" => __("Port"),
                "name"  => "bounces_port",
            ]
        );

        $fieldset3->addField(
            "bounces_auth",
            "select",
            [
                "label"  => __("Authentication"),
                'values' => $this->authOptions->create()->toOptionArray(),
                "name"   => "bounces_auth",
            ]
        );

        $fieldset3->addField(
            "bounces_ssl",
            "select",
            [
                "label"  => __("SSL"),
                'values' => $this->sslOptions->create()->toOptionArray(),
                "name"   => "bounces_ssl",
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
