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

namespace Licentia\Panda\Controller\Adminhtml\Support;

use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;

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
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {

        parent::execute();
        $resultRedirect = $this->resultRedirectFactory->create();

        $sender = $this->pandaHelper->getEmailSenderForInternalNotifications();

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPostValue();
            $email = \Licentia\Panda\Helper\Debug::SUPPORT_EMAIL;

            $this->_getSession()->setFormData($params);
            $msg = '';
            $msg .= "Reason : " . $params['reason'] . "<br>";
            $msg .= "Message : " . $params['message'] . "<br>";

            try {

                $html = new MimePart($msg);
                $html->type = Mime::TYPE_HTML;
                $html->charset = 'utf-8';
                $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

                $message = new Message();

                if ($params['attach'] == 1) {
                    $image = new MimePart($this->debugHelper->getCreateDumpFile());
                    $image->type = 'plain/text';
                    $image->filename = 'report.log';
                    $image->disposition = Mime::DISPOSITION_ATTACHMENT;
                    $image->encoding = Mime::ENCODING_BASE64;

                    $body = new MimeMessage();
                    $body->setParts([$html, $image]);
                    $message->setBody($body);
                    $contentTypeHeader = $message->getHeaders()->get('Content-Type');
                    $contentTypeHeader->setType('multipart/related');

                } else {
                    $message->setBody(\Licentia\Panda\Model\Service\Smtp::getMessageBody($msg));
                }

                $message->addTo($email, 'Panda Support');
                $message->setSubject('Contact - Panda Support');
                $message->setFrom($sender->getEmail(), $sender->getName());
                $message->addCc($params['email'], $params['name']);
                $message->setReplyTo($params['email'], $params['name']);

                $transport = $this->pandaHelper->getSmtpTransport($sender);

                $transport->send($message);

                $this->messageManager->addSuccessMessage(__('Your request has been sent'));
                $this->_getSession()->setFormData([]);
            } catch (\Exception $e) {
                $this->pandaHelper->logException($e);
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultRedirect->setPath('*/support');
        }

        if (!$sender) {
            $this->messageManager->addErrorMessage("You cannot send a support request without an email sender. " .
                                                    "If you can't add a email Sender, send an email to support@greenflyingpanda.com");
            return $resultRedirect->setPath('panda/senders');
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
