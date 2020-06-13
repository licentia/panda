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

namespace Licentia\Panda\Block\Adminhtml\Campaigns;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns
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

        $this->_objectId = 'id';
        $this->_blockGroup = 'Licentia_Panda';
        $this->_controller = 'adminhtml_campaigns';

        parent::_construct();
        $this->buttonList->remove('save');
        $this->buttonList->update('delete', 'label', __('Delete'));

        $buttons = [];
        $buttons['saveandcontinue'] = [
            'id'             => 'saveandcontinue',
            'label'          => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event'  => 'save',
                        'target' => '#edit_form',
                    ],
                ],
            ],
        ];

        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->registry->registry('panda_campaign');

        if ($campaign->getRecurring() == '0' && $campaign->getType() == 'email') {
            $emailUrl = $this->getUrl('*/followups/new', ['cid' => $campaign->getId()]);
            $this->buttonList->add(
                'followup_email',
                [
                    'label'   => __('Follow Up'),
                    'class'   => "add",
                    'title'   => "Follow Up",
                    'onclick' => "window.location='$emailUrl';",
                ]
            );
        }

        if ($campaign->getId()) {
            if ($campaign->getRecurring() != '0' && $campaign->getStatus() != 'finished') {
                $cancelUrl = $this->getUrl('*/*/cancel', ['id' => $campaign->getId()]);
                $text = __('Cancel this campaign? This action cannot be undone');

                $this->buttonList->add(
                    'cancel_campaign',
                    [
                        'label'   => __('Cancel'),
                        'onclick' => "if(!confirm('$text')){return false;}; window.location='$cancelUrl';",
                    ]
                );
            }
            if ($campaign->getRecurring() == '0' &&
                $campaign->getStatus() != 'finished' &&
                $campaign->getStatus() != 'paused'
            ) {
                $pauseUrl = $this->getUrl('*/*/pause', ['id' => $campaign->getId(), 'op' => 'pause']);
                $text = __('Pause campaign?');

                $this->buttonList->add(
                    'pause_campaign',
                    [
                        'label'   => __('Pause'),
                        'onclick' => "if(!confirm('$text')){return false;}; window.location='$pauseUrl';",
                    ]
                );
            }

            if ($campaign->getStatus() == 'paused') {
                $pauseUrl = $this->getUrl('*/*/pause', ['id' => $campaign->getId(), 'op' => 'resume']);
                $text = __('Resume campaign?');

                $this->buttonList->add(
                    'resume_campaign',
                    [
                        'label'   => __('Resume'),
                        'onclick' => "if(!confirm('$text')){return false;}; window.location='$pauseUrl';",
                    ]
                );
            }

            if ($campaign->getType() == 'email') {
                $previewUrl = $this->getUrl('*/*/preview', ['id' => $campaign->getId()]);
                $this->buttonList->add(
                    'preview',
                    [
                        'label'   => __('Preview'),
                        'onclick' => "window.open('$previewUrl'); return false;",
                    ]
                );
            }
        }
        if ($campaign->getRecurring() == '0' && $campaign->getStatus() != 'finished') {
            $text = __('Start deploying the campaign now?');
            $this->buttonList->add(
                'send',
                [
                    'label'          => __('Save & Send'),
                    'class'          => 'save',
                    'onclick'        => "if(!confirm('" . $text . "')){return false;}",
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event'     => 'save',
                                'target'    => '#edit_form',
                                'eventData' => ['action' => ['args' => ['op' => 'send']]],
                            ],
                        ],
                    ],
                ]
            );
        }

        if ($campaign->getStatus() == 'finished') {
            $this->buttonList->remove('reset');
        }

        if ($campaign->getId() && $campaign->getStatus() != 'standby' && $campaign->getStatus() != 'draft') {
            $locationReturn = $this->getUrl('*/reports/detail', ['id' => $campaign->getId()]);

            $this->buttonList->add(
                'reports',
                [
                    'label'   => __('Reports'),
                    'onclick' => "setLocation('{$locationReturn}')",
                ],
                10
            );
        }

        if ($campaign->getStatus() == 'finished') {
            unset($buttons['save'], $buttons['send'], $buttons['saveandcontinue']);

            $buttons['duplicate'] = [
                'id'             => 'duplicate',
                'label'          => __('Duplicate & Save'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'     => 'save',
                            'target'    => '#edit_form',
                            'eventData' => ['action' => ['args' => ['op' => 'duplicate']]],
                        ],
                    ],
                ],
            ];
        } else {
            $buttons['save'] = [
                'id'             => 'save',
                'label'          => __('Save'),
                'class'          => 'save',
                'default'        => true,
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'  => 'saveAndContinueEdit',
                            'target' => '#edit_form',
                        ],
                    ],
                ],
            ];
        }

        $this->getToolbar()
             ->addChild(
                 'save-split-button',
                 'Magento\Backend\Block\Widget\Button\SplitButton',
                 [
                     'id'           => 'save',
                     'label'        => __('Save'),
                     'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                     'button_class' => 'widget-button-update',
                     'options'      => $buttons,

                 ]
             );
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

        if ($this->registry->registry('panda_campaign')
                           ->getId()) {
            return __(
                "Edit Campaign '%1'",
                $this->escapeHtml(
                    $this->registry->registry('panda_campaign')
                                   ->getTitle()
                )
            );
        } else {
            return __('New Campaign');
        }
    }
}
