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

namespace Licentia\Panda\Model\Autoresponders;

/**
 * Class AbstractModel
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Magento\Framework\Model;

/**
 * Class AbstractModel
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class AbstractModel extends Model\AbstractModel
{

    /**
     * @var \Licentia\Panda\Model\ChainsFactory
     */
    protected \Licentia\Panda\Model\ChainsFactory $chainsFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection;

    /**
     * @var \Licentia\Panda\Model\ChainseditFactory
     */
    protected \Licentia\Panda\Model\ChainseditFactory $chainseditFactory;

    /**
     * @var \Magento\Backend\Block\Template
     */
    protected \Magento\Backend\Block\Template $template;

    /**
     * AbstractModel constructor.
     *
     * @param \Licentia\Panda\Model\ChainseditFactory                          $chainseditFactory
     * @param \Magento\Backend\Block\Template                                  $block
     * @param \Licentia\Panda\Model\ChainsFactory                              $chainsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection
     * @param Model\Context                                                    $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param Model\ResourceModel\AbstractResource|null                        $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null               $resourceCollection
     * @param array                                                            $data
     */
    public function __construct(
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        \Magento\Backend\Block\Template $block,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->chainsFactory = $chainsFactory;
        $this->chainseditFactory = $chainseditFactory;
        $this->chainseditCollection = $chainseditCollection;
        $this->template = $block;
    }

    /**
     * @param \Licentia\Panda\Model\Autoresponders $autoresponder
     * @param \Licentia\Panda\Model\Subscribers    $subscriber
     * @param \Licentia\Panda\Model\Events         $event
     * @param \Licentia\Panda\Model\Chains         $chain
     */
    public function run(
        \Licentia\Panda\Model\Autoresponders $autoresponder,
        \Licentia\Panda\Model\Subscribers $subscriber,
        \Licentia\Panda\Model\Events $event,
        \Licentia\Panda\Model\Chains $chain
    ) {
    }

    /**
     * @return bool
     */
    public function deleteChain()
    {

        $params = $this->getData('params');
        $id = $params['id'];

        $total = $this->chainseditCollection->create()
                                            ->addFieldToFilter('parent_id', ['in' => $id])
                                            ->getSize();

        if ($total > 1) {
            $this->chainseditFactory->create()
                                    ->getResource()
                                    ->getConnection()
                                    ->delete(
                                        $this->chainseditFactory->create()
                                                                ->getResource()
                                                                ->getTable('panda_autoresponders_chains_edit'),
                                        ['parent_id IN (?)' => $id]
                                    );
        } else {
            $chain = $this->chainseditFactory->create()->load($id);

            $this->chainseditFactory->create()
                                    ->getResource()
                                    ->getConnection()
                                    ->update(
                                        $this->chainseditFactory->create()
                                                                ->getResource()
                                                                ->getTable('panda_autoresponders_chains_edit'),
                                        ['parent_id' => $chain->getParentId()],
                                        ['parent_id =?' => $chain->getId()]
                                    );
        }

        $this->chainseditFactory->create()
                                ->getResource()
                                ->getConnection()
                                ->delete(
                                    $this->chainseditFactory->create()
                                                            ->getResource()
                                                            ->getTable('panda_autoresponders_chains_edit'),
                                    ['chain_id IN (?)' => $id]
                                );

        return true;
    }
}
