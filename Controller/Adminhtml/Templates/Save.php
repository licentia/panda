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

namespace Licentia\Panda\Controller\Adminhtml\Templates;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Templates
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Templates
{

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

            /** @var \Licentia\Panda\Model\Templates $model */
            $model = $this->registry->registry('panda_template');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Message Template no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (!isset($data['segments_ids'])) {
                $data['segments_ids'] = [0];
            }
            if (array_search(0, $data['segments_ids']) !== false) {
                $data['segments_ids'] = [];
            }
            $data['segments_ids'] = implode(',', $data['segments_ids']);

            if (!isset($data['age'])) {
                $data['age'] = [0];
            }
            if (array_search(0, $data['age']) !== false) {
                $data['age'] = [];
            }
            $data['age'] = implode(',', $data['age']);

            if (!isset($data['store_id'])) {
                $data['store_id'] = [0];
            }
            if (array_search(0, $data['store_id']) !== false) {
                $data['store_id'] = [];
            }
            $data['store_id'] = implode(',', $data['store_id']);

            $model->setData($data);
            $model->setId($id);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Message Template.'));
                $this->_getSession()->setFormData(false);

                // check if 'Save and Send Test'
                if ($this->getRequest()->getParam('test')) {
                    $sender = $this->sendersFactory->create()->load($data['sender_id']);
                    $recipients = explode(',', $data['recipients']);
                    $mail = $this->newsletterTemplateFactory->create();

                    $storeId = $this->storeManager->getDefaultStoreView()->getId();

                    $message = $this->templatesGlobalFactory->create()
                                                            ->getTemplateForMessage(
                                                                $model->getGlobalTemplateId(),
                                                                $storeId,
                                                                $model->getMessage()
                                                            );

                    $mail->emulateDesign($storeId);
                    $mail->setTemplateText($message);
                    $message = $mail->getProcessedTemplate();

                    $this->service->getEmailService()->validateEmailEnvironment($sender, $message, true, $recipients);
                    $this->messageManager->addSuccessMessage(__('Test Message Sent'));
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'tab_id' => $this->getRequest()->getParam('active_tab'),
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
                    __('Something went wrong while saving the Message Template. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(),]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
