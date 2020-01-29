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

namespace Licentia\Panda\Cron;

/**
 * Class NotifyMessagesDeliveryFailures
 *
 * @package Licentia\Panda\Cron
 */
class NotifyMessagesDeliveryFailures
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory
     */
    protected $errorsCollection;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected $sendersFactory;

    /**
     * @var \Magento\Framework\Mail\TransportInterfaceFactory
     */
    protected $transportFactory;

    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $message;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * ClearOld constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory           $dateFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $timezone
     * @param \Magento\Framework\Mail\TransportInterfaceFactory            $transportFactory
     * @param \Magento\Framework\Mail\MessageInterface                     $message
     * @param \Licentia\Panda\Model\CampaignsFactory                       $campaignsFactory
     * @param \Licentia\Panda\Model\SendersFactory                         $sendersFactory
     * @param \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory $errorsCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfigInterface
     * @param \Licentia\Panda\Logger\Logger                                $pandaLogger
     *
     * @internal param \Magento\Framework\Mail\TransportInterface $transport
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Mail\TransportInterfaceFactory $transportFactory,
        \Magento\Framework\Mail\MessageInterface $message,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory $errorsCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Panda\Logger\Logger $pandaLogger
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaLogger = $pandaLogger;
        $this->errorsCollection = $errorsCollection;
        $this->campaignsFactory = $campaignsFactory;
        $this->sendersFactory = $sendersFactory;
        $this->message = $message;
        $this->transportFactory = $transportFactory;
        $this->timezone = $timezone;
        $this->dateFactory = $dateFactory;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function execute()
    {

        $emails = $this->scopeConfig->getValue('panda_nuntius/info/errors');

        if (strlen($emails) < 5) {
            return false;
        }

        $abandonedDate = new \DateTime($this->dateFactory->create()->gmtDate());
        $abandonedDate->sub(new \DateInterval('PT60M'));
        $date = $abandonedDate->format('Y-m-d H:i:s');

        $collection = $this->errorsCollection->create();
        $collection->addFieldToFilter('created_at', ['gteq' => $date]);

        if ($collection->getSize() == 0) {
            return false;
        }

        $message = '';
        $campaigns = [];
        $senders = [];

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $error) {
            if (!isset($campaigns[$error->getCampaignId()])) {
                $camp = $this->campaignsFactory->create()->load($error->getCampaignId());
                $campaigns[$error->getCampaignId()] = $camp->getId() . ' - ' . $camp->getInternalName();
            }

            if (!isset($senders[$error->getSenderId()])) {
                $sender = $this->sendersFactory->create()->load($error->getSenderId());
                $senders[$error->getSenderId()] = $sender->getName();
            }

            $message .= __('Campaign:') . ' ' . $campaigns[$error->getCampaignId()] . "<br>";
            $message .= __('Sender:') . ' ' . $senders[$error->getSenderId()] . "<br>";
            $message .= __('Error Message:') . ' ' . $error->getMessage() . "<br>";
            $message .= __('Date:') . ' ' . $error->getCreatedAt() . "<br>";
            $message .= "<br><br><br><br>";
        }

        $fromEmail = $this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->message->setBodyHtml($message);
        $this->message->setMessageType('html');
        $this->message->setFrom($fromEmail);
        $this->message->setSubject(__('Panda Marketing: Campaign Sending Issues'));

        $emails = explode(',', $emails);
        foreach ($emails as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->message->addTo($email);
            }
        }

        $this->transportFactory->create(['message' => $this->message])
                               ->sendMessage();
    }
}
