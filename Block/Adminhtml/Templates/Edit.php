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

namespace Licentia\Panda\Block\Adminhtml\Templates;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Templates
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @param \Licentia\Panda\Model\TemplatesFactory $templatesFactory
     * @param \Magento\Backend\Block\Widget\Context  $context
     * @param \Magento\Framework\Registry            $registry
     * @param array                                  $data
     */
    public function __construct(
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->templatesFactory = $templatesFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_templates';

        parent::_construct();

        $template = $this->registry->registry('panda_template');

        $this->buttonList->update('save', 'label', __('Save Template'));
        $this->buttonList->update('delete', 'label', __('Delete Template'));

        if ($template->getId() && !$template->getParentId()) {
            $varUrl = $this->getUrl('*/*/new', ['tid' => $template->getId()]);

            $this->buttonList->add(
                'variation',
                [
                    'label'   => __('Add Variation'),
                    'onclick' => "window.location = '$varUrl'; return false;",
                ]
            );
        }

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
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        $template = $this->registry->registry('current_template');

        if ($template->getId()) {
            $extra = '';
            if ($template->getParentId()) {
                $temp = $this->templatesFactory->create()->load($template->getParentId());
                $extra = ' { ' . __('Variation of ') . $temp->getName() . ' }';
            }

            return $this->escapeHtml($template->getName()) . $extra;
        } else {
            if ($this->registry->registry('panda_template')
                               ->getId()) {
                return __(
                    "Edit Message Template '%1'",
                    $this->escapeHtml(
                        $this->registry->registry('panda_template')
                                       ->getName()
                    )
                );
            } else {
                return __("New Message Template");
            }
        }
    }
}
