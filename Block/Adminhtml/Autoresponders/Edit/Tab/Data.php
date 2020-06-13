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

namespace Licentia\Panda\Block\Adminhtml\Autoresponders\Edit\Tab;

/**
 * Class Data
 *
 * @package Licentia\Panda\Block\Adminhtml\Autoresponders\Edit\Tab
 */
class Data extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected $sendersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

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
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollection;

    /**
     * @var \Licentia\Panda\Model\LinksFactory
     */
    protected $linksFactory;

    /**
     * @var \Magento\Sales\Model\Order\ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesGlobalFactory
     */
    protected $templatesGlobalFactory;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * @param \Licentia\Panda\Model\TagsFactory                             $tagsFactory
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Licentia\Panda\Helper\Data                                   $pandaHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface               $productRepository
     * @param \Magento\Store\Model\System\Store                             $systemStore
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection
     * @param \Magento\Sales\Model\Order\ConfigFactory                      $configFactory
     * @param \Licentia\Panda\Model\CampaignsFactory                        $campaignsFactory
     * @param \Licentia\Panda\Model\LinksFactory                            $linksFactory
     * @param \Licentia\Forms\Model\FormsFactory                            $formsFactory
     * @param \Licentia\Panda\Model\AutorespondersFactory                   $autorespondersFactory
     * @param \Licentia\Panda\Model\SendersFactory                          $sendersFactory
     * @param \Licentia\Panda\Model\ExtraFieldsFactory                      $extraFieldsFactory
     * @param \Licentia\Panda\Model\TemplatesGlobalFactory                  $templatesGlobalFactory
     * @param \Licentia\Equity\Model\SegmentsFactory                        $segmentsFactory
     * @param array                                                         $data
     */
    public function __construct(
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection,
        \Magento\Sales\Model\Order\ConfigFactory $configFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\LinksFactory $linksFactory,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        array $data = []
    ) {

        $this->tagsFactory = $tagsFactory;
        $this->linksFactory = $linksFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->systemStore = $systemStore;
        $this->groupCollection = $groupCollection;
        $this->productRepository = $productRepository;
        $this->configFactory = $configFactory;
        $this->sendersFactory = $sendersFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->templatesGlobalFactory = $templatesGlobalFactory;
        $this->formsFactory = $formsFactory;
        $this->segmentsFactory = $segmentsFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry('panda_autoresponder');

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
        $fieldset2 = $form->addFieldset('content_fieldset', ['legend' => __('Content')]);

        $fieldset2->addField(
            'tags',
            'multiselect',
            [
                'label'  => __('Tags'),
                'title'  => __('Tags'),
                'name'   => 'tags',
                'note'   => __('Tag this autoresponder with theses tags'),
                'values' => $this->tagsFactory->create()
                                              ->getAllTagsValues(),
            ]
        );
        $options = $this->segmentsFactory->create()->getOptionArray('Any');
        $fieldset2->addField(
            'segments_ids',
            'multiselect',
            [
                'name'     => 'segments_ids[]',
                'label'    => __('Segment'),
                'title'    => __('Segment'),
                'required' => true,
                'values'   => $options,
            ]
        );
        $form->getElement('segments_ids')
             ->setData('size', count($options) > 7 ? 7 : count($options));

        $options = $this->systemStore->getStoreValuesForForm();
        array_unshift($options, ['label' => __('-- Any --'), 'value' => 0]);
        $fieldset2->addField(
            'store_id',
            'multiselect',
            [
                'name'     => 'store_id[]',
                'label'    => __('Store View'),
                'title'    => __('Store View'),
                'required' => true,
                'values'   => $options,
            ]
        );

        if ($this->_scopeConfig->getValue('panda_nuntius/info/customer_list')) {
            $fieldset2->addField(
                'previous_customers',
                "select",
                [
                    "label"   => __('Previous Customers'),
                    "name"    => 'previous_customers',
                    "options" => ['0' => __('No'), '1' => __('Yes')],
                    "note"    => __(
                        'If this autoresponder should be sent to previous customers instead of current subscribers.'
                    ),
                ]
            );
        }

        $fieldset2->addField(
            'is_active',
            "select",
            [
                "label"   => __('Status'),
                "options" => ['1' => __('Active'), '0' => __('Inactive')],
                "name"    => 'is_active',
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat();

        $fieldset2->addField(
            'from_date',
            'date',
            [
                'name'        => 'from_date',
                'date_format' => $dateFormat,
                'label'       => __('Active From Date'),
            ]
        );

        $fieldset2->addField(
            'to_date',
            'date',
            [
                'name'        => 'to_date',
                'date_format' => $dateFormat,
                'label'       => __('Active To Date'),
            ]
        );

        $fieldset2->addField(
            'track',
            'select',
            [
                'name'    => 'track',
                'type'    => 'options',
                'options' => [2 => __('Use Config'), 1 => __('Yes'), 0 => __('No')],
                'label'   => __('Track Stats?'),
                'note'    => __('If you want to track stats/conversions in this campaign'),
            ]
        );

        $fieldset2->addField(
            'autologin',
            'select',
            [
                'name'    => 'autologin',
                'type'    => 'options',
                'options' => [2 => __('Use Config'), 1 => __('Yes'), 0 => __('No')],
                'label'   => __('Auto-Login?'),
                'note'    => __('If customers should be logged in automatically after opening a campaign link'),
            ]
        );

        $fieldset = $form->addFieldset('canc_fieldset', ['legend' => __('Cancellation')]);

        $options = $this->autorespondersFactory->create()->toOptionValues();

        $fieldset->addField(
            'cancellation',
            "multiselect",
            [
                "label"  => __('Cancellation Triggers'),
                "values" => $options,
                "name"   => 'cancellation',
                "note"   => __("When selected events occur, pending emails won't be sent."),
            ]
        );

        $this->setForm($form);

        if ($current) {
            $form->addValues($current->getData());
        }

        if (!$current->getData('autologin') && !$current->getId()) {
            $form->addValues(['autologin' => 2]);
            $form->addValues(['track' => 2]);
        }

        return parent::_prepareForm();
    }
}
