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
 * @modified   27/03/20, 03:05 GMT
 *
 */

namespace Licentia\Panda\Controller\Adminhtml\Support;

/**
 * Class Index
 *
 * @package Licentia\Panda\Controller\Adminhtml\Support
 */
class Index extends \Licentia\Panda\Controller\Adminhtml\Support
{

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {

        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::support')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Support'), __('Support'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPostValue();
            $email = \Licentia\Panda\Helper\Debug::SUPPORT_EMAIL;

            $this->_getSession()->setFormData($params);
            $msg = '';
            $msg .= "Reason : " . $params['reason'] . "<br>";
            $msg .= "Message : " . $params['message'] . "<br>";

            try {
                $mail = new \Zend_Mail('UTF-8');
                $mail->addTo($email, 'Panda Support');
                $mail->setBodyHtml($msg);
                $mail->setSubject('Contact - Panda Support');
                $mail->setFrom($params['email'], $params['first_name'] . ' ' . $params['last_name']);

                if ($params['attach'] == 1) {
                    $content = $this->debugHelper->getCreateDumpFile();
                    $attachment = new \Zend_Mime_Part($content);
                    $attachment->type = 'plain/text';
                    $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                    $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                    $attachment->filename = 'report.log';

                    $mail->addAttachment($attachment);
                }

                $transport = $this->pandaHelper->getEmailSenderForInternalNotifications();

                $t = $mail->send($transport);

                if ($t === false) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to send. Please send an email to support@greenflyingpanda.com'));
                }
                $this->messageManager->addSuccessMessage(__('Your request has been sent'));
                $this->_getSession()->setFormData([]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultRedirect->setPath('*/support');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Support'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Support\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Support\Edit\Tabs')
                   );

        return $resultPage;
    }
}
