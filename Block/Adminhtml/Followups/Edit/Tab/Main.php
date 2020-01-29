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

namespace Licentia\Panda\Block\Adminhtml\Followups\Edit\Tab;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\Followups\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var
     */
    protected $followupsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Licentia\Panda\Model\FollowupFactory   $followupsFactory
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Licentia\Panda\Model\FollowupFactory $followupsFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->followupsFactory = $followupsFactory;
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry('panda_followup');
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->_coreRegistry->registry('panda_campaign');

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

        if ($this->getRequest()->getParam('cid')) {
            $fieldset->addField(
                'cid',
                'hidden',
                [
                    "name" => "cid",
                ]
            );
        }

        $fieldset->addField(
            'name',
            "text",
            [
                "label"    => __("Internal Name"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "name",
            ]
        );

        $fieldset->addField(
            "is_active",
            "select",
            [
                "label"    => __("Active"),
                "class"    => "required-entry",
                "required" => true,
                "values"   => ['0' => __('No'), '1' => __('Yes')],
                "name"     => "is_active",
            ]
        );

        $fieldset->addField(
            'subject',
            "text",
            [
                "label"    => __("Subject"),
                "class"    => "required-entry",
                "required" => true,
                "note"     => __('the {{subject}} tag will be replaced by the original campaign subject'),
                "name"     => "subject",
            ]
        );

        $fieldset->addField(
            "recipients_options",
            "multiselect",
            [
                "label"  => __("Send to subscribers that..."),
                "class"  => "required-entry",
                "values" => $this->followupsFactory->create()
                                                   ->getOptionValues(),
                "name"   => "recipients_options[]",
            ]
        );

        $fieldset->addField(
            'days',
            'select',
            [
                'name'     => 'days',
                'label'    => __('Send after X days'),
                'title'    => __('Send after X days'),
                'required' => true,
                'note'     => __(
                    'After the campaign deployment date (' .
                    $campaign->getData('deploy_at') .
                    ')'
                ),
                'options'  => array_combine(range(1, 10), range(1, 10)),
            ]
        );

        $form->addValues($current->getData());

        if ($this->getRequest()->getParam('cid')) {
            $form->addValues(
                [
                    "cid" => $this->getRequest()->getParam('cid'),
                ]
            );
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
