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

namespace Licentia\Panda\Ui\Component\MassAction;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\System\Store as SystemStore;
use Zend\Stdlib\JsonSerializable;

/**
 * Class Stores
 *
 * @package Licentia\Panda\Ui\Component\MassAction\Group
 */
class Stores implements JsonSerializable
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $currentOptions = [];

    /**
     * @var
     */
    protected $paramName;

    /**
     * @var array
     */
    protected $additionalData = [];

    /**
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var
     */
    protected $urlPath;

    /**
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Options constructor.
     *
     * @param SystemStore  $systemStore
     * @param Escaper      $escaper
     * @param UrlInterface $urlBuilder
     * @param array        $data
     *
     */
    public function __construct(
        SystemStore $systemStore,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        array $data = []
    ) {

        $this->escaper = $escaper;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->systemStore = $systemStore;
    }

    protected function prepareData()
    {

        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {

        static $i = 0;

        if ($this->options === null) {
            $options = $this->generateStores();
            $this->prepareData();
            foreach ($options as $storeId => $StoreName) {
                $storeId .= '-' . $i;

                $this->options[$storeId] = [
                    'type'  => $storeId,
                    'label' => $StoreName,
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$storeId]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $storeId]
                    );
                }

                $this->options[$storeId] = array_merge_recursive(
                    $this->options[$storeId],
                    $this->additionalData
                );
            }

            $this->options = array_values($this->options);
        }
        $i++;

        return $this->options;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        if ($this->options !== null) {
            return $this->options;
        }

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }

    /**
     * Generate current options
     *
     */
    protected function generateStores()
    {

        $stores = [];

        $websiteCollection = $this->systemStore->getWebsiteCollection();
        $groupCollection = $this->systemStore->getGroupCollection();
        $storeCollection = $this->systemStore->getStoreCollection();
        /** @var \Magento\Store\Model\Website $website */
        foreach ($websiteCollection as $website) {
            /** @var \Magento\Store\Model\Group $group */
            foreach ($groupCollection as $group) {
                if ($group->getWebsiteId() == $website->getId()) {
                    $stores = [];
                    /** @var  \Magento\Store\Model\Store $store */
                    foreach ($storeCollection as $store) {
                        $name = $this->escaper->escapeHtml(
                            $website->getName() . ' / ' . $group->getName() . ' / ' . $store->getName()
                        );
                        $stores[$store->getId()] = $name;
                    }
                }
            }
        }

        return $stores;
    }
}
