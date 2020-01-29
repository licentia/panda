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
 * @modified   29/01/20, 15:22 GMT
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
