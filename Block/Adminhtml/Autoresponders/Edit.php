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
 * @modified   29/01/20, 15:22 GMT
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
    protected $registry = null;

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
