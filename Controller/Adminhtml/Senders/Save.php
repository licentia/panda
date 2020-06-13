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

namespace Licentia\Panda\Controller\Adminhtml\Senders;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Senders
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Senders
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

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('id');

            /** @var \Licentia\Panda\Model\Senders $model */
            $model = $this->registry->registry('panda_sender');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This sender no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['password']) && $data['password'] == $model::OBSCURE_PASSWORD_REPLACEMENT) {
                unset($data['password']);
            }
            if (isset($data['api_key']) && $data['api_key'] == $model::OBSCURE_PASSWORD_REPLACEMENT) {
                unset($data['api_key']);
            }
            if (isset($data['bounces_password']) && $data['bounces_password'] == $model::OBSCURE_PASSWORD_REPLACEMENT) {
                unset($data['bounces_password']);
            }

            $model->setData($data);
            $model->setId($id);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the sender.'));

                $this->_getSession()->setFormData(false);

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
                    __('Something went wrong while saving the Sender. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'    => $model->getId(),
                    'ctype' => isset($data['type']) ? $data['type'] : false,
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
