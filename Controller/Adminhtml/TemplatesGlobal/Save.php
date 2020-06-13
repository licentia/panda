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

namespace Licentia\Panda\Controller\Adminhtml\TemplatesGlobal;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\TemplatesGlobal
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\TemplatesGlobal
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
        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('id');

            /** @var \Licentia\Panda\Model\TemplatesGlobal $model */
            $model = $this->registry->registry('panda_template_global');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Design Template no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $parent = false;
            if (isset($data['parent_id']) && (int) $data['parent_id'] > 0) {
                $model->unsetData('id');
                $model->unsetData('template_id');
                unset($data['id']);
                unset($data['template_id']);

                $parent = true;
            } else {
                unset($data['parent_id']);
                unset($data['store_id']);
            }

            $model->addData($data);

            try {
                if (strpos($data['content'], '{MESSAGE}') === false) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Please insert the {MESSAGE} tag in the template')
                    );
                }

                $model->save();

                $this->messageManager->addSuccessMessage(__('You saved the Design Template.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('test')) {
                    $sender = $this->sendersFactory->create()->load($data['sender_id']);
                    $recipients = explode(',', $data['recipients']);
                    $mail = $this->newsletterTemplateFactory->create();

                    $storeId = $model->getStoreId();

                    if (!$storeId) {
                        $storeId = $this->storeManager->getDefaultStoreView()->getId();
                    }

                    $mail->emulateDesign($storeId);
                    $mail->setTemplateText($model->getContent());
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

                if ($parent || $model->getParentId()) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $model->getParentId(), 'tab_id' => 'variations_section']
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
                    __('Something went wrong while saving the Design Template. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
