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

namespace Licentia\Panda\Block\Adminhtml\Templates\Edit\Tab;

/**
 * Class Help
 *
 * @package Licentia\Panda\Block\Adminhtml\Formulas\Edit\Tab
 */
class Variations extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollection;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * Variations constructor.
     *
     * @param \Magento\Framework\Registry                                   $coreRegistry
     * @param \Licentia\Panda\Helper\Data                                   $helperData
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Magento\Store\Model\System\Store                             $systemStore
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection
     * @param \Licentia\Panda\Model\ExtraFieldsFactory                      $extraFieldsFactory
     * @param \Licentia\Equity\Model\SegmentsFactory                        $segmentsFactory
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Licentia\Panda\Helper\Data $helperData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        array $data = []
    ) {

        $this->systemStore = $systemStore;
        $this->groupCollection = $groupCollection;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->helperData = $helperData;
        $this->registry = $coreRegistry;
        $this->segmentsFactory = $segmentsFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->registry->registry('panda_template');

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

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('content_fieldset', ['legend' => __('Content')]);

        $options = $this->segmentsFactory->create()->getOptionArray('Any');
        $fieldset->addField(
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

        if (!$this->_storeManager->isSingleStoreMode()) {
            $options = $this->systemStore->getStoreValuesForForm();
            array_unshift($options, ['label' => __('-- Any --'), 'value' => 0]);
            $fieldset->addField(
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
        }

        $fieldset->addField(
            'gender',
            "select",
            [
                "label"  => __('Gender'),
                "values" => [
                    ['label' => __('Male'), 'value' => 'male'],
                    ['label' => __('Female'), 'value' => 'female'],
                    ['label' => __('Not Specified'), 'value' => 'ignore'],
                ],
                "name"   => 'gender',
            ]
        );
        $form->getElement('gender')
             ->setData('size', 5);

        $values = [];

        $ages = \Licentia\Reports\Model\Products\Relations::POSSIBLE_AGE_RANGES;
        foreach ($ages as $age) {
            $values[] = ['label' => $age, 'value' => $age];
        }

        $fieldset->addField(
            'age',
            "multiselect",
            [
                "label"  => __('Age'),
                "values" => $values,
                "name"   => 'age',
            ]
        );

        if ($this->getRequest()->getParam('tid')) {
            $fieldset->addField(
                'parent_id',
                "hidden",
                [
                    "value" => $this->getRequest()->getParam('tid'),
                    "name"  => 'parent_id',
                ]
            );
        }

        $this->setForm($form);

        if ($current) {
            $form->addValues($current->getData());
        }

        return parent::_prepareForm();
    }
}
