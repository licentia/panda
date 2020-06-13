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
 * Class ValidateEmailEnvironment
 *
 * @package Licentia\Panda\Controller\Adminhtml\Support
 */
class ValidateEmailEnvironment extends \Licentia\Panda\Controller\Adminhtml\Support
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        $params = $this->getRequest()->getParams();
        $email = $params['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->messageManager->addErrorMessage(__('Please insert a valid Email Address'));

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        $this->serviceFactory->create()
                             ->getEmailService()->ValidateEmailEnvironment($email);

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}
