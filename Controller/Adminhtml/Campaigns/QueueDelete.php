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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

use Magento\Backend\App\Action;

/**
 * Class Delete
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class QueueDelete extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @var \Licentia\Panda\Model\QueueFactory
     */
    protected $queueFactory;

    /**
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Model\SendersFactory              $sendersFactory
     * @param \Licentia\Panda\Model\CampaignsFactory            $campaignsFactory
     * @param \Licentia\Panda\Model\QueueFactory                $queueFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\QueueFactory $queueFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $sendersFactory,
            $registry,
            $campaignsFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->queueFactory = $queueFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        $messagesIds = $this->getRequest()->getParam('ids');
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!is_array($messagesIds)) {
            $this->messageManager->addErrorMessage(__('Please select one or more messages.'));
        } else {
            try {
                foreach ($messagesIds as $messageId) {
                    $this->queueFactory->create()
                                       ->load($messageId)
                                       ->delete();
                }
                $this->messageManager->addSuccessMessage(
                    __(
                        'Total of %1 record(s) were deleted.',
                        count($messagesIds)
                    )
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting the queue.')
                );
            }
        }

        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }
}
