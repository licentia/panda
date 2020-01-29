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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

/**
 * Class Pause
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Pause extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->registry->registry('panda_campaign');

        $status = $this->getRequest()->getParam('op') == 'resume' ? 'standby' : 'paused';

        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $campaign->setData('status', $status)
                         ->save();

                $this->messageManager->addSuccessMessage(__('The campaign has been %1.', $status));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while %1 the campaign.', $status)
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Unable to find a campaign to %1.', $status));
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}
