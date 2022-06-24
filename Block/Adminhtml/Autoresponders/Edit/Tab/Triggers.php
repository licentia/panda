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

namespace Licentia\Panda\Block\Adminhtml\Autoresponders\Edit\Tab;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\Autoresponders\Edit\Tab
 */
class Triggers extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Magento\Sales\Model\Order\ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollection;

    /**
     * @var \Licentia\Panda\Model\LinksFactory
     */
    protected $linksFactory;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentData;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerAttributes;

    /**
     * Triggers constructor.
     *
     * @param \Licentia\Panda\Model\Source\CustomerAttributes               $customerAttributes
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface               $productRepository
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Licentia\Panda\Helper\Data                                   $pandaHelper
     * @param \Magento\Store\Model\System\Store                             $systemStore
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection
     * @param \Magento\Sales\Model\Order\ConfigFactory                      $configFactory
     * @param \Licentia\Panda\Model\CampaignsFactory                        $campaignsFactory
     * @param \Licentia\Panda\Model\LinksFactory                            $linksFactory
     * @param \Licentia\Forms\Model\FormsFactory                            $formsFactory
     * @param \Licentia\Panda\Model\AutorespondersFactory                   $autorespondersFactory
     * @param \Magento\Shipping\Model\Config                                $shippingConfig
     * @param \Magento\Payment\Helper\Data                                  $paymentData
     * @param array                                                         $data
     */
    public function __construct(
        \Licentia\Panda\Model\Source\CustomerAttributes $customerAttributes,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection,
        \Magento\Sales\Model\Order\ConfigFactory $configFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\LinksFactory $linksFactory,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->formsFactory = $formsFactory;
        $this->linksFactory = $linksFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->systemStore = $systemStore;
        $this->groupCollection = $groupCollection;
        $this->productRepository = $productRepository;
        $this->configFactory = $configFactory;
        $this->paymentData = $paymentData;
        $this->shippingConfig = $shippingConfig;
        $this->customerAttributes = $customerAttributes;
    }

    /**
     * @return void
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry('panda_autoresponder');

        $event = $this->getRequest()->getParam('event');
        $campaignId = $this->getRequest()->getParam('campaign_id');
        $productId = $this->getRequest()->getParam('product');
        $type = $this->getRequest()->getParam('type');
        $linkId = $this->getRequest()->getParam('link_id');

        if ($current->getId()) {
            $event = $current->getEvent();
            $campaignId = $current->getCampaignId();
        } else {
            $current->setData('event', $event);
            $current->setData('campaign_id', $campaignId);
            $current->setData('product', $productId);
            $current->setData('link_id', $linkId);
        }

        $location =
            $this->getUrl(
                '*/*/*',
                [
                    'type' => $type,
                    'id'   => $this->getRequest()->getParam('id'),
                ]
            );

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

        $options = $this->autorespondersFactory->create()->toOptionArray();

        if (!$event) {
            array_unshift($options, __('Please Select'));
        }

        $script = "<script type='text/javascript'>require(['prototype'], function () {

     goToUrl = {
        go: function(url) {

        var els=new Array('event','campaign_id','link_id','product');

        var temp = '';
        Form.getElements( $('edit_form') ).each(function(item){

            if(els.indexOf(item.name)==-1)
            return;

            if(item.value.length==0)
            return;

            if(item.name =='form_key')
            return;

           temp += item.name+'/'+item.value+'/';

           });

             window.location=url+temp
 }
         }});</script>";

        if ($event) {
            $fieldset->addField(
                'name',
                'text',
                [
                    'name'     => 'name',
                    'label'    => __('Name'),
                    'title'    => __('Name'),
                    "required" => true,
                ]
            );
        }

        $fieldset->addField(
            'event',
            'hidden',
            [
                'name'    => 'event',
                'default' => $event,
            ]
        );

        if ($event == 'campaign_open' || $event == 'campaign_link' || $event == 'campaign_click') {
            $options = $this->campaignsFactory->create()->toFormValuesNonAuto();
            if ($event == 'campaign_link') {
                $location = "goToUrl.go('$location')";
                if (!$campaignId) {
                    $options[''] = __('Please Select');
                }
            } else {
                $location = '';
            }

            $fieldset->addField(
                'campaign_id',
                "select",
                [
                    "label"    => __('Campaign'),
                    "options"  => $options,
                    "name"     => 'campaign_id',
                    "disabled" => $current->getId() ? true : false,
                    'value'    => '',
                    "onchange" => $location,
                    "required" => true,
                ]
            )->setAfterElementHtml($script);
        }

        if ($event == 'campaign_link' && $campaignId) {
            $links = $this->linksFactory->create()->getHashForCampaign($campaignId);

            if (count($links) == 0) {
                $links = ['' => __('No links detected in selected campaign')];
            }

            $fieldset->addField(
                'link_id',
                "select",
                [
                    "label"    => __('Link'),
                    "options"  => $links,
                    "required" => true,
                    "name"     => 'link_id',
                ]
            );
        }

        $config = $current->getConfigInfo($event);
        if (isset($config['days']) && $config['days'] == true) {
            $fieldset->addField(
                'days_before',
                'text',
                [
                    'name'     => 'days_before',
                    'label'    => __('Number of Days'),
                    'title'    => __('Number of Days'),
                    "note"     => __(
                        'How many days before this event happens should the autoresponder be triggered'
                    ),
                    "required" => true,
                    "class"    => 'validate-digits',
                ]
            );
        }

        if ($event == 'new_form_entry') {
            $options = $this->formsFactory->create()->toFormValues();

            $fieldset->addField(
                'form_id',
                'select',
                [
                    'options' => $options,
                    'name'    => 'form_id',
                    'label'   => __('Form'),
                    'title'   => __('Form'),
                ]
            );
        }

        if ($event == 'product_cycle') {
            $fieldset->addField(
                'products',
                'textarea',
                [
                    'name'  => 'products',
                    'label' => __('Products'),
                    'title' => __('Products'),
                    'note'  => __(
                        'Send the Autoresponder only for these products. One SKU per line. [2] One/both of these fields is required'
                    ),
                ]
            );

            $categories = $this->pandaHelper->getCategories();
            $fieldset->addField(
                'categories',
                'multiselect',
                [
                    'name'   => 'categories[]',
                    'label'  => __('Product categories'),
                    'title'  => __('Product Categories'),
                    'note'   => __('Send only if the product is in one of the selected categories. [2] One/both of these fields is required'),
                    'values' => $categories,
                ]
            );
        }

        if ($event == 'internal_event') {
            $fieldset->addField(
                'observers',
                'text',
                [
                    'name'     => 'observers',
                    'label'    => __('Magento Observers'),
                    'title'    => __('Magento Observers'),
                    "note"     => 'Magento Internal Observers Names. Separate multiples with a comma. eg: customer_register_success, sales_order_place_after',
                    "required" => true,
                ]
            );
        }

        if ($event == 'order_product') {
            $fieldset->addField(
                'product',
                'textarea',
                [
                    'name'     => 'product',
                    'label'    => __('Product SKU'),
                    'title'    => __('Product SKU'),
                    "note"     => 'One SKU per line.' . ' <a target="_blank" href="' .
                                  $this->getUrl('catalog/product') .
                                  '">' . __('Go to Product Listing') . '.</a>',
                    "required" => true,
                ]
            );
        }

        if ($event == 'customer_group') {
            $groups = $this->groupCollection->create()->toOptionArray();

            $fieldset->addField(
                'old_customer_group_id',
                'multiselect',
                [
                    'name'   => 'old_customer_group_id',
                    'label'  => __('Previous Customer Group'),
                    'title'  => __('Previous Customer Group'),
                    "values" => $groups,
                ]
            );

            $fieldset->addField(
                'new_customer_group_id',
                'multiselect',
                [
                    'name'     => 'new_customer_group_id',
                    'label'    => __('New Customer Group'),
                    'title'    => __('New Customer Group'),
                    "values"   => $groups,
                    "required" => true,
                ]
            );
        }

        if ($event == 'customer_attribute') {
            $attributes = $this->customerAttributes->toOptionArray();

            $fieldset->addField(
                'customer_attribute',
                'select',
                [
                    'name'   => 'customer_attribute',
                    'label'  => __('Customer Attribute'),
                    'title'  => __('Customer Attribute'),
                    "values" => $attributes,
                ]
            );

            $fieldset->addField(
                'customer_attribute_fire',
                'select',
                [
                    'name'    => 'customer_attribute_fire',
                    'label'   => __('Fire When'),
                    'title'   => __('Fire When'),
                    "options" => [
                        'new_not_empty'               => __('New value is not empty'),
                        'new_not_empty_old_not_empty' => __('New value is not empty and old value was not empty'),
                        'new_not_empty_old_empty'     => __('New value is not empty and old value was empty'),
                        'new_empty'                   => __('New value is empty'),
                        'new_empty_old_not_empty'     => __('New value is empty and old value was not empty'),
                        'new_empty_old_empty'         => __('New value is empty and old value was empty'),
                    ],
                ]
            );
        }

        if ($event && $event != 'new_account' && $event != 'order_status') {
            $fieldset->addField(
                'send_once',
                "select",
                [
                    "label"   => __('Apply Only Once?'),
                    "options" => ['1' => __('Yes'), '0' => __('Every Time Occurs')],
                    "name"    => 'send_once',
                    "value"   => '1',
                    "note"    => __('To the same subscriber'),
                ]
            );
        }

        if ($event == 'utm_campaign') {
            for ($i = 1; $i <= 3; $i++) {
                $fieldsetUtm[$i] = $form->addFieldset(
                    'params_fieldset_' . $i,
                    ['legend' => __('UTM Params') . ' ' . $i]
                );

                $htmlOptions = '
                <script type="text/javascript">

                require(["jquery"],function ($){

                toggleControlsOptionsMain' . $i . ' = {
                    run: function() {
                        if($("#utm_parameter_' . $i . '").val() == "0" ){
                            $("#utm_match_' . $i . '").parent().parent().hide();
                            $("#utm_condition_' . $i . '").parent().parent().parent().hide();
                        }else{
                            $("#utm_match_' . $i . '").parent().parent().show();
                            $("#utm_condition_' . $i . '").parent().parent().parent().show();
                        }
                    }
                }
                window.toggleControlsOptionsMain = toggleControlsOptionsMain' . $i . ';
                $(function() {
                    toggleControlsOptionsMain' . $i . '.run();
                });

                });
                </script>
                ';

                $onChange = 'toggleControlsOptionsMain' . $i . '.run()';
                $optionsValues = \Licentia\Panda\Model\Autoresponders::UTM_URL_PARAMS;

                array_unshift($optionsValues, __('-- Select --'));

                if ($i == 1) {
                    $htmlOptions = '';
                    $onChange = '';
                    array_shift($optionsValues);
                }

                $fieldsetUtm[$i]->addField(
                    'utm_parameter_' . $i,
                    "select",
                    [
                        "label"    => __('UTM Parameter'),
                        "options"  => $optionsValues,
                        "name"     => 'utm_parameter[]',
                        'onchange' => $onChange,
                    ]
                )->setAfterElementHtml($htmlOptions);

                $htmlOptions = '
                <script type="text/javascript">

                require(["jquery"],function ($){

                toggleControlsOptions' . $i . ' = {
                    run: function() {
                        if($("#utm_condition_' . $i . '").val() == "exists" ){
                            $("#utm_match_' . $i . '").parent().parent().hide();
                        }else{
                            $("#utm_match_' . $i . '").parent().parent().show();
                        }
                    }
                }
                window.toggleControlsOptions = toggleControlsOptions' . $i . ';
                $(function() {
                    toggleControlsOptions' . $i . '.run();
                });

                });
                </script>
                ';

                $fieldsetUtm[$i]->addField(
                    'utm_condition_' . $i,
                    "select",
                    [
                        "label"    => __('Condition'),
                        "options"  => [
                            'exists'         => __('Is Present/Any Value'),
                            'is'             => __('Is'),
                            'starts'         => __('Starts With'),
                            'ends'           => __('Ends With'),
                            'contains'       => __('Contains'),
                            'doesnotcontain' => __('Does Not Contain'),
                            'wildcard'       => __('Wildcard'),
                        ],
                        "name"     => 'utm_condition[]',
                        'onchange' => 'toggleControlsOptions' . $i . '.run()',
                    ]
                )->setAfterElementHtml($htmlOptions);

                $fieldsetUtm[$i]->addField(
                    'utm_match_' . $i,
                    "text",
                    [
                        "label" => __('Value to Match'),
                        "name"  => "utm_match[]",
                        "note"  => __(
                            'Separate Multiple Values with a comma , Leave empty to ignore. Wildcard example: some*text'
                        ),
                    ]
                );
            }
        }

        if (stripos($event, 'order') !== false) {
            $options = $this->paymentData->getPaymentMethodList(true, true);

            $fieldset->addField(
                'payment_method',
                "multiselect",
                [
                    "label"  => __('Match Payment Method'),
                    "values" => $options,
                    "name"   => 'payment_method',
                ]
            );

            $methods = $this->shippingConfig->getActiveCarriers();
            $options = [];
            foreach ($methods as $_ccode => $_carrier) {
                if ($_methods = $_carrier->getAllowedMethods()) {
                    foreach ($_methods as $_mcode => $_method) {
                        $_code = $_ccode . '_' . $_mcode;
                        $options[] = ['value' => $_code, 'label' => $_method];
                    }
                }
            }
            $fieldset->addField(
                'shipping_method',
                "multiselect",
                [
                    "label"  => __('Match Shipping Method'),
                    "values" => $options,
                    "name"   => 'shipping_method',
                ]
            );
        }

        if (($event == 'new_account' || $event == 'order_status')) {
            $fieldset->addField(
                'send_once',
                "hidden",
                [
                    "value" => 1,
                    "name"  => 'send_once',
                ]
            );
        }

        if ($event == 'order_status') {
            $fieldset->addField(
                'order_status',
                "select",
                [
                    "label"   => __('New Status'),
                    "options" => $this->configFactory->create()
                                                     ->getStatuses(),
                    "name"    => 'order_status',
                ]
            );
        }

        if (stripos($event, 'order_status_') !== false) {
            $status = $this->configFactory->create()->getStatuses();

            $status[0] = __('-- Ignore --');

            unset($status[str_replace("order_status_", '', $event)]);

            $fieldset->addField(
                'order_status_previous',
                "select",
                [
                    "label"   => __('From Status'),
                    "options" => $status,
                    "name"    => 'order_status_previous',
                ]
            );

            $fieldset->addField(
                'order_status_time',
                'text',
                [
                    'name'  => 'order_status_time',
                    'label' => __('Max. Time'),
                    'title' => __('Max. Time'),
                    "note"  => __(
                        'Do not send if more than X minutes have passed since the previous status was set. (use 0 or leave blank to ignore)'
                    ),
                ]
            );
        }

        if ($event == 'new_search') {
            $fieldset->addField(
                'search',
                'text',
                [
                    'name'     => 'search',
                    'label'    => __('Search Value'),
                    'title'    => __('Search Value'),
                    'required' => true,
                    "note"     => __('Separate multiple values with a comma ,'),
                ]
            );

            $fieldset->addField(
                'search_option',
                "select",
                [
                    "label"   => __('Query String Match'),
                    "options" => ['eq' => __('Equal'), 'like' => __('Contains')],
                    "name"    => 'search_option',
                ]
            );
        }

        $this->setForm($form);
        if ($event = 'utm_campaign') {
            for ($i = 1; $i <= 3; $i++) {
                $form->addValues(['utm_match_' . $i => $current->getData('utm_match/' . ($i - 1))]);
                $form->addValues(['utm_parameter_' . $i => $current->getData('utm_parameter/' . ($i - 1))]);
                $form->addValues(['utm_condition_' . $i => $current->getData('utm_condition/' . ($i - 1))]);
            }
        }
        if ($current) {
            $form->addValues($current->getData());
        }
    }

    /**
     * @return mixed
     */
    public function getAutoresponder()
    {

        return $this->_coreRegistry->registry('panda_autoresponder');
    }
}
