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

namespace Licentia\Panda\Block\Adminhtml\ExtraFields;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\ExtraFields
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

        $this->_objectId = 'id';
        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_extraFields';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Extra Field'));
        $this->buttonList->update('delete', 'label', __('Delete Extra Field'));

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

        if ($this->registry->registry('panda_extra')
                           ->getId()) {
            return __(
                "Edit Extra Field '%1'",
                $this->escapeHtml(
                    $this->registry->registry('panda_extra')
                                   ->getTitle()
                )
            );
        } else {
            return __('New Extra Field');
        }
    }
}
