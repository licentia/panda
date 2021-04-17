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

namespace Licentia\Panda\Ui\Component\Listing\Column\Splits;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Campaigns
 *
 * @package Licentia\Panda\Ui\Component\Listing\Column\Splits
 */
class Campaigns extends Column
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected \Magento\Framework\UrlInterface $urlInterface;

    /**
     * @var \Licentia\Panda\Model\SplitsFactory
     */
    protected \Licentia\Panda\Model\SplitsFactory $splitsFactory;

    /**
     * Campaigns constructor.
     *
     * @param \Licentia\Panda\Model\SplitsFactory $splitsFactory
     * @param \Magento\Framework\UrlInterface     $urlInterface
     * @param ContextInterface                    $context
     * @param UiComponentFactory                  $uiComponentFactory
     * @param array                               $components
     * @param array                               $data
     */
    public function __construct(
        \Licentia\Panda\Model\SplitsFactory $splitsFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {

        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->splitsFactory = $splitsFactory;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $return = false;

                if ((int) $item['sent'] == 1) {
                    $campaigns = $this->splitsFactory->create()->getTestCampaigns($item['split_id']);

                    if ($campaigns) {
                        $return = true;
                        /** @var \Licentia\Panda\Model\Campaigns $campaign */
                        foreach ($campaigns as $campaign) {
                            $url = $this->urlInterface->getUrl(
                                'panda/campaigns/edit',
                                ['id' => $campaign->getData('campaign_id')]
                            );
                            $item[$this->getData('name')][$campaign->getId()] = [
                                'href'   => $url,
                                'label'  => __('Test') . ' [' . ucfirst($campaign->getData('split_version')) . ']',
                                'hidden' => false,
                            ];
                        }
                    }
                }
                if ((int) $item['closed'] == 1) {
                    /** @var \Licentia\Panda\Model\Campaigns $campaign */
                    $campaign = $this->splitsFactory->create()->getFinalCampaign($item['split_id'], 'campaign_id');

                    if ($campaign) {
                        $return = true;
                        $url = $this->urlInterface->getUrl(
                            'panda/campaigns/edit',
                            ['id' => $campaign->getData('campaign_id')]
                        );
                        $item[$this->getData('name')]['final'] = [
                            'href'   => $url,
                            'label'  => __('Campaign'),
                            'hidden' => false,
                        ];
                    }
                }

                if (!$return) {
                    $item[$this->getData('name')]['a'] = 'N/A';
                }
            }
        }

        return $dataSource;
    }
}
