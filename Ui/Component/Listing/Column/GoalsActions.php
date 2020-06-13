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

namespace Licentia\Panda\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class BlockActions
 */
class GoalsActions extends Column
{

    /**
     * Url path
     */
    const URL_PATH_EDIT = 'panda/goals/edit';

    const URL_PATH_DELETE = 'panda/goals/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Constructor
     *
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param ContextInterface                          $context
     * @param UiComponentFactory                        $uiComponentFactory
     * @param UrlInterface                              $urlBuilder
     * @param array                                     $components
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {

        $this->authorization = $authorization;
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
                if (isset($item['goal_id'])) {
                    $item[$this->getData('name')] = [
                        'edit'   => [
                            'href'  => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['goal_id'],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href'    => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['goal_id'],
                                ]
                            ),
                            'label'   => __('Delete'),
                            'confirm' => [
                                'title'   => __('Delete "${ $.$data.name }"'),
                                'message' => __(
                                    'Are you sure you wan\'t to delete the "${ $.$data.name }" goal?'
                                ),
                            ],
                        ],
                    ];

                    if (!$this->authorization->isAllowed('Licentia_Panda::goals_save')) {
                        unset($item[$this->getData('name')]['edit']);
                    }
                    if (!$this->authorization->isAllowed('Licentia_Panda::goals_delete')) {
                        unset($item[$this->getData('name')]['delete']);
                    }
                }
            }
        }

        return $dataSource;
    }
}
