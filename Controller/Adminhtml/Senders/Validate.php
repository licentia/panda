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

namespace Licentia\Panda\Controller\Adminhtml\Senders;

/**
 * Class Validate
 *
 * @package Licentia\Panda\Controller\Adminhtml\Senders
 */
class Validate extends \Licentia\Panda\Controller\Adminhtml\Senders
{

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {

        parent::execute();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->registry->registry('panda_sender');

        if ($model->getId()) {
            try {
                if ($model->getType() == 'sms') {
                    $this->serviceFactory->create()->getSmsService()->validateSmsEnvironment($model);
                } else {
                    $this->serviceFactory->create()->getEmailService()->validateEmailEnvironment($model);
                }
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while testing the Sender.')
                );
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a sender to test.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
