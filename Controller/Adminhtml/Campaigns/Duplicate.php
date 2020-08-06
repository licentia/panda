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
