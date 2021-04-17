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

namespace Licentia\Panda\Model\Source;

/**
 * Class SmsSenders
 *
 * @package Licentia\Panda\Model\Source
 */
class SmsSenders implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected \Licentia\Panda\Model\SendersFactory $sendersFactory;

    /**
     * EmailSenders constructor.
     *
     * @param \Licentia\Panda\Model\SendersFactory $sendersFactory
     */
    public function __construct(
        \Licentia\Panda\Model\SendersFactory $sendersFactory
    ) {

        $this->sendersFactory = $sendersFactory;
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {

        $collection = $this->sendersFactory->create()
                                           ->getCollection()->addFieldToFilter('type', 'sms');

        $return = [];

        /** @var \Licentia\Panda\Model\Senders $item */
        foreach ($collection as $item) {
            $return[] = ['value' => $item->getId(), 'label' => $item->getName() . ' / ' . $item->getEmail()];
        }

        return $return;
    }
}
