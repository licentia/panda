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

namespace Licentia\Panda\Controller\Adminhtml\Splits;

/**
 * Class Send
 *
 * @package Licentia\Panda\Controller\Adminhtml\Splits
 */
class Send extends \Licentia\Panda\Controller\Adminhtml\Splits
{

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $split = $this->registry->registry('panda_split');

        try {
            $version = $this->getRequest()->getParam('version');
            if (!in_array($version, ['a', 'b'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Version'));
            }

            if (!$split->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Unable to find A/B Campaign'));
            }
            if ($split->getClosed() == 1 ||
                $split->getActive() == 0 ||
                $split->getWinner() != 'manually' ||
                $split->getSent() == 0
            ) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Unable to perform action. Please verify all requisites')
                );
            }

            $split->sendManually($split, $version, true);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while sending the A/B Campaign.')
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
