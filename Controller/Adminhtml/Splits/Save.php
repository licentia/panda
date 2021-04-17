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

namespace Licentia\Panda\Controller\Adminhtml\Splits;

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Splits
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Splits
{

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected \Licentia\Panda\Model\TagsFactory $tagsFactory;

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
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone;

    /**
     * @param Action\Context                                       $context
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\DateTime   $dateFilter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfigInterface
     * @param \Magento\Framework\Registry                          $registry
     * @param \Licentia\Panda\Helper\Data                          $pandaHelper
     * @param \Licentia\Panda\Model\SplitsFactory                  $splitsFactory
     * @param \Licentia\Panda\Model\CampaignsFactory               $campaignsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\SplitsFactory $splitsFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $splitsFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->tagsFactory = $tagsFactory;
        $this->timezone = $timezone;
        $this->campaignsFactory = $campaignsFactory;
        $this->dateFilter = $dateFilter;
        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
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

            try {
                try {
                    $this->timezone->formatDate($data['deploy_at']);
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in Deploy Field'));
                }

                $inputFilter = new \Zend_Filter_Input(['deploy_at' => $this->dateFilter], [], $data);
                $data = $inputFilter->getUnescaped();

                $model = $this->registry->registry('panda_split');

                if (!$model->getId() && $id) {
                    $this->messageManager->addErrorMessage(__('This A/B Campaign no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }

                if (!isset($data['segments_ids'])) {
                    $data['segments_ids'] = [];
                }
                if (array_search(0, $data['segments_ids']) !== false) {
                    $data['segments_ids'] = [];
                }
                $data['segments_ids'] = implode(',', $data['segments_ids']);

                if (!isset($data['store_id'])) {
                    $data['store_id'] = [0];
                }
                if (array_search(0, $data['store_id']) !== false) {
                    $data['store_id'] = [];
                }
                $data['store_id'] = implode(',', $data['store_id']);

                $data['controller'] = true;

                $cDate = new \DateTime();
                $cDate->sub(new \DateInterval('P1D'));

                $sendDate = new \DateTime($data['deploy_at']);
                $sendDate->add(new \DateInterval('P' . $data['days'] . 'D'));
                $real = $sendDate->format('Y-m-d H:i:s');

                $data['send_at'] = $real;

                if (!isset($data['tags'])) {
                    $data['tags'] = [];
                }

                $model->setData($data);
                if ($id) {
                    $model->setId($id);
                }

                if ($cDate->format('Y-m-d H:i:s') > $data['deploy_at'] && $model->getSend() == 0) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Your Send Date cannot be earlier than now.')
                    );
                }

                $model->addData($data);
                $model->save();

                $this->tagsFactory->create()->updateTags('splits', $model, $data['tags']);

                $this->_getSession()->setFormData(false);

                $extraMsg = '';
                if ($this->scopeConfig->getValue(
                    'panda_nuntius/info/warning',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                )) {
                    $totalRecipients = $model->calculateNumberRecipients();

                    if ($totalRecipients !== false) {
                        $extraMsg = ' ' . __('Predicted Campaign Recipients: %1', $totalRecipients);

                        if ($totalRecipients == 0) {
                            $this->messageManager->addErrorMessage(
                                __('Please be advise: There are no recipients expected to receive this campaign.')
                            );
                            $extraMsg = '';
                        }
                    }
                }

                $this->messageManager->addSuccessMessage(__('You saved the A/B Campaign.' . $extraMsg));

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
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the A/B Campaign. Check the error log for more information.')
                );
            }
            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $id,
                    'active_tab' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
