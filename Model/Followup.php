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

namespace Licentia\Panda\Model;

/**
 * Class Followup
 *
 * @package Licentia\Panda\Model
 */
class Followup extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_followup';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'followup';

    /**
     * @var FollowupFactory
     */
    protected $followupFactory;

    /**
     * @var SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var ResourceModel\Followup\CollectionFactory
     */
    protected $followupCollection;

    /**
     * @var ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscriberCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $helperPanda;

    /**
     * @param \Licentia\Panda\Helper\Data                                  $helperPanda
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $timezone
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param CampaignsFactory                                             $campaignsFactory
     * @param SubscribersFactory                                           $subscribersFactory
     * @param FollowupFactory                                              $followupFactory
     * @param ResourceModel\Followup\CollectionFactory                     $followupCollection
     * @param ResourceModel\Subscribers\CollectionFactory                  $subscriberCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $helperPanda,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CampaignsFactory $campaignsFactory,
        SubscribersFactory $subscribersFactory,
        FollowupFactory $followupFactory,
        ResourceModel\Followup\CollectionFactory $followupCollection,
        ResourceModel\Subscribers\CollectionFactory $subscriberCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->helperPanda = $helperPanda;
        $this->timezone = $timezone;
        $this->followupFactory = $followupFactory;
        $this->followupCollection = $followupCollection;
        $this->campaignsFactory = $campaignsFactory;
        $this->subscribersFactory = $subscribersFactory;
        $this->subscriberCollection = $subscriberCollection;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Panda\Model\ResourceModel\Followup::class);
    }

    /**
     *
     * @return array
     */
    public function getOptionValues()
    {

        $options = [];
        $options[] = ['value' => 'no_open', 'label' => __("Didn't open the message")];
        $options[] = ['value' => 'open', 'label' => __("Opened the message")];
        $options[] = ['value' => 'no_click', 'label' => __("Didn't click the message")];
        $options[] = ['value' => 'click', 'label' => __("Clicked the message")];
        $options[] = ['value' => 'no_conversion', 'label' => __("Didn't make a purchase (No Conversion)")];
        $options[] = ['value' => 'conversion', 'label' => __("Made a purchase (Conversion)")];

        return $options;
    }

    /**
     * @param $followupId
     *
     * @return mixed
     */
    public function getSubscribersObject($followupId)
    {

        $followup = $this->followupFactory->create()->load($followupId);

        $collection = $this->subscriberCollection->create();

        $select = $collection->getSelect();
        $subSelect = clone $select;

        $recipients = explode(',', $followup->getRecipientsOptions());

        if (in_array('open', $recipients)) {
            $select->join(
                $collection->getTable('panda_stats'),
                'main_table.subscriber_id=' . $collection->getTable('panda_stats') .
                '.subscriber_id',
                []
            );
            $select->where($collection->getTable('panda_stats') . ".type ='views' ");
            $select->where(
                $collection->getTable('panda_stats') . ".campaign_id =? ",
                $followup->getCampaignId()
            );
        }

        if (in_array('no_open', $recipients)) {
            $subSelect->reset('from');
            $subSelect->reset('columns');
            $subSelect->from($collection->getTable('panda_stats'), ['subscriber_id']);
            $subSelect->where(
                $collection->getTable('panda_stats') . ".campaign_id = ? ",
                $followup->getCampaignId()
            );
            $subSelect->where($collection->getTable('panda_stats') . ".type = ? ", 'views');
            $select->where("main_table.subscriber_id NOT IN (?)", $subSelect);
        }

        if (in_array('click', $recipients)) {
            $select->join(
                $collection->getTable('panda_stats'),
                'main_table.subscriber_id=' . $collection->getTable('panda_stats') .
                '.subscriber_id',
                []
            );
            $select->where($collection->getTable('panda_stats') . ".type ='clicks' ");
            $select->where(
                $collection->getTable('panda_stats') . ".campaign_id =? ",
                $followup->getCampaignId()
            );
        }

        if (in_array('no_click', $recipients)) {
            $subSelect->reset('from');
            $subSelect->reset('columns');
            $subSelect->from($collection->getTable('panda_stats'), ['subscriber_id']);
            $subSelect->where(
                $collection->getTable('panda_stats') . ".campaign_id = ? ",
                $followup->getCampaignId()
            );
            $subSelect->where($collection->getTable('panda_stats') . ".type = ? ", 'clicks');
            $select->where("main_table.subscriber_id NOT IN (?)", $subSelect);
        }

        if (in_array('conversion', $recipients)) {
            $select->join(
                $collection->getTable('panda_conversions'),
                'main_table.subscriber_id=' . $collection->getTable('panda_conversions') .
                '.subscriber_id',
                []
            );
            $select->where(
                $collection->getTable('panda_conversions') . ".campaign_id =? ",
                $followup->getCampaignId()
            );
        }

        if (in_array('no_conversion', $recipients)) {
            $subSelect->reset('from');
            $subSelect->reset('columns');
            $subSelect->from($collection->getTable('panda_conversions'), ['subscriber_id']);
            $subSelect->where(
                $collection->getTable('panda_conversions') . ".campaign_id = ? ",
                $followup->getCampaignId()
            );
            $select->where("main_table.subscriber_id NOT IN (?)", $subSelect);
        }

        $select->join(
            $collection->getTable('panda_messages_history'),
            'main_table.subscriber_id=' . $collection->getTable('panda_messages_history') . '.subscriber_id',
            []
        );
        $select->where(
            $collection->getTable('panda_messages_history') . ".campaign_id = ? ",
            $followup->getCampaignId()
        );

        $select->group("main_table.subscriber_id");

        return $collection;
    }

    /**
     *
     */
    public function cron()
    {

        $date = $this->helperPanda->gmtDate();

        $followups = $this->followupCollection->create()
                                              ->addFieldToFilter('send_at', ['lteq' => $date])
                                              ->addFieldToFilter('sent', 0)
                                              ->addFieldToFilter('is_active', 1);

        /** @var \Licentia\Panda\Model\Followup $followup */
        foreach ($followups as $followup) {

            /** @var \Licentia\Panda\Model\Campaigns $campaign */
            $campaign = $this->campaignsFactory->create()->load($followup->getCampaignId());

            $data = [];
            $data['subject'] = str_replace('{{subject}}', $campaign->getSubject(), $followup->getSubject());
            $data['internal_name'] = '[FU] ' . $campaign->getInternalName();
            $data['deploy_at'] = $followup->getSendAt();
            $data['message'] = str_replace("{{message}}", $campaign->getMessage(), $followup->getMessage());
            $data['sender_id'] = $campaign->getSenderId();
            $data['recurring'] = '0';
            $data['status'] = 'standby';
            $data['segments_ids'] = $campaign->getSegmentsIds();
            $data['previous_customers'] = $campaign->getPreviousCustomers();
            $data['track'] = $campaign->getData('track');
            $data['global_template_id'] = $campaign->getData('global_template_id');
            $data['autologin'] = $campaign->getData('autologin');
            $data['store_id'] = $campaign->getStoreId();
            $data['recurring'] = '0';
            $data['auto'] = '1';
            $data['followup_id'] = $followup->getId();

            $data['controller'] = false;

            $this->campaignsFactory->create()
                                   ->setData($data)
                                   ->save();

            $followup->setSent(1)
                     ->save();
        }
    }

    /**
     *
     * @param Campaigns $campaign
     *
     */
    public function updateSendDate(Campaigns $campaign)
    {

        $collection = $this->followupCollection->create()
                                               ->addFieldToFilter('campaign_id', $campaign->getId());

        /** @var Followup $item */
        foreach ($collection as $item) {
            $date = new \Zend_Date;
            $date->setTime($campaign->getDeployAt(), Campaigns::MYSQL_DATETIME)
                 ->setDate($campaign->getDeployAt(), Campaigns::MYSQL_DATETIME)
                 ->addDay($item->getDays());

            $item->setSendAt($date->get(Campaigns::MYSQL_DATETIME))
                 ->save();
        }
    }

    /**
     * @param null $followup
     *
     * @return bool
     */
    public function calculateNumberRecipients($followup = null)
    {

        if (null === $followup) {
            $followup = $this;
        }

        if ($followup->getSent() == 1 || $followup->getIsActive() == 0) {
            return false;
        }

        $subscribers = $this->getSubscribersObject($followup->getId());

        return $subscribers->getSize();
    }

    /**
     * @param $followupId
     *
     * @return $this
     */
    public function setFollowupId($followupId)
    {

        return $this->setData('followup_id', $followupId);
    }

    /**
     * @param $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId)
    {

        return $this->setData('campaign_id', $campaignId);
    }

    /**
     * @param $segmentsIds
     *
     * @return $this
     */
    public function setSegmentsIds($segmentsIds)
    {

        return $this->setData('segments_ids', $segmentsIds);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {

        return $this->setData('name', $name);
    }

    /**
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {

        return $this->setData('subject', $subject);
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setMessage($message)
    {

        return $this->setData('message', $message);
    }

    /**
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {

        return $this->setData('is_active', $isActive);
    }

    /**
     * @param $recipients
     *
     * @return $this
     */
    public function setRecipients($recipients)
    {

        return $this->setData('recipients', $recipients);
    }

    /**
     * @param $recipientsOptions
     *
     * @return $this
     */
    public function setRecipientsOptions($recipientsOptions)
    {

        return $this->setData('recipients_options', $recipientsOptions);
    }

    /**
     * @param $sendAt
     *
     * @return $this
     */
    public function setSendAt($sendAt)
    {

        return $this->setData('send_at', $sendAt);
    }

    /**
     * @param $days
     *
     * @return $this
     */
    public function setDays($days)
    {

        return $this->setData('days', $days);
    }

    /**
     * @param $hours
     *
     * @return $this
     */
    public function setHours($hours)
    {

        return $this->setData('hours', $hours);
    }

    /**
     * @param $sent
     *
     * @return $this
     */
    public function setSent($sent)
    {

        return $this->setData('sent', $sent);
    }

    /**
     * @param $globalTemplateId
     *
     * @return $this
     */
    public function setGlobalTemplateId($globalTemplateId)
    {

        return $this->setData('global_template_id', $globalTemplateId);
    }

    /**
     * @return mixed
     */
    public function getFollowupId()
    {

        return $this->getData('followup_id');
    }

    /**
     * @return mixed
     */
    public function getCampaignId()
    {

        return $this->getData('campaign_id');
    }

    /**
     * @return mixed
     */
    public function getSegmentsIds()
    {

        return $this->getData('segments_ids');
    }

    /**
     * @return mixed
     */
    public function getName()
    {

        return $this->getData('name');
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {

        return $this->getData('subject');
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {

        return $this->getData('message');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
    }

    /**
     * @return mixed
     */
    public function getRecipients()
    {

        return $this->getData('recipients');
    }

    /**
     * @return mixed
     */
    public function getRecipientsOptions()
    {

        return $this->getData('recipients_options');
    }

    /**
     * @return mixed
     */
    public function getSendAt()
    {

        return $this->getData('send_at');
    }

    /**
     * @return mixed
     */
    public function getDays()
    {

        return $this->getData('days');
    }

    /**
     * @return mixed
     */
    public function getHours()
    {

        return $this->getData('hours');
    }

    /**
     * @return mixed
     */
    public function getSent()
    {

        return $this->getData('sent');
    }

    /**
     * @return mixed
     */
    public function getGlobalTemplateId()
    {

        return $this->getData('global_template_id');
    }
}
