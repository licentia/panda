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
 * @modified   29/01/20, 15:22 GMT
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
