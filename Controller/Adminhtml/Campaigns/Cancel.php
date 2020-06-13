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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

/**
 * Class Cancel
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Cancel extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = $this->campaignsFactory->create();
                $model->load($id)
                      ->setData('status', 'canceled')
                      ->save();
                $this->messageManager->addSuccessMessage(__('The campaign has been canceled.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while cancelling the campaign.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Unable to find a campaign to cancel.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
