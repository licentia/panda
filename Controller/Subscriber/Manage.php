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

namespace Licentia\Panda\Controller\Subscriber;

/**
 * Class Manage
 *
 * @package Licentia\Panda\Controller\Subscriber
 */
class Manage extends \Licentia\Panda\Controller\Subscriber
{

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        $resultPage = $this->resultPageFactory->create();
        $resultPage->initLayout();
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('id');
        $code = $this->getRequest()->getParam('code');
        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadById($id);

        if ($subscriber->getCode() != $code || !$subscriber->getId()) {
            $this->messageManager->addErrorMessage(__('There was a problem with the subscription.'));

            return $resultRedirect->setPath('/');
        }

        $this->registry->register('panda_subscriber', $subscriber);
        $this->registry->register('subscriber_subscribe_if_needed', true);

        return $resultPage;
    }
}
