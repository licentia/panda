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

namespace Licentia\Panda\Block\Adminhtml\Popups;

use Magento\Framework\Url;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Popups
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
     * @var Url
     */
    protected Url $urlHelper;

    /**
     * @param Url                                   $urlHelper
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        Url $urlHelper,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_objectId = 'id';
        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_popups';

        $model = $this->registry->registry('panda_popup');

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save ' . $model->getTypeName()));
        $this->buttonList->update('delete', 'label', __('Delete ' . $model->getTypeName()));

        $model = $this->registry->registry('panda_popup');

        $this->buttonList->remove('save');
        $this->getToolbar()
             ->addChild(
                 'save-split-button',
                 'Magento\Backend\Block\Widget\Button\SplitButton',
                 [
                     'id'           => 'save-split-button',
                     'label'        => __('Save' . ' ' . $model->getTypeName()),
                     'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                     'button_class' => 'widget-button-update',
                     'options'      => [
                         [
                             'id'             => 'save-button',
                             'label'          => __('Save' . ' ' . $model->getTypeName()),
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

        if ($model->getId() && $model->getType() != 'block') {
            $url = $this->_storeManager->getDefaultStoreView()
                                       ->getBaseUrl() . '?panda_emulated_popup=' . $model->getId();

            $this->buttonList->add(
                'preview',
                [
                    'label'   => __('Preview'),
                    'class'   => 'save',
                    'onclick' => "window.open('$url'); return false;",
                ],
                20
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

        $model = $this->registry->registry('panda_popup');
        if ($model->getId()) {
            return __(
                "Edit %1 '%1'",
                $model->getTypeName(),
                $this->escapeHtml($model->getName())
            );
        } else {
            return __('New %1', $model->getTypeName());
        }
    }
}
