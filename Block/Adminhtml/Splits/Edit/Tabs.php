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

namespace Licentia\Panda\Block\Adminhtml\Splits\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Splits\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param \Magento\Framework\Registry              $coreRegistry
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        $this->registry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('split_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('A/B Campaign Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        $current = $this->registry->registry('panda_split');

        $option = $this->getRequest()->getParam('option');

        $this->addTab(
            "form_section",
            [
                "label"   => __("General"),
                "title"   => __("General"),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab\Form')
                                  ->toHtml(),
            ]
        );

        if ($option || $current->getId()) {
            $this->addTab(
                "emaila_section",
                [
                    "label"   => __("Email"),
                    "title"   => __("Email"),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab\Emaila')
                                      ->toHtml(),
                ]
            );

            $this->addTab(
                "emailb_section",
                [
                    "label"   => __("Testing Options"),
                    "title"   => __("Testing Options"),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit\Tab\Emailb')
                                      ->toHtml(),
                ]
            );
        }

        if ($this->getRequest()->getParam('tab_id')) {
            $this->setActiveTab(str_replace($this->getId() . '_', '', $this->getRequest()->getParam('tab_id')));
        }

        return parent::_beforeToHtml();
    }
}
