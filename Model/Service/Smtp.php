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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Panda\Model\Service;

/**
 * Class Smtp
 *
 * @package Licentia\Panda\Model\Service
 */
class Smtp extends ServiceAbstract
{

    const EMAIL_MESSAGES_QUEUE = 5000;

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
    public function sendEmail($tryAgain = false, $errorIds = [])
    {

        if ($this->scopeConfig->getValue('system/smtp/disable')) {
            return $this;
        }

        $date = $this->pandaHelper->gmtDate();

        $count = (int) $this->scopeConfig->getValue('panda_nuntius/info/count');
        if ($count == 0) {
            $count = 100;
        }

        $headersNoNo = [
            'to',
            'cc',
            'bcc',
            'from',
            'subject',
            'reply-to',
            'return-path',
            'date',
            'message-id',
        ];

        if ($tryAgain) {
            $queue = $this->errorsCollection->create()
                                            ->addFieldToFilter('main_table.type', 'email')
                                            ->addFieldToFilter('error_id', ['in' => $errorIds]);
        } else {
            $uniqueRandomString = sha1(microtime());

            $this->queueResource->create()
                                ->addProcessId($count, $uniqueRandomString, $date, $this->pandaHelper);

            $queue = $this->queueCollection->create()
                                           ->setPageSize($count)
                                           ->addFieldToFilter('process_id', $uniqueRandomString)
                                           ->addFieldToFilter(
                                               ['send_date', 'send_date'],
                                               [
                                                   ['lteq' => $date],
                                                   ['null' => true],
                                               ]
                                           )
                                           ->addFieldToFilter('main_table.type', 'email')
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
        $a = 0;

        /** @var \Licentia\Panda\Model\ResourceModel\Queue $resource */
        $resource = $this->queueResource->create();

        /** @var \Licentia\Panda\Model\Queue $message */
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

            $transport = $this->pandaHelper->getSmtpTransport($sender);

            if (!$transport) {
                continue;
            }

            $mail = new \Zend_Mail('UTF-8');
            $mail->addTo($message->getEmail(), $message->getName());
            $mail->setSubject($message->getSubject());
            $mail->setBodyHtml($message->getMessage());
            $mail->setFrom($message->getSenderEmail(), $message->getSenderName());

            if ($sender->getReplyTo() && !$mail->getReplyTo()) {
                $mail->setReplyTo($sender->getReplyTo());
            }

            $headers = (array) json_decode($message->getHeaders());

            foreach ($headers as $name => $value) {
                if (in_array($name, $headersNoNo)) {
                    if ($name == 'bcc') {
                        $mail->addBcc($value);
                    }
                    if ($name == 'cc') {
                        $mail->addCc($value);
                    }
                    if ($name == 'reply-to' && !$mail->getReplyTo()) {
                        $mail->setReplyTo($value);
                    }
                    if ($name == 'return-path') {
                        $mail->setReturnPath($value);
                    }
                } else {
                    $mail->addHeader($name, $value);
                }
            }

            if (!isset($campaignData[$campaign->getId()])) {
                $campaignData[$campaign->getId()]['sent'] = 0;
            }
            $resource->beginTransaction();
            try {
                $data = $message->getData();
                $data['sent_date'] = $this->pandaHelper->gmtDate();
                $data['type'] = 'email';

                $archive->setData($data)->save();

                $mail->send($transport);

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

                $this->pandaHelper->logException($e);
            }

            if ($rollback && !$tryAgain) {
                if ($message->getAttempts() > 12) {
                    $data = [];
                    $data['campaign_id'] = $message->getCampaignId();
                    $data['subject'] = $message->getSubject();
                    $data['subscriber_id'] = $message->getSubscriberId();
                    $data['email'] = $message->getEmail();
                    $data['error_code'] = $e->getCode();
                    $data['error_message'] = $e->getMessage();
                    $data['created_at'] = $this->pandaHelper->gmtDate();

                    $dataInsert = array_merge($data, $message->getData());
                    $errors->setData($dataInsert)->save();

                    $message->delete();
                    $a++;
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

            if ($tryAgain) {
                $messages[] =
                    __(
                        'Campaign %1: %2 emails sent, %3 not sent',
                        $campaign->getInternalName(),
                        $campaignData[$campaign->getId()]['sent'],
                        $campaignData[$campaign->getId()]['unsent']
                    );
            }
        }

        $this->setData('sent', $i);

        $this->setData('messages', $messages);

        return $this;
    }

    /**
     * @param \Licentia\Panda\Model\Senders $sender
     * @param null                          $message
     * @param bool                          $test
     * @param array                         $recipients
     */
    public function validateEmailEnvironment(
        \Licentia\Panda\Model\Senders $sender,
        $message = null,
        $test = false,
        $recipients = []
    ) {

        $email = $sender->getData('email');

        if (!$message) {
            $message = "Hi there,<br><br>This is a test message to check if your settings are defined correctly.
                    <br><br>If you received this email, everything seems to be working fine<br><br>Be happy... :)";
        }

        $transport = $this->pandaHelper->getSmtpTransport($sender);

        $mail = new \Zend_Mail('UTF-8');
        $mail->setBodyHtml($message);
        $mail->setFrom($email, $sender->getName());

        $hasRecipient = false;
        foreach ($recipients as $recipient) {
            $recipient = trim($recipient);
            if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $mail->addTo($recipient);
                $hasRecipient = true;
            }
        }

        if (!$hasRecipient) {
            $mail->addTo($email);
        }

        if ($sender->getReplyTo() && !$mail->getReplyTo()) {
            $mail->setReplyTo($sender->getReplyTo());
        }

        $mail->setSubject('Test Message - Magento|Green Flying Panda');

        try {
            $mail->send($transport);
            if (!$test) {
                $this->messageManager->addSuccessMessage(
                    __('Message sent to %1', implode(' ', (array) $email))
                );
                $this->messageManager->addSuccessMessage(
                    __('Everything Seems To Be OK with your SMTP Configuration!!!')
                );
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
            if (!$test) {
                $this->messageManager->addErrorMessage(
                    __('Error with SMTP Configuration (Server error): ') . $e->getMessage()
                );
            }
        }
        if (!$test) {
            $config = [];
            if ($sender->getBouncesSsl() != 'none') {
                $config['ssl'] = strtoupper($sender->getBouncesSsl());
            }
            $config['password'] = $sender->getBouncesPassword();
            $config['host'] = $sender->getBouncesServer();
            $config['user'] = $sender->getBouncesUsername();
            $config['port'] = $sender->getBouncesPort();

            if (strlen($sender->getBouncesEmail()) > 0) {
                try {
                    new \Zend_Mail_Storage_Imap($config);
                    $this->messageManager->addSuccessMessage(
                        __('Everything Seems To Be OK with your Bounces Configuration!!!')
                    );
                } catch (\Exception $e) {
                    $this->pandaHelper->logWarning($e);
                    $this->messageManager->addErrorMessage(__('Error Bounces Configuration: ') . $e->getMessage());
                }
            }
        }
    }
}
