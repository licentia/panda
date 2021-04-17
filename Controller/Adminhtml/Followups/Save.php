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

namespace Licentia\Panda\Controller\Adminhtml\Followups;

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Followups
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Followups
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var
     */
    protected \Licentia\Panda\Model\CampaignsFactory $campaignsFactory;

    /**
     * @param Action\Context                                     $context
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Framework\Registry                        $registry
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Licentia\Panda\Model\FollowupFactory              $followupFactory
     * @param \Licentia\Panda\Model\CampaignsFactory             $campaignsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory       $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\FollowupFactory $followupFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $followupFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->campaignsFactory = $campaignsFactory;
        $this->dateFilter = $dateFilter;
        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $cid = $this->getRequest()->getParam('cid');

            /** @var \Licentia\Panda\Model\Followup $model */
            $model = $this->registry->registry('panda_followup');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Follow Up no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (!$model->getId()) {
                /** @var \Licentia\Panda\Model\Campaigns $campaign */
                $campaign = $this->campaignsFactory->create()->load($cid);
                $data['campaign_id'] = $cid;
            } else {
                $tmpFollow = $this->followupFactory->create()->load($model->getId());
                /** @var \Licentia\Panda\Model\Campaigns $campaign */
                $campaign = $this->campaignsFactory->create()->load($tmpFollow->getCampaignId());
            }

            if (!$campaign->getId()) {
                $this->messageManager->addErrorMessage(__('Follow Up not found'));

                return $resultRedirect->setPath(
                    '*/*/',
                    [
                        'id'     => $this->getRequest()->getParam('id'),
                        'cid'    => $this->getRequest()->getParam('cid'),
                        'active_tab' => $this->getRequest()->getParam('active_tab'),
                    ]
                );
            }

            if ($campaign->getRecurring() != '0' && $data['active'] != '0') {
                $data['is_active'] = '0';
                $this->messageManager->addNotice(
                    __("You can't create Follow Ups for recurring campaigns. Follow Up is inactive.")
                );
            }

            $sendDate = new \DateTime($campaign->getData('deploy_at'));
            $sendDate->add(new \DateInterval('P' . (int) $data['days'] . 'D'));
            $real = $sendDate->format('Y-m-d H:i:s');

            $data['send_at'] = $real;
            $data['recipients_options'] = implode(',', $data['recipients_options']);

            $model->addData($data);
            $model->save();

            try {
                $model->addData($data);
                $model->save();

                $this->_getSession()->setFormData(false);

                $extraMsg = '';
                if ($this->scopeConfig->getValue(
                    'panda_nuntius/info/warning',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                )) {
                    $totalRecipients = $model->calculateNumberRecipients();

                    if ($totalRecipients !== false) {
                        $extraMsg = ' ' . __(
                                'Predicted Campaign Recipients: %1.2',
                                $totalRecipients
                            );

                        if ($totalRecipients == 0) {
                            $this->messageManager->addErrorMessage(
                                __('Please be advise: There are no recipients expected to receive this campaign.')
                            );
                            $extraMsg = '';
                        }
                    }
                }

                $this->messageManager->addSuccessMessage(__('You saved the Follow Up.' . $extraMsg));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'active_tab' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e,
                    __('Something went wrong while saving the campaign. Check the error log for more information.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $model->getId(),
                    'active_tab' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
