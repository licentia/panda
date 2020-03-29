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
 * @modified   29/03/20, 03:21 GMT
 *
 */

namespace Licentia\Panda\Block\Adminhtml\Subscriber\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber\Edit
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
        $this->setId('subscriber_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Subscriber Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->registry->registry('panda_subscriber');

        $this->addTab(
            'form_section',
            [
                'label'   => __('Subscriber Information'),
                'title'   => __('Subscriber Information'),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab\Form')
                                  ->toHtml(),
            ]
        );

        if ($subscriber->getId()) {
            $this->addTab(
                "conversions_section",
                [
                    "label" => __("Conversions"),
                    "title" => __("Conversions"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/gridconv', ['_current' => true]),
                ]
            );

            $this->addTab(
                "archive_section",
                [
                    "label" => __("Emails Sent"),
                    "title" => __("Emails Sent"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/archivegrid', ['_current' => true]),
                ]
            );

            $this->addTab(
                "archive_sms_section",
                [
                    "label" => __("SMS Sent"),
                    "title" => __("SMS Sent"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/archivesmsgrid', ['_current' => true]),
                ]
            );

            $this->addTab(
                "forms_section",
                [
                    "label"   => __("Forms"),
                    "title"   => __("Forms"),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab\Forms')
                                      ->toHtml(),
                ]
            );

            $this->addTab(
                "bounces_section",
                [
                    "label" => __("Bounces"),
                    "title" => __("Bounces"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/bouncesgrid', ['_current' => true]),
                ]
            );
        }

        if ($this->getRequest()->getParam('tab_id')) {
            $this->setActiveTab($this->getRequest()->getParam('tab_id'));
        }

        return parent::_beforeToHtml();
    }
}
