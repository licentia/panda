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

namespace Licentia\Panda\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class BlockActions
 */
class CampaignsActions extends Column
{

    /**
     * Url path
     */
    const URL_PATH_EDIT = 'panda/campaigns/edit';

    const URL_PATH_DELETE = 'panda/campaigns/delete';

    const URL_PATH_SEND = 'panda/campaigns/send';

    const URL_PATH_DUPLICATE = 'panda/campaigns/duplicate';

    const URL_PATH_REPORTS = 'panda/reports/detail';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {

        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $items
     *
     * @return array
     */
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
                if (isset($item['campaign_id'])) {
                    $actions = [
                        'edit'      => [
                            'href'  => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['campaign_id'],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                        'duplicate' => [
                            'href'  => $this->urlBuilder->getUrl(
                                static::URL_PATH_DUPLICATE,
                                [
                                    'id' => $item['campaign_id'],
                                ]
                            ),
                            'label' => __('Duplicate'),
                        ],
                        'send'      => [
                            'href'    => $this->urlBuilder->getUrl(
                                static::URL_PATH_SEND,
                                [
                                    'id' => $item['campaign_id'],
                                ]
                            ),
                            'label'   => __('Send'),
                            'confirm' => [
                                'title'   => __('Send "${ $.$data.internal_name }"'),
                                'message' => __(
                                    'Are you sure you wan\'t to send the "${ $.$data.internal_name }" campaign?'
                                ),
                            ],
                        ],
                        'reports'   => [
                            'href'  => $this->urlBuilder->getUrl(
                                static::URL_PATH_REPORTS,
                                [
                                    'id' => $item['campaign_id'],
                                ]
                            ),
                            'label' => __('Reports'),

                        ],
                        'delete'    => [
                            'href'    => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['campaign_id'],
                                ]
                            ),
                            'label'   => __('Delete'),
                            'confirm' => [
                                'title'   => __('Delete "${ $.$data.internal_name }"'),
                                'message' => __(
                                    'Are you sure you wan\'t to delete the "${ $.$data.internal_name }" campaign?'
                                ),
                            ],
                        ],
                    ];

                    if ($item['status'] != 'finished' && $item['status'] != 'running') {
                        unset($actions['reports']);
                    }
                    if ($item['status'] != 'finished') {
                        unset($actions['duplicate']);
                    }
                    if ($item['status'] != 'standby' || $item['recurring'] != '0') {
                        unset($actions['send']);
                    }
                    if ($item['type'] != 'email') {
                        unset($actions['reports']);
                    }

                    $item[$this->getData('name')] = $actions;
                }
            }
        }

        return $dataSource;
    }
}
