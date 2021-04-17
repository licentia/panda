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
 * Class Unsubscribes
 *
 * @package Licentia\Panda\Model
 */
class Unsubscribes extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected string $_eventPrefix = 'panda_unsubscribes';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected string $_eventObject = 'unsubscribes';

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var ResourceModel\Unsubscribes\CollectionFactory
     */
    protected ResourceModel\Unsubscribes\CollectionFactory $unsubscribesCollection;

    /**
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ResourceModel\Unsubscribes\CollectionFactory                 $collectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Unsubscribes\CollectionFactory $collectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->pandaHelper = $pandaHelper;
        $this->unsubscribesCollection = $collectionFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Unsubscribes::class);
    }

    /**
     * Logs unsubscribes
     *
     * @param Campaigns   $campaign
     * @param Subscribers $subscriber
     *
     * @return Unsubscribes
     * @throws \Exception
     */
    public function unsubscribe($campaign, $subscriber)
    {

        $unsubs = $this->unsubscribesCollection->create()
                                               ->addFieldToFilter('campaign_id', $campaign->getId())
                                               ->addFieldToFilter('email', $subscriber->getEmail());

        if ($unsubs->count() == 0) {
            $campaign->setUnsubscribes($campaign->getData('unsubscribes') + 1)
                     ->save();

            $data = [];
            $data['campaign_id'] = $campaign->getId();
            $data['email'] = $subscriber->getEmail();
            $data['cellphone'] = $subscriber->getCellphone();
            $data['unsubscribed_at'] = $this->pandaHelper->gmtDate();
            $this->setData($data)
                 ->save();
        }

        return $this;
    }
}
