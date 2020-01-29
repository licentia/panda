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
 * Class Duplicate
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Duplicate extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->registry->registry('panda_campaign');

        if ($model->getId()) {
            try {
                $sendDate = new \DateTime();
                $sendDate->add(new \DateInterval('P6D'));

                $model->setId(null);

                $data = $model->getData();

                $data['deploy_at'] = $sendDate->format('Y-m-d H:i:s');
                $data['status'] = 'standby';
                $data['clicks'] = 0;
                $data['unique_clicks'] = 0;
                $data['views'] = 0;
                $data['unique_views'] = 0;
                $data['unsent'] = 0;
                $data['errors'] = 0;
                $data['total_messages'] = 0;
                $data['sent'] = 0;
                $data['bounces'] = 0;
                $data['unsubscribes'] = 0;
                unset($data['run_times_left']);
                $data['conversions_number'] = 0;
                $data['conversions_amount'] = 0;
                $data['conversions_average'] = 0;

                $model->setData($data)
                      ->save();

                $this->messageManager->addSuccessMessage(__('You have duplicated the Campaign.'));

                return $resultRedirect->setPath('*/campaigns/edit', ['id' => $model->getId()]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while duplicating the campaign.')
                );
            }

            return $resultRedirect->setPath('*/*/');
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find an campaign to duplicate.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
