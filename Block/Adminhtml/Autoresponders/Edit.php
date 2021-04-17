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

namespace Licentia\Panda\Block\Adminhtml\Autoresponders;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Autoresponders
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected ?\Magento\Framework\Registry $registry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_autoresponders';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Autoresponder'));
        $this->buttonList->update('delete', 'label', __('Delete Autoresponder'));

        if (!$this->registry->registry('panda_autoresponder')
                            ->getId()) {
            $this->buttonList->remove('save');
            $this->buttonList->remove('reset');

            $this->buttonList->add(
                'save_and_continue',
                [
                    'label'          => __('Save and Continue Edit'),
                    'class'          => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ],
                ],
                10
            );

            $this->buttonList->update('save_and_continue', 'class', 'primary');
        } else {
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
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {

        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
        );
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        if ($this->registry->registry('panda_autoresponder')
                           ->getId()) {
            return __(
                "Edit Autoresponder '%1'",
                $this->escapeHtml(
                    $this->registry->registry('panda_autoresponder')
                                   ->getName()
                )
            );
        } else {
            return __('New Autoresponder');
        }
    }
}
