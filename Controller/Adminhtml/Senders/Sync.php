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

namespace Licentia\Panda\Controller\Adminhtml\Senders;

/**
 * Class Sync
 *
 * @package Licentia\Panda\Controller\Adminhtml\Senders
 */
class Sync extends \Licentia\Panda\Controller\Adminhtml\Senders
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->sendersFactory->create()->sync();
            $this->messageManager->addSuccessMessage(__('Senders synced'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while syncing senders: %1', $e->getMessage())
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
