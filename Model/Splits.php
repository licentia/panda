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

namespace Licentia\Panda\Model;

/**
 * Class Splits
 *
 * @package Licentia\Panda\Model
 */
class Splits extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_splits';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'splits';

    /**
     * @var ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscriberCollection;

    /**
     * @var SplitsFactory
     */
    protected $splitsFactory;

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var
     */
    protected $campaignsCollection;

    /**
     * @var
     */
    protected $splitsCollection;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $helperPanda;

    /**
     * @var TagsFactory
     */
    protected $tagsFactory;

    /**
     * Splits constructor.
     *
     * @param TagsFactory                                                  $tagsFactory
     * @param \Licentia\Panda\Helper\Data                                  $helperPanda
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $timezone
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param CampaignsFactory                                             $campaignsFactory
     * @param SplitsFactory                                                $splitsFactory
     * @param ResourceModel\Campaigns\CollectionFactory                    $campaignsCollection
     * @param ResourceModel\Splits\CollectionFactory                       $splitsCollection
     * @param ResourceModel\Subscribers\CollectionFactory                  $subscriberCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        TagsFactory $tagsFactory,
        \Licentia\Panda\Helper\Data $helperPanda,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CampaignsFactory $campaignsFactory,
        SplitsFactory $splitsFactory,
        ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        ResourceModel\Splits\CollectionFactory $splitsCollection,
        ResourceModel\Subscribers\CollectionFactory $subscriberCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->helperPanda = $helperPanda;
        $this->timezone = $timezone;
        $this->subscriberCollection = $subscriberCollection;
        $this->splitsFactory = $splitsFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->campaignsCollection = $campaignsCollection;
        $this->splitsCollection = $splitsCollection;
        $this->tagsFactory = $tagsFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Splits::class);
    }

    /**
     * @return array
     */
    public static function getTestingOptions()
    {

        return [
            'sender'                 => __('Sender'),
            'subject'                => __('Subject'),
            'message'                => __('Message'),
            'sender_subject_message' => __('Sender + Subject + Message'),
            'sender_subject'         => __('Sender + Subject'),
            'sender_message'         => __('Sender + Message'),
            'subject_message'        => __('Subject + Message'),
        ];
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterDelete()
    {

        $this->tagsFactory->create()->updateTags('splits', $this, []);

        return parent::afterDelete();
    }

    /**
     * @return $this
     */
    public function afterLoad()
    {

        parent::afterLoad();

        if ($this->getId()) {
            $tags = $this->tagsFactory->create()->getTagsHash('splits', $this->getId());
            $this->setData('tags', array_flip($tags));
        }

        return $this;
    }

    /**
     * @param Splits $split
     *
     * @return ResourceModel\Subscribers\Collection
     */
    public function getSubscribersCollection(Splits $split)
    {

        $collectionTemp = $this->subscriberCollection->create()
                                                     ->addSegments($split->getSegmentsIds())
                                                     ->addStoreIds($split->getStoreId())
                                                     ->addFieldToSelect('subscriber_id');

        if ($split->getPreviousCustomers() == 1) {
            $collectionTemp->addFieldToFilter('previous_customer', 1);
        } else {
            $collectionTemp->addActiveSubscribers();
        }

        return $collectionTemp;
    }

    /**
     *
     * @param Splits $split
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTestSubscribers(Splits $split)
    {

        if ($split->getRecipients()) {
            return $split->getRecipients();
        }

        $collectionTemp = $this->getSubscribersCollection($split);

        if (strlen($split->getPercentage()) == 1) {
            $perc = "0." . $split->getPercentage();
        } else {
            $perc = $split->getPercentage();
        }

        $total = $collectionTemp->getSize();
        $limit = ceil($total * $perc / 100);

        if ($limit < 2) {
            $limit = 2;
        }

        if (!$limit % 2) {
            $limit++;
        }

        $collectionA = $this->getSubscribersCollection($split);
        $collectionA->setOrder('subscriber_id', 'ASC');

        $collectionA->setPageSize($limit / 2);
        //$collectionA->setCurPage($limit / 2);
        $a = $collectionA->getLastItem()->getSubscriberId();

        $collectionB = $this->getSubscribersCollection($split);
        $collectionB->setOrder('subscriber_id', 'ASC');

        $collectionB->setPageSize($limit);
        //$collectionB->setCurPage($limit);
        $b = $collectionB->getLastItem()->getSubscriberId();

        $split->setRecipients($a . ',' . $b)->save();

        return $split->getRecipients();
    }

    /**
     *
     * @return boolean
     * @throws \Exception
     */
    public function cron()
    {

        $date = $this->helperPanda->gmtDate();

        $collectionPercentage = $this->splitsCollection->create()
                                                       ->addFieldToFilter('sent', 0)
                                                       ->addFieldToFilter('is_active', 1)
                                                       ->addFieldToFilter('deploy_at', ['lteq' => $date]);

        /** @var Splits $split */
        foreach ($collectionPercentage as $split) {
            foreach (['a', 'b'] as $version) {
                $this->sendCampaignData($split, $version);
            }

            $split->setsent(1)->save();
        }

        $collectionGeneral = $this->splitsCollection->create()
                                                    ->addFieldToFilter('sent', 1)
                                                    ->addFieldToFilter('closed', 0)
                                                    ->addFieldToFilter('is_active', 1)
                                                    ->addFieldToFilter('winner', ['neq' => 'manually'])
                                                    ->addFieldToFilter('send_at', ['lteq' => $date]);

        foreach ($collectionGeneral as $split) {
            $winner = $split->getWinner();

            if (($winner == 'views' && $split->getViewsA() >= $split->getViewsB()) ||
                ($winner == 'clicks' && $split->getClicksA() >= $split->getClicksB()) ||
                ($winner == 'conversions' && $split->getConversionsA() >= $split->getConversionsB())
            ) {
                $version = 'a';
            } else {
                $version = 'b';
            }

            $this->sendCampaignData($split, $version, true);

            $split->setClosed(1)
                  ->setIsActive(0)
                  ->save();
        }

        return true;
    }

    /**
     *
     * @param Splits $split
     * @param        $version
     *
     * @throws \Exception
     */
    public function sendManually(Splits $split, $version)
    {

        $this->sendCampaignData($split, $version, true);
        $split->setClosed(1)
              ->save();
    }

    /**
     * @param Splits     $split
     * @param            $version
     * @param bool|false $final
     *
     * @return mixed
     * @throws \Exception
     */
    protected function sendCampaignData(Splits $split, $version, $final = false)
    {

        $options = explode('_', $split->getTesting());
        $message = (in_array('message', $options)) ? $version : 'a';
        $sender = (in_array('sender', $options)) ? $version : 'a';
        $subject = (in_array('subject', $options)) ? $version : 'a';

        $data = [];
        $data['subject'] = $split->getData('subject_' . $subject);
        $data['internal_name'] = '[A/B] ' . $split->getName();
        $data['deploy_at'] = $split->getDeployAt();
        $data['message'] = $split->getData('message_' . $message);
        $data['sender_id'] = $split->getData('sender_id_' . $sender);
        $data['recurring'] = '0';
        $data['status'] = 'standby';
        $data['segments_ids'] = $split->getSegmentsIds();
        $data['store_id'] = $split->getStoreId();
        $data['autologin'] = $split->getAutologin();
        $data['global_template_id'] = $split->getGlobalTemplateId();
        $data['track'] = $split->getTrack();
        $data['auto'] = ($final) ? 0 : 1;
        $data['split_id'] = $split->getId();
        $data['split_version'] = $version;
        $data['split_final'] = ($final) ? 1 : 0;
        $data['previous_customers'] = $split->getPreviousCustomers();
        $data['number_recipients'] = $split->getNumberRecipients();

        $data['controller'] = false;

        $newCampaign = $this->campaignsFactory->create()
                                              ->setData($data)
                                              ->save();

        if ($final == 1) {
            $this->updateStatsForMainSplit($split, $newCampaign);
        }

        return $newCampaign;
    }

    /**
     *
     * @param Splits    $split
     * @param Campaigns $campaign
     *
     * @throws \Exception
     */
    public function updateStatsForMainSplit(Splits $split, Campaigns $campaign)
    {

        $tables = ['conversions', 'stats', 'coupons'];

        $campaigns = $this->campaignsCollection->create()
                                               ->addFieldToFilter('split_id', $split->getId())
                                               ->addFieldToFilter('split_final', 0);

        /** @var Campaigns $record */
        foreach ($campaigns as $record) {
            foreach ($tables as $table) {
                $this->getResource()
                     ->updateStatsForMainSplit(
                         'panda_' . $table,
                         ['campaign_id' => $campaign->getId()],
                         ['campaign_id = ?' => $record->getId()]
                     );
            }

            $campaign->setClicks($record->getClicks() + $campaign->getClicks());
            $campaign->setUniqueClicks($record->getUniqueClicks() + $campaign->getUniqueClicks());
            $campaign->setViews($record->getViews() + $campaign->getViews());
            $campaign->setUniqueViews($record->getUniqueViews() + $campaign->getUniqueViews());

            $campaign->save();
        }
    }

    /**
     * @param      $splitId
     * @param bool $field
     *
     * @return bool|\Magento\Framework\DataObject
     */
    public function getFinalCampaign($splitId, $field = false)
    {

        $campaigns = $this->campaignsCollection->create()
                                               ->addFieldToFilter('split_id', $splitId)
                                               ->addFieldToFilter('split_final', 1);

        if ($field) {
            $campaigns->addFieldToSelect($field);
        }

        if ($campaigns->count() == 1) {
            return $campaigns->getFirstItem();
        }

        return new \Magento\Framework\DataObject;
    }

    /**
     * @param      $splitId
     * @param bool $field
     *
     * @return array|ResourceModel\Campaigns\Collection
     */
    public function getTestCampaigns($splitId, $field = false)
    {

        $campaigns = $this->campaignsCollection->create()
                                               ->addFieldToFilter('split_id', $splitId)
                                               ->addFieldToFilter('split_final', 0);

        if ($field) {
            $campaigns->addFieldToSelect($field);
        }

        if ($campaigns->count() == 0) {
            return [];
        }

        return $campaigns;
    }

    /**
     * @return array
     */
    public function getWinnerOptions()
    {

        return [
            'views'       => __('Views'),
            'clicks'      => __('Clicks'),
            'conversions' => __('Conversions'),
            'manually'    => __('Manually'),
        ];
    }

    /**
     * @param null $campaign
     *
     * @return bool|int
     */
    public function calculateNumberRecipients($campaign = null)
    {

        /** @var Campaigns $campaign */
        if (null === $campaign) {
            $campaign = $this;
        }

        if ($campaign->getSent() == 1 || $campaign->getClosed() == 1) {
            return false;
        }

        $subscribers = $this->subscriberCollection->create()
                                                  ->addSegments($campaign->getSegmentsIds())
                                                  ->addStoreIds($campaign->getStoreId());

        if ($campaign->getPreviousCustomers() == 1) {
            $subscribers->addFieldToFilter('previous_customer', 1);
        } else {
            $subscribers->addActiveSubscribers();
        }

        return $subscribers->getSize();
    }

    /**
     * @param $splitId
     *
     * @return $this
     */
    public function setSplitId($splitId)
    {

        return $this->setData('split_id', $splitId);
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
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {

        return $this->setData('store_id', $storeId);
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
     * @param $subjectA
     *
     * @return $this
     */
    public function setSubjectA($subjectA)
    {

        return $this->setData('subject_a', $subjectA);
    }

    /**
     * @param $subjectB
     *
     * @return $this
     */
    public function setSubjectB($subjectB)
    {

        return $this->setData('subject_b', $subjectB);
    }

    /**
     * @param $senderIdA
     *
     * @return $this
     */
    public function setSenderIdA($senderIdA)
    {

        return $this->setData('sender_id_a', $senderIdA);
    }

    /**
     * @param $senderIdB
     *
     * @return $this
     */
    public function setSenderIdB($senderIdB)
    {

        return $this->setData('sender_id_b', $senderIdB);
    }

    /**
     * @param $messageA
     *
     * @return $this
     */
    public function setMessageA($messageA)
    {

        return $this->setData('message_a', $messageA);
    }

    /**
     * @param $messageB
     *
     * @return $this
     */
    public function setMessageB($messageB)
    {

        return $this->setData('message_b', $messageB);
    }

    /**
     * @param $viewsA
     *
     * @return $this
     */
    public function setViewsA($viewsA)
    {

        return $this->setData('views_a', $viewsA);
    }

    /**
     * @param $viewsB
     *
     * @return $this
     */
    public function setViewsB($viewsB)
    {

        return $this->setData('views_b', $viewsB);
    }

    /**
     * @param $clicksA
     *
     * @return $this
     */
    public function setClicksA($clicksA)
    {

        return $this->setData('clicks_a', $clicksA);
    }

    /**
     * @param $clicksB
     *
     * @return $this
     */
    public function setClicksB($clicksB)
    {

        return $this->setData('clicks_b', $clicksB);
    }

    /**
     * @param $conversionsA
     *
     * @return $this
     */
    public function setConversionsA($conversionsA)
    {

        return $this->setData('conversions_a', $conversionsA);
    }

    /**
     * @param $conversionsB
     *
     * @return $this
     */
    public function setConversionsB($conversionsB)
    {

        return $this->setData('conversions_b', $conversionsB);
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
     * @param $deployAt
     *
     * @return $this
     */
    public function setDeployAt($deployAt)
    {

        return $this->setData('deploy_at', $deployAt);
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
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {

        return $this->setData('is_active', $isActive);
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
     * @param $winner
     *
     * @return $this
     */
    public function setWinner($winner)
    {

        return $this->setData('winner', $winner);
    }

    /**
     * @param $percentage
     *
     * @return $this
     */
    public function setPercentage($percentage)
    {

        return $this->setData('percentage', $percentage);
    }

    /**
     * @param $lastSubscriberId
     *
     * @return $this
     */
    public function setLastSubscriberId($lastSubscriberId)
    {

        return $this->setData('last_subscriber_id', $lastSubscriberId);
    }

    /**
     * @param $closed
     *
     * @return $this
     */
    public function setClosed($closed)
    {

        return $this->setData('closed', $closed);
    }

    /**
     * @param $recipientsA
     *
     * @return $this
     */
    public function setRecipientsA($recipientsA)
    {

        return $this->setData('recipients_a', $recipientsA);
    }

    /**
     * @param $recipientsB
     *
     * @return $this
     */
    public function setRecipientsB($recipientsB)
    {

        return $this->setData('recipients_b', $recipientsB);
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
     * @param $testing
     *
     * @return $this
     */
    public function setTesting($testing)
    {

        return $this->setData('testing', $testing);
    }

    /**
     * @param $previousCustomers
     *
     * @return $this
     */
    public function setPreviousCustomers($previousCustomers)
    {

        return $this->setData('previous_customers', $previousCustomers);
    }

    /**
     * @param $autologin
     *
     * @return $this
     */
    public function setAutologin($autologin)
    {

        return $this->setData('autologin', $autologin);
    }

    /**
     * @param $track
     *
     * @return $this
     */
    public function setTrack($track)
    {

        return $this->setData('track', $track);
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
    public function getSplitId()
    {

        return $this->getData('split_id');
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
    public function getStoreId()
    {

        return $this->getData('store_id');
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
    public function getSubjectA()
    {

        return $this->getData('subject_a');
    }

    /**
     * @return mixed
     */
    public function getSubjectB()
    {

        return $this->getData('subject_b');
    }

    /**
     * @return mixed
     */
    public function getSenderIdA()
    {

        return $this->getData('sender_id_a');
    }

    /**
     * @return mixed
     */
    public function getSenderIdB()
    {

        return $this->getData('sender_id_b');
    }

    /**
     * @return mixed
     */
    public function getMessageA()
    {

        return $this->getData('message_a');
    }

    /**
     * @return mixed
     */
    public function getMessageB()
    {

        return $this->getData('message_b');
    }

    /**
     * @return mixed
     */
    public function getViewsA()
    {

        return $this->getData('views_a');
    }

    /**
     * @return mixed
     */
    public function getViewsB()
    {

        return $this->getData('views_b');
    }

    /**
     * @return mixed
     */
    public function getClicksA()
    {

        return $this->getData('clicks_a');
    }

    /**
     * @return mixed
     */
    public function getClicksB()
    {

        return $this->getData('clicks_b');
    }

    /**
     * @return mixed
     */
    public function getConversionsA()
    {

        return $this->getData('conversions_a');
    }

    /**
     * @return mixed
     */
    public function getConversionsB()
    {

        return $this->getData('conversions_b');
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
    public function getDeployAt()
    {

        return $this->getData('deploy_at');
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
    public function getIsActive()
    {

        return $this->getData('is_active');
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
    public function getWinner()
    {

        return $this->getData('winner');
    }

    /**
     * @return mixed
     */
    public function getPercentage()
    {

        return $this->getData('percentage');
    }

    /**
     * @return mixed
     */
    public function getLastSubscriberId()
    {

        return $this->getData('last_subscriber_id');
    }

    /**
     * @return mixed
     */
    public function getClosed()
    {

        return $this->getData('closed');
    }

    /**
     * @return mixed
     */
    public function getRecipientsA()
    {

        return $this->getData('recipients_a');
    }

    /**
     * @return mixed
     */
    public function getRecipientsB()
    {

        return $this->getData('recipients_b');
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
    public function getTesting()
    {

        return $this->getData('testing');
    }

    /**
     * @return mixed
     */
    public function getPreviousCustomers()
    {

        return $this->getData('previous_customers');
    }

    /**
     * @return mixed
     */
    public function getAutologin()
    {

        return $this->getData('autologin');
    }

    /**
     * @return mixed
     */
    public function getTrack()
    {

        return $this->getData('track');
    }

    /**
     * @return mixed
     */
    public function getGlobalTemplateId()
    {

        return $this->getData('global_template_id');
    }

    /**
     * @param $numberRecipients
     *
     * @return $this
     */
    public function setNumberRecipients($numberRecipients)
    {

        return $this->setData('number_recipients', $numberRecipients);
    }

    /**
     * @return mixed
     */
    public function getNumberRecipients()
    {

        return $this->getData('number_recipients');
    }
}
