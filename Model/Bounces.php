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

namespace Licentia\Panda\Model;

/**
 * Class Bounces
 *
 * @package Licentia\Panda\Model
 */
class Bounces extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected string $_eventPrefix = 'panda_bounces';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected string $_eventObject = 'bounces';

    /**
     * @var
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var SubscribersFactory
     */
    protected SubscribersFactory $subscribersFactory;

    /**
     * @var CampaignsFactory
     */
    protected CampaignsFactory $campaignsFactory;

    /**
     * @var BouncesFactory
     */
    protected BouncesFactory $bouncesFactory;

    /**
     * @var ResourceModel\Senders\CollectionFactory
     */
    protected ResourceModel\Senders\CollectionFactory $sendersCollection;

    /**
     * Bounces constructor.
     *
     * @param SubscribersFactory                                           $subscribersFactory
     * @param BouncesFactory                                               $bouncesFactory
     * @param CampaignsFactory                                             $campaignsFactory
     * @param ResourceModel\Senders\CollectionFactory                      $sendersCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scope
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        SubscribersFactory $subscribersFactory,
        BouncesFactory $bouncesFactory,
        CampaignsFactory $campaignsFactory,
        ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->subscribersFactory = $subscribersFactory;
        $this->scopeConfig = $scope;
        $this->pandaHelper = $pandaHelper;
        $this->sendersCollection = $sendersCollection;
        $this->campaignsFactory = $campaignsFactory;
        $this->bouncesFactory = $bouncesFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Bounces::class);
    }

    /**
     * @return $this
     */
    public function processBounces()
    {

        $senders = $this->sendersCollection->create();

        /** @var Senders $sender */
        foreach ($senders as $sender) {
            $config = ['ssl' => strtoupper($sender->getBouncesSsl())];
            $config['auth'] = $sender->getBouncesAuth();
            $config['host'] = $sender->getBouncesServer();
            $config['password'] = $this->pandaHelper->decrypt($sender->getBouncesPassword());
            $config['user'] = $sender->getBouncesUsername();
            $config['port'] = $sender->getBouncesPort();
            if (!filter_var($sender->getBouncesEmail(), FILTER_VALIDATE_EMAIL)) {
                return $this;
            }

            $maxBounces = $this->getMaxBounces();

            try {
                $mail = new \Laminas\Mail\Storage\Imap($config);
            } catch (\Exception $e) {
                $this->pandaHelper->logWarning($e);

                return $this;
            }

            $mailboxIds = $mail->getUniqueId();

            foreach ($mailboxIds as $uid) {

                try {

                    $number = $mail->getNumberByUniqueId($uid);
                    $message = $mail->getMessage($number);
                    $content = $message->getContent();
                    $s = null;
                    $c = null;
                    $code = null;
                    preg_match('/X-Panda-Sid:\s?(\d+)/', $content, $s);
                    preg_match('/X-Panda-Cid:\s?(\d+)/', $content, $c);
                    preg_match('/Diagnostic-Code:\s?(.*)/', $content, $code);

                    $subscriberId = (int) end($s);
                    $campaignId = (int) end($c);
                    $reason = end($code);

                    $subscriber = $this->subscribersFactory->create()->load($subscriberId);
                    $campaign = $this->campaignsFactory->create()->load($campaignId);
                    if (!$subscriber->getId() || !$campaign->getId()) {
                        #$mail->removeMessage($number);
                        continue;
                    }

                    $subscriber->setBounces($subscriber->getBounces() + 1)->save();
                    $campaign->setBounces($subscriber->getBounces() + 1)->save();

                    $data = [];
                    $data['campaign_id'] = $campaignId;
                    $data['subscriber_id'] = $subscriberId;
                    $data['code'] = $reason;
                    $data['content'] = $message->getContent();
                    $data['created_at'] = $this->pandaHelper->gmtDate();

                    $this->bouncesFactory->create()->setData($data)->save();
                    $mail->removeMessage($number);

                    if ($subscriber->getBounces() >= $maxBounces) {
                        $this->subscribersFactory->create()
                                                 ->loadByEmail($subscriber->getEmail())
                                                 ->unsubscribe();
                    }

                } catch (\Exception $e) {

                    $this->pandaHelper->logWarning($e);

                }

            }

        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxBounces()
    {

        return $this->scopeConfig->getValue(
            'panda/bounces/max_bounces',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @param $bounceId
     *
     * @return $this
     */
    public function setBounceId($bounceId)
    {

        return $this->setData('bounce_id', $bounceId);
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
     * @param $subscriberId
     *
     * @return $this
     */
    public function setSubscriberId($subscriberId)
    {

        return $this->setData('subscriber_id', $subscriberId);
    }

    /**
     * @param $code
     *
     * @return $this
     */
    public function setCode($code)
    {

        return $this->setData('code', $code);
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function setContent($content)
    {

        return $this->setData('content', $content);
    }

    /**
     * @param $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {

        return $this->setData('created_at', $createdAt);
    }

    /**
     * @return mixed
     */
    public function getBounceId()
    {

        return $this->getData('bounce_id');
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
    public function getSubscriberId()
    {

        return $this->getData('subscriber_id');
    }

    /**
     * @return mixed
     */
    public function getCode()
    {

        return $this->getData('code');
    }

    /**
     * @return mixed
     */
    public function getContent()
    {

        return $this->getData('content');
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {

        return $this->getData('created_at');
    }
}
