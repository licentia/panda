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

namespace Licentia\Panda\Controller\Adminhtml\Errors;

use Magento\Backend\App\Action;

/**
 * Class MassSend
 *
 * @package Licentia\Panda\Controller\Adminhtml\Errors
 */
class MassSend extends \Licentia\Panda\Controller\Adminhtml\Errors
{

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * MassSend constructor.
     *
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Licentia\Panda\Model\CampaignsFactory            $campaignsFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $fileFactory,
            $registry,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->campaignsFactory = $campaignsFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        $messagesIds = $this->getRequest()->getParam('errors');
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!is_array($messagesIds)) {
            $this->messageManager->addErrorMessage(__('Please select one or more messages.'));
        } else {
            try {
                $messages = $this->campaignsFactory->create()->tryAgain($messagesIds);

                foreach ($messages as $message) {
                    $this->messageManager->addSuccessMessage(__($message));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting the items.')
                );
            }
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
