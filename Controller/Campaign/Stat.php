<?php
/**
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

namespace Licentia\Panda\Controller\Campaign;

use Magento\Framework\App\Action\Context;

/**
 * Class View
 *
 * @package Licentia\Panda\Controller\Campaign
 */
class Stat extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Model\StatsFactory
     */
    protected $statsFactory;

    /**
     * @param Context                                  $context
     * @param \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
     * @param \Licentia\Panda\Model\CampaignsFactory   $campaignsFactory
     * @param \Licentia\Panda\Model\StatsFactory       $statsFactory
     */
    public function __construct(
        Context $context,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\StatsFactory $statsFactory
    ) {

        parent::__construct($context);

        $this->subscribersFactory = $subscribersFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->statsFactory = $statsFactory;
    }

    /**
     *
     */
    public function execute()
    {

        $camp = $this->getRequest()->getParam('c');
        $u = $this->getRequest()->getParam('u');
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->campaignsFactory->create()->load($camp);
        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadById($u);

        $this->statsFactory->create()->logViews($campaign, $subscriber);

        $im = imagecreatetruecolor(1, 1);
        imagefilledrectangle($im, 0, 0, 99, 99, 0xFFFFFF);
        header('Content-Type: image/gif');

        imagegif($im);
        imagedestroy($im);

        return;
    }
}
