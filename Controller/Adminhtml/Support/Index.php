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

                $sender = $this->pandaHelper->getEmailSenderForInternalNotifications();

                $mail = new \Zend_Mail('UTF-8');
                $mail->addTo($email, 'Panda Support');
                $mail->setBodyHtml($msg);
                $mail->setSubject('Contact - Panda Support');
                $mail->setFrom($sender->getEmail(), $sender->getName());
                $mail->addCc($params['email'], $params['name']);
                $mail->setReplyTo($params['email'], $params['name']);

                if ($params['attach'] == 1) {
                    $content = $this->debugHelper->getCreateDumpFile();
                    $attachment = new \Zend_Mime_Part($content);
                    $attachment->type = 'plain/text';
                    $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                    $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                    $attachment->filename = 'report.log';

                    $mail->addAttachment($attachment);
                }

                $transport = $this->pandaHelper->getSmtpTransport($sender);

                $t = $mail->send($transport);

                if ($t === false) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to send. Please send an email to support@greenflyingpanda.com'));
                }
                $this->messageManager->addSuccessMessage(__('Your request has been sent'));
                $this->_getSession()->setFormData([]);
            } catch (\Exception $e) {
                $this->pandaHelper->logException($e);
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
