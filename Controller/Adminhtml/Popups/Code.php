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

namespace Licentia\Panda\Controller\Adminhtml\Popups;

/**
 * Class Delete
 *
 * @package Licentia\Panda\Controller\Adminhtml\Popups
 */
class Code extends \Licentia\Panda\Controller\Adminhtml\Popups
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \Licentia\Panda\Model\Popups $model */
        $model = $this->registry->registry('panda_popup');

        if ($model->getId()) {
            try {
                $model->setCode($model->getCode() + 1)->save();
                $this->messageManager->addSuccessMessage(__('Cookie Code reseted.'));

                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while resetting the code from the %1', $model->getTypeName())
                );
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } else {
            $this->messageManager->addErrorMessage(
                __('We can\'t find an %1 to reset the code.', $model->getTypeName())
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
