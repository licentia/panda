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

namespace Licentia\Panda\Block\Adminhtml\TemplatesGlobal;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\TemplatesGlobal
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Licentia\Panda\Model\TemplatesGlobalFactory
     */
    protected $templatesGlobalFactory;

    /**
     * @param \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory
     * @param \Magento\Backend\Block\Widget\Context        $context
     * @param \Magento\Framework\Registry                  $registry
     * @param \Magento\Store\Model\System\Store            $systemStore
     * @param array                                        $data
     */
    public function __construct(
        \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {

        $this->templatesGlobalFactory = $templatesGlobalFactory;
        $this->registry = $registry;
        $this->systemStore = $systemStore;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_templatesGlobal';

        parent::_construct();

        $template = $this->registry->registry('panda_template_global');

        $this->buttonList->update('save', 'label', __('Save Template'));
        $this->buttonList->update('delete', 'label', __('Delete Template'));

        $this->buttonList->add(
            'test',
            [
                'label'          => __('Save and Send Test Emails'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'     => 'saveAndContinueEdit',
                            'target'    => '#edit_form',
                            'eventData' => ['action' => ['args' => ['test' => '1']]],
                        ],
                    ],
                ],
            ],
            -100
        );

        if ($template->getId() && !$template->getParentId() && !$this->getRequest()->getParam('parent_id')) {
            $options = $this->_getSaveSplitButtonOptions();

            if (count($options) > 0) {
                $this->getToolbar()
                     ->addChild(
                         'save-split-button-var',
                         'Magento\Backend\Block\Widget\Button\SplitButton',
                         [
                             'id'           => 'save-split-button-var',
                             'label'        => __('New Store View Variation'),
                             'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                             'button_class' => 'widget-button-save',
                             'options'      => $options,
                         ]
                     );
            }
        }

        $this->buttonList->remove('save');
        $this->getToolbar()
             ->addChild(
                 'save-split-button',
                 'Magento\Backend\Block\Widget\Button\SplitButton',
                 [
                     'id'           => 'save-split-button',
                     'label'        => __('Save'),
                     'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                     'button_class' => 'widget-button-update',
                     'options'      => [
                         [
                             'id'             => 'save-button',
                             'label'          => __('Save'),
                             'default'        => true,
                             'data_attribute' => [
                                 'mage-init' => [
                                     'button' => [
                                         'event'  => 'saveAndContinueEdit',
                                         'target' => '#edit_form',
                                     ],
                                 ],
                             ],
                         ],
                         [
                             'id'             => 'save-continue-button',
                             'label'          => __('Save & Close'),
                             'data_attribute' => [
                                 'mage-init' => [
                                     'button' => [
                                         'event'  => 'save',
                                         'target' => '#edit_form',
                                     ],
                                 ],
                             ],
                         ],
                     ],
                 ]
             );
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getSaveSplitButtonOptions()
    {

        $template = $this->registry->registry('panda_template_global');

        $options = [];
        $templates = $this->templatesGlobalFactory->create()
                                                  ->getCollection()->addFieldToFilter('parent_id', $template->getId());

        $stores = $this->systemStore->getStoreOptionHash();

        foreach ($templates as $info) {
            unset($stores[$info->getStoreId()]);
        }

        foreach ($stores as $key => $store) {
            $options[] = [
                'id'      => 'edit-button',
                'label'   => __($store),
                'onclick' => "window.location='" . $this->getUrl(
                        '*/*/edit',
                        [
                            'store_id'  => $key,
                            'parent_id' => $this->getRequest()->getParam('id'),
                        ]
                    ) . "'",
                'default' => false,
            ];
        }

        return $options;
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        if ($this->registry->registry('panda_template_global')
                           ->getId()) {
            return __(
                "Edit Design Template '%1'",
                $this->escapeHtml(
                    $this->registry->registry('panda_template_global')
                                   ->getName()
                )
            );
        } else {
            return __("New Design Template");
        }
    }
}
