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
 * @modified   05/03/20, 20:41 GMT
 *
 */

namespace Licentia\Panda\Model\Service;

/**
 * Class ServiceAbstract
 *
 * @package Licentia\Panda\Model\Service
 */
abstract class ServiceAbstract extends \Magento\Newsletter\Model\Template
{

    /**
     * @var array
     */
    protected $templateVariations = [];

    /**
     * @var \Licentia\Panda\Model\FollowupFactory
     */
    protected $followupFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Licentia\Panda\Model\SplitsFactory
     */
    protected $splitsFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Queue\Collection
     */
    protected $queueCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory
     */
    protected $archiveCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\History\CollectionFactory
     */
    protected $historyCollection;

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected $sendersFactory;

    /**
     * @var \Licentia\Panda\Model\QueueFactory
     */
    protected $queueFactory;

    /**
     * @var \Licentia\Panda\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @var
     */
    protected $defaultStoreId;

    /**
     * @var \Licentia\Panda\Model\EventsFactory
     */
    protected $eventsFactory;

    /**
     * @var
     */
    protected $filter;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $newsletterData;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Licentia\Panda\Model\ErrorsFactory
     */
    protected $errorsFactory;

    /**
     * @var \Licentia\Panda\Model\ArchiveFactory
     */
    protected $archiveFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscriberCollection;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $encoderInterface;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Queue
     */
    protected $queueResource;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $storeModel;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory
     */
    protected $errorsCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory
     */
    protected $templatesCollection;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $listSegmentsFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesGlobalFactory
     */
    protected $templatesGlobalFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

    /**
     * @var \Licentia\Panda\Block\EmptyBlock
     */
    protected $emptyBlock;

    /**
     * @var \Magento\Framework\UrlInterface
     */

    private $urlInterface;

    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $shipmentTrackFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * ServiceAbstract constructor.
     *
     * @param \Licentia\Panda\Block\EmptyBlock                                  $emptyBlock
     * @param \Magento\Framework\View\FileSystem                                $viewFileSystem
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface              $timezone
     * @param \Magento\Framework\Model\Context                                  $context
     * @param \Magento\Framework\View\DesignInterface                           $design
     * @param \Magento\Framework\Registry                                       $registry
     * @param \Magento\Store\Model\App\Emulation                                $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface                        $storeManager
     * @param \Magento\Framework\View\Asset\Repository                          $assetRepo
     * @param \Magento\Framework\Filesystem                                     $filesystem
     * @param \Magento\Framework\UrlInterface                                   $urlInterface
     * @param \Magento\Framework\Message\ManagerInterface                       $messageManager
     * @param \Magento\Store\Model\Store                                        $storeModel
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                 $customerRepository
     * @param \Magento\Newsletter\Model\Template\FilterFactory                  $coreFilterFactory
     * @param \Magento\Review\Model\ReviewFactory                               $reviewFactory
     * @param \Magento\Framework\Url\EncoderInterface                           $encoderInterface
     * @param \Magento\Payment\Helper\Data                                      $paymentHelper
     * @param \Magento\Sales\Model\OrderFactory                                 $orderFactory
     * @param \Magento\Sales\Model\Order\Shipment\TrackFactory                  $trackFactory
     * @param \Magento\Backend\Model\Session                                    $session
     * @param \Licentia\Panda\Model\AutorespondersFactory                       $autorespondersFactory
     * @param \Licentia\Panda\Model\SendersFactory                              $sendersFactory
     * @param \Licentia\Panda\Model\CampaignsFactory                            $campaignsFactory
     * @param \Licentia\Panda\Model\FollowupFactory                             $followupFactory
     * @param \Licentia\Panda\Model\TemplatesGlobalFactory                      $templatesGlobalFactory
     * @param \Licentia\Panda\Model\EventsFactory                               $eventsFactory
     * @param \Licentia\Panda\Model\ArchiveFactory                              $archiveFactory
     * @param \Licentia\Panda\Model\ErrorsFactory                               $errorsFactory
     * @param \Licentia\Panda\Model\SplitsFactory                               $splitsFactory
     * @param \Magento\Framework\App\RequestInterface                           $request
     * @param \Licentia\Panda\Model\QueueFactory                                $queueFactory
     * @param \Licentia\Panda\Model\HistoryFactory                              $historyFactory
     * @param \Licentia\Panda\Model\TemplatesFactory                            $templatesFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory                          $formEntriesFactory
     * @param \Licentia\Panda\Model\SubscribersFactory                          $subscribersFactory
     * @param \Licentia\Panda\Helper\Data                                       $newsletterData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                $scopeConfig
     * @param \Magento\Email\Model\Template\Config                              $emailConfig
     * @param \Magento\Email\Model\TemplateFactory                              $templateFactory
     * @param \Magento\Framework\Filter\FilterManager                           $filterManager
     * @param \Magento\Framework\UrlInterface                                   $urlModel
     * @param \Magento\Framework\Url                                            $urlHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                   $productRepository
     * @param \Magento\Sales\Model\Order\ShipmentFactory                        $shipmentFactory
     * @param \Licentia\Panda\Model\ResourceModel\QueueFactory                  $queueResource
     * @param \Licentia\Panda\Model\ResourceModel\Queue\CollectionFactory       $queueCollection
     * @param \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory   $templatesCollection
     * @param \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $subscriberCollection
     * @param \Licentia\Panda\Model\ResourceModel\History\CollectionFactory     $historyCollection
     * @param \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory      $errorsCollection
     * @param \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory     $archiveCollection
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory               $listSegmentsFactory
     * @param array                                                             $data
     */
    public function __construct(
        \Licentia\Panda\Block\EmptyBlock $emptyBlock,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\Store $storeModel,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Newsletter\Model\Template\FilterFactory $coreFilterFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Framework\Url\EncoderInterface $encoderInterface,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Backend\Model\Session $session,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\FollowupFactory $followupFactory,
        \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory,
        \Licentia\Panda\Model\EventsFactory $eventsFactory,
        \Licentia\Panda\Model\ArchiveFactory $archiveFactory,
        \Licentia\Panda\Model\ErrorsFactory $errorsFactory,
        \Licentia\Panda\Model\SplitsFactory $splitsFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Licentia\Panda\Model\QueueFactory $queueFactory,
        \Licentia\Panda\Model\HistoryFactory $historyFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Helper\Data $newsletterData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\UrlInterface $urlModel,
        \Magento\Framework\Url $urlHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Licentia\Panda\Model\ResourceModel\QueueFactory $queueResource,
        \Licentia\Panda\Model\ResourceModel\Queue\CollectionFactory $queueCollection,
        \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection,
        \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $subscriberCollection,
        \Licentia\Panda\Model\ResourceModel\History\CollectionFactory $historyCollection,
        \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory $errorsCollection,
        \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory $archiveCollection,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $design,
            $registry,
            $appEmulation,
            $storeManager,
            $assetRepo,
            $filesystem,
            $scopeConfig,
            $emailConfig,
            $templateFactory,
            $filterManager,
            $urlModel,
            $request,
            $coreFilterFactory,
            $data
        );

        $this->emptyBlock = $emptyBlock;
        $this->viewFileSystem = $viewFileSystem;
        $this->queueResource = $queueResource;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->archiveCollection = $archiveCollection;
        $this->historyCollection = $historyCollection;
        $this->eventsFactory = $eventsFactory;
        $this->reviewFactory = $reviewFactory;
        $this->messageManager = $messageManager;
        $this->timezone = $timezone;
        $this->orderFactory = $orderFactory;
        $this->shipmentTrackFactory = $trackFactory;
        $this->shipmentFactory = $shipmentFactory;
        $this->paymentHelper = $paymentHelper;
        $this->urlInterface = $urlInterface;
        $this->sendersFactory = $sendersFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->followupFactory = $followupFactory;
        $this->splitsFactory = $splitsFactory;
        $this->queueCollection = $queueCollection;
        $this->queueFactory = $queueFactory;
        $this->newsletterData = $newsletterData;
        $this->templatesFactory = $templatesFactory;
        $this->historyFactory = $historyFactory;
        $this->archiveFactory = $archiveFactory;
        $this->errorsFactory = $errorsFactory;
        $this->storeModel = $storeModel;
        $this->session = $session;
        $this->subscriberCollection = $subscriberCollection;
        $this->subscribersFactory = $subscribersFactory;
        $this->errorsCollection = $errorsCollection;
        $this->customerRepository = $customerRepository;
        $this->encoderInterface = $encoderInterface;
        $this->productRepository = $productRepository;
        $this->urlHelper = $urlHelper;
        $this->templatesCollection = $templatesCollection;
        $this->listSegmentsFactory = $listSegmentsFactory;
        $this->templatesGlobalFactory = $templatesGlobalFactory;
        $this->formEntriesFactory = $formEntriesFactory;
    }

    /**
     * @return $this
     */
    public function buildQueue()
    {

        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->getData('campaign');
        $campaign->setSent(null);
        $campaign->setClicks(null);
        $campaign->setUniqueClicks(null);
        $campaign->setViews(null);
        $campaign->setUniqueViews(null);
        $campaign->setBounces(null);
        $campaign->setUnsubscribes(null);
        $campaign->unsetData('unsent');
        $subscribersIds = $this->getData('subscribers');

        $campaign->setStatus('running')->save();

        $queueCount = $this->scopeConfig->getValue('panda_nuntius/info/queue');

        $maxQueueHour = (int) $campaign->getData('max_queue_hour');
        $nextHourQueue = new \DateTime($this->newsletterData->gmtDate());
        $sendCount = $this->scopeConfig->getValue('panda_nuntius/info/count');

        if ($queueCount <= $sendCount) {
            $queueCount = $sendCount + 1;
        }

        if ($maxQueueHour > 0) {
            $lasMessageQueue = $this->queueCollection->create()
                                                     ->addFieldToFilter('campaign_id', $campaign->getId())
                                                     ->setOrder('send_date', 'DESC')
                                                     ->setPageSize('1');

            if ($lasMessageQueue->getSize() == 1) {
                if ($lasMessageQueue->getFirstItem()
                                    ->getData('send_at') > $nextHourQueue->format('Y-m-d H:i:s')) {
                    $nextHourQueue = new \DateTime(
                        $lasMessageQueue->getFirstItem()
                                        ->getData('send_at')
                    );
                }
            }
        }

        if ($queueCount < 100) {
            $queueCount = 100;
        }

        if ($subscribersIds) {
            if (!is_array($subscribersIds)) {
                $subscribersIds = explode(',', $subscribersIds);
            }
            $subscribersIds = array_unique($subscribersIds);

            if (count($subscribersIds) == 0) {
                $subscribersIds = null;
            }
        }

        /** @var \Licentia\Panda\Model\ResourceModel\Subscribers\Collection $subscribers */
        if (!$this->getData('once')) {
            if ($campaign->getFollowupId()) {
                $subscribers = $this->followupFactory->create()
                                                     ->getSubscribersObject($campaign->getFollowupId());
            } else {
                $subscribers = $this->subscriberCollection->create()
                                                          ->addSegments($campaign->getSegmentsIds())
                                                          ->addStoreIds($campaign->getStoreId());

                if ($campaign->getSplitId()) {
                    $split = $this->splitsFactory->create()->load($campaign->getSplitId());

                    $lastSubscribers = explode(',', $split->getTestSubscribers($split));

                    if ($campaign->getSplitFinal() == 1) {
                        $subscribers->addFieldToFilter('main_table.subscriber_id', ['gt' => $lastSubscribers[1]]);
                    } elseif ($campaign->getData('split_version') == 'a') {
                        $subscribers->addFieldToFilter('main_table.subscriber_id', ['lteq' => $lastSubscribers[0]]);
                    } elseif ($campaign->getData('split_version') == 'b') {
                        $subscribers->addFieldToFilter('main_table.subscriber_id', ['gt' => $lastSubscribers[0]]);
                        $subscribers->addFieldToFilter('main_table.subscriber_id', ['lteq' => $lastSubscribers[1]]);
                    }
                }
            }

            if (is_array($subscribersIds)) {
                $subscribers->addfieldToFilter('main_table.subscriber_id', ['in' => $subscribersIds]);
            }

            /** @var \Licentia\Panda\Model\Queue $last */
            $last = $this->queueCollection->create()
                                          ->addFieldToFilter('campaign_id', $campaign->getId())
                                          ->setOrder('subscriber_id', 'DESC')
                                          ->setPageSize(1)
                                          ->getFirstItem();

            if ($last->getSubscriberId()) {
                $subscribers->addfieldToFilter('main_table.subscriber_id', ['gt' => $last->getSubscriberId()]);
            } else {
                $last = $this->archiveCollection->create()
                                                ->addFieldToFilter('campaign_id', $campaign->getId())
                                                ->setOrder('subscriber_id', 'DESC')
                                                ->setPageSize(1)
                                                ->getFirstItem();

                if ($last->getSubscriberId()) {
                    $subscribers->addfieldToFilter('main_table.subscriber_id', ['gt' => $last->getSubscriberId()]);
                }
            }

            if ($campaign->getParentId() && $campaign->getRecurringUnique() == 1) {
                $previousHistorySubscribers = $this->historyCollection->create()
                                                                      ->addFieldToSelect('subscriber_id')
                                                                      ->addFieldToFilter(
                                                                          'campaign_id',
                                                                          $campaign->getParentId()
                                                                      )
                                                                      ->getAllIds('subscriber_id');

                $subscribers->addFieldToFilter('main_table.subscriber_id', ['nin' => $previousHistorySubscribers]);
            }

            $subscribers->setOrder('main_table.subscriber_id', 'ASC');
            $subscribers->setPageSize($queueCount);
        } else {
            $subscribers = $this->subscriberCollection->create()
                                                      ->addfieldToFilter('subscriber_id', ['in' => $subscribersIds]);
        }

        if ($campaign->getData('previous_customers') == 1) {
            $subscribers->addFieldToFilter('previous_customer', '1');
        } else {
            $subscribers->addActiveSubscribers();
        }

        $subscribers->addSubscriberTypeFilter($campaign->getType());

        $historySelect = $subscribers->getResource()
                                     ->getConnection()
                                     ->select()
                                     ->from($this->getResource()->getTable('panda_messages_history'), ['subscriber_id'])
                                     ->where('campaign_id=?', $campaign->getId());

        $subscribers->getSelect()->where("subscriber_id NOT IN ($historySelect)");

        $subscribers->setOrder('store_id');
        $buildFrom = $campaign->getSenderId();
        $sender = $this->sendersFactory->create()->load($buildFrom);

        $queue = $this->queueFactory->create();
        $history = $this->historyFactory->create();
        $i = 0;
        $errors = false;
        $currentEmulateStoreId = -1;
        $nextHourQueueCount = 0;

        $this->_registry->register('panda_segments_data', new \Magento\Framework\DataObject(), true);

        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        foreach ($subscribers as $subscriber) {
            $this->_registry->unregister('panda_segments_email');
            $this->_registry->register('panda_segments_email', $subscriber->getEmail());

            $isValid = $campaign->validate($subscriber);

            if (!$isValid) {
                continue;
            }

            if ($maxQueueHour > 0 && $nextHourQueueCount > $maxQueueHour) {
                $nextHourQueue->add(new \DateInterval('PT1H'));
                $nextHourQueueCount = 0;
            }

            if ($currentEmulateStoreId !== $subscriber->getStoreId()) {
                if ($currentEmulateStoreId !== -1) {
                    $this->revertDesign();
                }
                $currentEmulateStoreId = $subscriber->getStoreId() ? $subscriber->getStoreId() :
                    $this->getDefaultStoreId();
                $this->emulateDesign($currentEmulateStoreId);
            }

            /*
            $count = $this->historyCollection->create()
                                             ->addFieldToFilter('campaign_id', $campaign->getId())
                                             ->addFieldToFilter('subscriber_id', $subscriber->getId());

            if ($count->count() > 0) {
                continue;
            }
            */

            $parseContent = $this->parseDynamicMessageContent($campaign, $subscriber);

            $message = $parseContent->getMessage();
            $subject = $parseContent->getSubject();

            $data = [];
            $data['type'] = $campaign->getType();
            $data['campaign_id'] = $campaign->getId();
            $data['subscriber_id'] = $subscriber->getId();
            $data['sender_id'] = $sender->getId();
            $data['subject'] = $subject;
            $data['message'] = $message;
            $data['send_date'] = $nextHourQueue->format('Y-m-d H:i:s');

            $subscribersHours = (int) $this->scopeConfig->getValue('panda_nuntius/info/subscriber_hours');

            if ($subscribersHours > 0 && $subscriber->getLastMessageSentAt()) {
                $last = $this->queueCollection->create()
                                              ->addFieldToFilter('subscriber_id', $subscriber->getId())
                                              ->setOrder('send_date', 'DESC')
                                              ->setPageSize(1)
                                              ->getFirstItem();

                if ($last && $last->getData('send_date') > $subscriber->getLastMessageSentAt()) {
                    $lastMessage = new \Zend_Date($last->getData('send_date'));
                } else {
                    $lastMessage = new \Zend_Date($subscriber->getLastMessageSentAt());
                }

                $finalDate = new \Zend_Date($data['send_date']);
                if ($lastMessage->addHour($subscribersHours) > $finalDate) {
                    $finalDate->addHour($subscribersHours);
                }

                $data['send_date'] = $finalDate->get('YYYY-MM-dd HH:mm:ss');
            } elseif ($campaign->getData('subscriber_time') == 1) {
                $data['send_date'] = $this->getSendTime($campaign, $subscriber);
            }

            if ($campaign->getType() == 'email') {
                $data['sender_name'] = $sender->getName();
                $data['sender_email'] = $sender->getEmail();
                $data['name'] = $subscriber->getName();
                $data['email'] = $subscriber->getEmail();
                $data['headers'] = $this->_buildHeaders($campaign, $sender, $subscriber);
                $data['headers'] = json_encode($data['headers']);
            }

            if ($campaign->getType() == 'sms') {
                $data['cellphone'] = $subscriber->getCellphone();
            }

            try {
                $queue->setData($data)->save();
                $history->setData($data)->save();
                $nextHourQueueCount++;
                $i++;
            } catch (\Exception $e) {
                $errors = true;
            }

            if ($campaign->getNumberRecipients() && $campaign->getNumberRecipients() >= $campaign->getTotalMessages()) {
                $i = 0;
                break;
            }
        }

        $this->revertDesign();
        $status = ($i == 0) ? 'running' : 'queuing';

        if ($this->getData('once')) {
            $status = 'running';
        }

        if ($i > 0) {
            $campaign->setData('status', $status)
                     ->save();

            $this->setData('totalEmails', $i);
            $this->setData('errors', $errors);
            $this->setData('id', true);
            $this->setData('campaign', $campaign);
        } else {
            $campaign->setData('status', $status)
                     ->save();
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultStoreId()
    {

        if (is_null($this->defaultStoreId)) {
            $this->defaultStoreId = $this->storeManager->getWebsite()->getDefaultStore()->getId();
        }

        return $this->defaultStoreId;
    }

    /**
     * @return int
     */
    public function getType()
    {

        return self::TYPE_HTML;
    }

    /**
     * @param \Licentia\Panda\Model\Campaigns $campaign
     * @param                                 $subscriber
     *
     * @return \Magento\Framework\DataObject|string
     * @throws \Exception
     */
    public function parseDynamicMessageContent(\Licentia\Panda\Model\Campaigns $campaign, $subscriber)
    {

        /** @var \Licentia\Panda\Model\Subscribers $subscriber */

        $subscriberId = $subscriber->getId();

        $this->_registry->unregister('panda_subscriber');
        $this->_registry->unregister('current_customer');
        $this->_registry->unregister('panda_campaign');
        $this->_registry->register('panda_subscriber', $subscriber, true);
        $this->_registry->register('panda_campaign', $campaign, true);

        $customer = $subscriber->getCustomerId();

        if ($customer) {
            $this->_registry->register('current_customer', $customer, true);
        }
        $storeId = $subscriber->getStoreId() ? $subscriber->getStoreId() : $this->getDefaultStoreId();

        $this->emulateDesign($storeId);

        $this->_eventManager->dispatch(
            'panda_campaign_view',
            [
                'campaign'    => $campaign,
                'customer_id' => $customer,
                'subscriber'  => $subscriber,
            ]
        );

        $variables = [];
        $variables['subscriber'] = $subscriber;
        $variables['campaign'] = $campaign;

        if ($campaign->getUrl()) {
            $message = $this->newsletterData->getContentFromUrl($campaign, $subscriber);
        } else {
            if ($campaign->getAutoresponderEventId() > 0) {
                $event = $this->eventsFactory->create()->load($campaign->getAutoresponderEventId());
                $autoresponder = $this->autorespondersFactory->create()->load($event->getAutoresponderId());

                if ($event->getId()) {
                    if ($autoresponder->getEvent() == 'new_review' ||
                        $autoresponder->getEvent() == 'new_review_approved' ||
                        $autoresponder->getEvent() == 'new_review_self'
                    ) {
                        $variables['review'] = $this->reviewFactory->create()->load($event->getDataObjectId());
                        $variables['product'] = $this->productRepository->getById(
                            $variables['review']->getEntityPkValue()
                        );
                    }
                    if ($autoresponder->getEvent() == 'shipment_new_track') {

                        /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
                        $track = $this->shipmentTrackFactory->create()->load($event->getDataObjectId());
                        $campaign->setMessage(
                            str_replace(
                                [
                                    '{{track_title}}',
                                    '{{track_code}}',
                                    '{{order_id}}',
                                ],
                                [
                                    $track->getTitle(),
                                    $track->getTrackNumber(),
                                    $track->getShipment()
                                          ->getOrder()
                                          ->getIncrementId(),
                                ],
                                $campaign->getMessage()
                            )
                        );

                        $variables['shipment'] = $track->getShipment();
                        $variables['order'] = $track->getShipment()->getOrder();
                    }
                    if ($autoresponder->getEvent() == 'shipment_new_no_track') {

                        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
                        $shipment = $this->shipmentFactory->create()->load($event->getDataObjectId());

                        $variables['shipment'] = $shipment;
                        $variables['order'] = $shipment->getOrder();
                    }
                    if ($autoresponder->getEvent() == 'new_form_entry') {

                        /** @var \Licentia\Forms\Model\FormEntries $entries */
                        $entries = $this->formEntriesFactory->create()
                                                            ->load($event->getDataObjectId())
                                                            ->getForm();

                        $variables['form'] = $entries;
                        $variables['entry'] = $entries->getForm();
                    }

                    if (in_array($autoresponder->getEvent(), ['order_new', 'order_product']) ||
                        stripos($autoresponder->getEvent(), 'order_status') !== false
                    ) {
                        $variables['order'] = $this->orderFactory->create()->load($event->getDataObjectId());
                        $paymentBlock = $this->paymentHelper->getInfoBlock($variables['order']->getPayment())
                                                            ->setIsSecureMode(true);

                        $paymentBlock->getMethod()->setStore($storeId);
                        $paymentBlockHtml = $paymentBlock->toHtml();

                        $variables['payment_html'] = $paymentBlockHtml;

                        if ($event->getEvent() == 'order_product') {
                            $product = $this->productRepository->getById($autoresponder->getDataObjectId());
                            $this->_registry->register('current_product', $product, true);
                            $variables['product'] = $product;
                        }
                    }
                }
            }

            if ($campaign->getData('product_cycle')) {
                $product = $this->productRepository->getById($campaign->getData('cycle_product_id'));
                $variables['product'] = $product;
            }

            $dynContentObj = new \Magento\Framework\DataObject(
                [
                    'variables'  => $variables,
                    'subscriber' => $subscriber,
                ]
            );

            $this->_eventManager->dispatch('panda_add_message_variables', ['variables' => $dynContentObj]);
            $variables = $dynContentObj->getVariables();

            $message = '';
            $messageTemplate = null;
            if ($subscriber->getId()) {
                $messageTemplate = $this->getMessageTemplateForSubscriber($campaign, $subscriber);
            }
            if (!$messageTemplate) {
                $message = $campaign->getMessage();
            }

            if (!$subscriber->getId()) {
                $message = $campaign->getMessage();

            } elseif ($campaign->getTemplateFile() || ($messageTemplate && $messageTemplate->getTemplateFile())) {
                if ($messageTemplate && $messageTemplate->getTemplateFile()) {
                    $fileName = $messageTemplate->getTemplateFile();
                } else {
                    $fileName = $campaign->getTemplateFile();
                }

                $block = $this->emptyBlock;
                $block->setData('subscriber', $subscriber);
                $block->setData('campaign', $campaign);
                $block->setTemplate($fileName);
                $message = $block->toHtml();

                /*
                ob_start();
                try {
                    extract(['campaign' => $campaign, 'subscriber' => $subscriber], EXTR_SKIP);

                    include $fileName;
                } catch (\Exception $exception) {
                    ob_end_clean();
                    throw $exception;
                }

                $message = ob_get_clean();
                */
            }

            if ($subscriber->getStoreId() == 0) {
                $subscriber->setStoreId($this->getDefaultStoreId());
            }

            if ($campaign->getType() == 'email') {
                $message = $this->templatesGlobalFactory->create()
                                                        ->getTemplateForMessage(
                                                            $campaign->getGlobalTemplateId(),
                                                            $storeId,
                                                            $message
                                                        );
            }

            if (stripos($message, '{{inlinecss') !== false) {
                $message = $campaign->getMessageParsed();

                if (!$message) {
                    $message = $this->getFilter()->applyInlineCss($campaign->getMessage());
                    $this->campaignsFactory->create()
                                           ->setData(
                                               [
                                                   'campaign_id'    => $campaign->getId(),
                                                   'message_parsed' => $message,
                                               ]
                                           )
                                           ->save();

                    $campaign->setMessageParsed($message);
                }
            }

            if (stripos($message, '{{') !== false) {
                $message = $this->setTemplateText($message)
                                ->getProcessedTemplate($variables);

                $message = $this->getFilter()
                                ->setVariables($variables)
                                ->filter($message);
            }

        }

        $subject = $campaign->getSubject();

        if (stripos($subject, '{{') !== false) {
            $subject = $this->setTemplateText($subject)
                            ->getProcessedTemplate($variables);

            $subject = $this->getFilter()
                            ->setVariables($variables)
                            ->filter($subject);
        }

        $imgUrl = $this->urlHelper->setScope($storeId)
                                  ->getUrl(
                                      'panda/campaign/stat',
                                      [
                                          'c'      => $campaign->getCampaignId(),
                                          'u'      => $subscriberId,
                                          '_nosid' => true,
                                      ]
                                  );

        if ($campaign->trackCampaign() && $campaign->getType() == 'email') {
            $message .= ' <img width="1" height="1" src="' . $imgUrl . '" border="0"> ';
            try {
                $doc = new \DOMDocument();
                $doc->loadHTML('<?xml encoding="UTF-8">' . $message);
                foreach ($doc->getElementsByTagName('a') as $link) {
                    if (substr($link->getAttribute('href'), 0, 8) != 'https://' &&
                        substr($link->getAttribute('href'), 0, 7) != 'http://') {
                        continue;
                    }

                    $urlParams = [
                        'u'      => $subscriber->getSubscriberId(),
                        'c'      => $campaign->getCampaignId(),
                        'url'    => $this->encoderInterface->encode($link->getAttribute('href')),
                        '_nosid' => true,
                    ];

                    if ($customer && $campaign->getAutologin()) {
                        try {
                            $_customer = $this->customerRepository->getById($customer);
                            if ($_customer) {
                                $cryptdata = $this->newsletterData->encrypt($_customer, true);
                                $urlParams = array_merge($urlParams, ['id' => $cryptdata]);
                            }
                        } catch (\Exception $e) {
                        }
                    }
                    $link->setAttribute('href', $this->urlHelper->getUrl('panda/campaign/go/', $urlParams));
                }

                foreach ($doc->childNodes as $item) {
                    if ($item->nodeType == XML_PI_NODE) {
                        $doc->removeChild($item);
                        $doc->encoding = 'UTF-8';
                    }
                }
                $message = $doc->saveHTML();
            } catch (\Exception $e) {
            }
        }

        $this->revertDesign();

        $return = new \Magento\Framework\DataObject;
        $return->setMessage($message);
        $return->setSubject($subject);

        return $return;
    }

    /**
     * @return \Magento\Newsletter\Model\Template\Filter
     */
    protected function getFilter()
    {

        if (!$this->filter) {
            $this->filter = $this->_filterFactory->create();
        }

        return $this->filter;
    }


    /*
     * Returns processed template
     * Called from Filter::foreachDirective()
     * @param string $templateCode Template code
     * @param array $variables Template processor variables
     * @return string Template processed
     */

    /**
     * @param       $templateCode
     * @param array $variables
     *
     * @return mixed
     */
    public function getInclude($templateCode, array $variables)
    {

        $processor = $this->getFilter();
        $processor->setVariables($variables);
        $message = $this->templatesFactory->create()
                                          ->load($templateCode)
                                          ->getMessage();

        return $processor->filter($message);
    }

    /**
     * @param \Licentia\Panda\Model\Campaigns   $campaign
     * @param \Licentia\Panda\Model\Subscribers $subscriber
     *
     * @return string|\Zend_Db_Expr
     */
    public function getSendTime(
        \Licentia\Panda\Model\Campaigns $campaign,
        \Licentia\Panda\Model\Subscribers $subscriber
    ) {

        $subscriberHour = $subscriber->getSendTime();
        if ($subscriberHour == -1 || $subscriberHour == '') {
            return new \Zend_Db_Expr('NULL');
        }

        $hour = date('H', strtotime($campaign->getDeployAt()));

        if ($hour == $subscriberHour) {
            return new \Zend_Db_Expr('NULL');
        }

        if ($hour < $subscriberHour) {
            $nHour = $subscriberHour - $hour;

            return date('Y-m-d H', strtotime('now +' . $nHour . 'hours')) . ':00:00';
        }

        if ($hour > $subscriberHour) {
            $nHour = 24 - $hour + $subscriberHour;

            return date('Y-m-d H', strtotime('now +' . $nHour . 'hours')) . ':00:00';
        }

        return new \Zend_Db_Expr('NULL');
    }

    /**
     * @param \Licentia\Panda\Model\Campaigns   $campaign
     * @param \Licentia\Panda\Model\Senders     $sender
     * @param \Licentia\Panda\Model\Subscribers $subscriber
     *
     * @return array
     */
    protected function _buildHeaders(
        \Licentia\Panda\Model\Campaigns $campaign,
        \Licentia\Panda\Model\Senders $sender,
        \Licentia\Panda\Model\Subscribers $subscriber
    ) {

        $headersNoNo = ['cc', 'bcc', 'return-path'];

        $returnHeaders = [];
        $headersSmtp = $sender->getData('headers');

        $find =
            [
                '{campaignId}',
                '{fromEmail}',
                '{fromName}',
                '{toEmail}',
                '{toName}',
                '{campaignName}',
                '{subject}',
            ];

        $replace = [
            $campaign->getId(),
            $sender->getEmail(),
            $sender->getName(),
            $subscriber->getEmail(),
            $subscriber->getName(),
            $campaign->getInternalName(),
            $campaign->getSubject(),
        ];

        $headers = explode("\n", str_replace($find, $replace, $headersSmtp));

        foreach ($headers as $header) {
            $parts = explode('|', $header);
            if (count($parts) != 2) {
                continue;
            }
            $name = trim($parts[0]);
            $value = trim($parts[1]);

            if (in_array($name, $headersNoNo)) {
                continue;
            }
            $returnHeaders[$name] = $value;
        }

        if ($sender->getData('bounces_email')) {
            $returnHeaders['return-path'] = $sender->getData('bounces_email');
        }

        $returnHeaders['X-Panda-Cid'] = $campaign->getId();
        $returnHeaders['X-Panda-Sid'] = $subscriber->getId();
        $returnHeaders['List-Unsubscribe'] = $subscriber->getUnsubscriptionLink(true);

        return $returnHeaders;
    }

    /**
     * @param \Licentia\Panda\Model\Campaigns   $campaign
     * @param \Licentia\Panda\Model\Subscribers $subscriber
     *
     * @return bool|mixed
     */
    public function getMessageTemplateForSubscriber(
        \Licentia\Panda\Model\Campaigns $campaign,
        \Licentia\Panda\Model\Subscribers $subscriber
    ) {

        if (!$this->templateVariations) {
            $this->templateVariations = $this->templatesCollection->create()->addFieldToFilter('is_active', 1);

            if ($campaign->getTemplateId()) {
                $this->templateVariations->addFieldToFilter('parent_id', $campaign->getTemplateId());
            } else {
                $tId = $this->templatesFactory->create()->load($campaign->getId(), 'campaign_id');

                if (!$tId->getId()) {
                    return false;
                }
                $this->templateVariations->addFieldToFilter('parent_id', $tId->getId());
            }
        }

        foreach ($this->templateVariations as $template) {
            if ($age = $template->getAge()) {
                if (!$subscriber->getDob()) {
                    $subscriberAge = $subscriber->getKpis()->getAge();

                    if (!$subscriberAge) {
                        $subscriberAge = $subscriber->getKpis()->getPredictedAge();
                    }

                    if (!$subscriberAge) {
                        continue;
                    }
                } else {
                    $subscriberAge = (new \DateTime($subscriber->getDob()))
                        ->diff(new \DateTime('now'))
                        ->y;
                }

                $tmpRange = explode('-', $age);

                if ($subscriberAge >= $tmpRange[0] && (!isset($tmpRange[1]) || $subscriberAge <= $tmpRange[1])) {
                    continue;
                }
            }

            if ($gender = $template->getGender()) {
                $subscriberGender = $subscriber->getGender();

                if (!$subscriberGender) {
                    $subscriberGender = $subscriber->getKpis()->getGender();

                    if (!$subscriberGender) {
                        $subscriberGender = $subscriber->getKpis()->getPredictedGender();
                    }
                }

                if ($subscriberGender == 'ignore' and in_array($subscriber->getGender(), ['male', 'female'])) {
                    continue;
                }

                if ($subscriber->getGender() != $subscriberGender) {
                    continue;
                }
            }

            if ($template->getSegmentsIds()) {
                $segmentsIds = explode(',', $template->getSegmentsIds());

                $segs = $this->listSegmentsFactory->create()->addFieldToFilter('email', $subscriber->getEmail());

                $res = [];
                /** @var \Licentia\Equity\Model\Segments $seg */
                foreach ($segs as $seg) {
                    $res[] = $seg->getId();
                }

                if (!array_intersect($segmentsIds, $res)) {
                    continue;
                }
            }

            return $template;
        }

        return false;
    }
}
