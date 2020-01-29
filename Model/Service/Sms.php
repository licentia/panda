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

namespace Licentia\Panda\Model\Service;

/**
 * Class Smtp
 *
 * @package Licentia\Panda\Model\Service
 */
class Sms extends \Licentia\Panda\Model\Service\ServiceAbstract
{

    /**
     * @var array
     */
    protected $campaigns = [];

    /**
     * @var array
     */
    protected $senders = [];

    /**
     * @param bool  $tryAgain
     * @param array $errorIds
     *
     * @return $this
     */
    public function sendSms($tryAgain = false, $errorIds = [])
    {

        if ($this->scopeConfig->getValue('system/smtp/disable')) {
            return $this;
        }

        $date = $this->newsletterData->gmtDate();

        $count = (int) $this->scopeConfig->getValue('panda_nuntius/info/count');
        if ($count == 0) {
            $count = 100;
        }
        if ($tryAgain) {
            $queue = $this->errorsCollection->create()
                                            ->addFieldToFilter('main_table.type', 'sms')
                                            ->addFieldToFilter('error_id', ['in' => $errorIds]);
        } else {
            /** @var \Licentia\Panda\Model\ResourceModel\Queue\Collection $queue */
            $queue = $this->queueCollection->create()
                                           ->setPageSize($count)
                                           ->addFieldToFilter(
                                               ['send_date', 'send_date'],
                                               [
                                                   ['lteq' => $date],
                                                   ['null' => true],
                                               ]
                                           )
                                           ->addFieldToFilter('main_table.type', 'sms')
                                           ->setCurPage(1);
        }

        $queue->getSelect()
              ->joinLeft(
                  ['camp' => $queue->getTable('panda_campaigns')],
                  'main_table.campaign_id = camp.campaign_id',
                  []
              );

        $queue->addFieldToFilter('camp.status', ['nin' => 'paused', 'canceled']);

        $errors = $this->errorsFactory->create();
        $archive = $this->archiveFactory->create();

        $campaignData = [];
        $i = 0;

        /** @var  $resource \Licentia\Panda\Model\ResourceModel\Queue */
        $resource = $this->queueResource->create();

        foreach ($queue as $message) {
            if (!isset($this->campaigns[$message->getCampaignId()])) {
                /** @var \Licentia\Panda\Model\Campaigns $campaign */
                $campaign = $this->campaignsFactory->create()->load($message->getCampaignId());
                $this->campaigns[$message->getCampaignId()] = $campaign;
            } else {
                $campaign = $this->campaigns[$message->getCampaignId()];
            }

            if (!isset($this->senders[$message->getCampaignId()])) {
                $sender = $this->sendersFactory->create()->load($campaign->getSenderId());
                $this->senders[$campaign->getSenderId()] = $sender;
            } else {
                $sender = $this->senders[$campaign->getSenderId()];
            }

            $transport = $this->newsletterData->getSmsTransport($sender);

            if (!$transport) {
                return false;
            }

            if (!isset($campaignData[$campaign->getId()])) {
                $campaignData[$campaign->getId()]['sent'] = 0;
            }

            $resource->beginTransaction();
            try {
                $data = $message->getData();
                $data['sent_date'] = $this->newsletterData->gmtDate();
                $data['type'] = 'sms';
                $data['sender_name'] = $sender->getName();
                $archive->setData($data)->save();

                $transport->sendSMS($message->getCellphone(), $message->getMessage());

                $campaignData[$campaign->getId()]['sent'] = $campaignData[$campaign->getId()]['sent'] + 1;

                $message->delete();
                $rollback = false;
            } catch (\Exception $e) {
                if (stripos($e->getMessage(), 'uni_subs_camp') !== false) {
                    $message->delete();
                    continue;
                }
                $resource->rollBack();
                $rollback = true;

                $this->_logger->critical($e->getMessage());
            }

            if ($rollback && !$tryAgain) {
                if ($message->getAttempts() > 2) {
                    $data = [];
                    $data['type'] = 'sms';
                    $data['campaign_id'] = $message->getCampaignId();
                    $data['sender_id'] = $message->getSenderId();
                    $data['subscriber_id'] = $message->getSubscriberId();
                    $data['cellphone'] = $message->getCellphone();
                    $data['error_code'] = $e->getCode();
                    $data['error_message'] = $e->getMessage();
                    $data['created_at'] = $this->newsletterData->gmtDate();

                    $dataInsert = array_merge($data, $message->getData());
                    $errors->setData($dataInsert)->save();

                    $message->delete();
                } else {
                    $message->setData('attempts', $message->getData('attempts') + 1)->save();
                }
            }

            if (!$rollback) {
                $resource->commit();
            }
        }

        $messages = [];
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        foreach ($this->campaigns as $campaign) {
            $pending = $this->queueCollection->create()
                                             ->addFieldToSelect('campaign_id')
                                             ->addFieldToFilter('campaign_id', $campaign->getId());

            if ($campaign->getStatus() == 'running' && $pending->getSize() == 0) {
                $campaign->setData('status', 'finished');
            }

            $campaign->save();

            if ($pending->getSize() == 0) {
                $campaign->updateCampaignAfterSend($campaign, true);
            }
        }

        $this->setData('sent', $i);
        $this->setData('messages', $messages);

        return $this;
    }

    /**
     * @param \Licentia\Panda\Model\Senders $sender
     *
     * @return bool
     */
    public function validateSmsEnvironment($sender)
    {

        $transport = $this->newsletterData->getSmsTransport($sender);

        if (!$transport) {
            return false;
        }

        $message = "Hi there. This is a test message sent from Green Flying Panda. " .
                   "Everything seems to be working fine. Be happy... :)";

        try {
            $transport->sendSMS($sender->getTestSms(), $message);

            $this->messageManager->addSuccessMessage(__('Everything Seems To Be OK with your SMS Configuration!!!'));
        } catch (\Exception $e) {
            $this->_logger->warning($e->getMessage());
            $this->messageManager->addErrorMessage(
                __('Error Sending SMS. Please verify your auth data: ') . $e->getMessage()
            );

            return false;
        }

        return true;
    }
}
