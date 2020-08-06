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

namespace Licentia\Panda\Controller\Adminhtml\Subscriber;

/**
 * Class ImportCore
 *
 * @package Licentia\Panda\Controller\Adminhtml\Subscriber
 */
class ImportCore extends \Licentia\Panda\Controller\Adminhtml\Subscriber
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        $model = $this->registry->registry('panda_subscriber');

        try {
            $result = $model->importCoreNewsletterSubscribers();
            $this->messageManager->addSuccessMessage(__('%1 Subscribers Imported.', $result));
        } catch (\Exception $ex) {
            $this->messageManager->addErrorMessage($ex->getMessage());
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}
