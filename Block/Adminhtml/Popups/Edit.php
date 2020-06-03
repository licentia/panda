<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   03/06/20, 16:18 GMT
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
    protected $registry = null;

    /**
     * @var Url
     */
    protected $urlHelper;

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
