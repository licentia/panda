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

namespace Licentia\Panda\Model\Source;

/**
 * Class PopupsIdentifiers
 *
 * @package Licentia\Panda\Model\Source
 */
class PopupsIdentifiers implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\PopupsFactory
     */
    protected $popupsResource;

    /**
     * Popups constructor.
     *
     * @param \Licentia\Panda\Model\ResourceModel\PopupsFactory $popupsResource
     */
    public function __construct(
        \Licentia\Panda\Model\ResourceModel\PopupsFactory $popupsResource
    ) {

        $this->popupsResource = $popupsResource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $resource = $this->popupsResource->create();

        $connection = $resource->getConnection();

        $result = $connection->fetchCol(
            $connection->select()
                       ->from(
                           $resource->getMainTable(),
                           ['DISTINCT(identifier)']
                       )
                       ->order('identifier asc')
        );

        $return = [];

        foreach ($result as $item) {
            $return[] = ['value' => $item, 'label' => $item];
        }

        return $return;
    }
}
