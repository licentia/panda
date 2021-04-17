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

namespace Licentia\Panda\Block\Adminhtml\Autoresponders\Edit\Tab;

use Licentia\Panda\Model\ResourceModel\Chainsedit;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\Autoresponders\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var string
     */
    public string $html = '';

    /**
     * @var Chainsedit\CollectionFactory
     */
    protected Chainsedit\CollectionFactory $chainseditCollection;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $autorespondersFactory;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param Chainsedit\CollectionFactory            $chainseditCollection
     * @param array                                   $data
     */
    public function __construct(
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        array $data = []
    ) {

        $this->autorespondersFactory = $autorespondersFactory;
        $this->chainseditCollection = $chainseditCollection;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setTemplate('autoresponders/tree.phtml');
    }

    /**
     *
     */
    public function getAutorespondersActions()
    {

        return $this->autorespondersFactory->create()->getActionsList();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->getAutoresponder();

        $chains = $this->chainseditCollection->create()
                                             ->addFieldToFilter('autoresponder_id', $current->getId());

        $store_all_id = [];
        $store_all_id[] = 0;

        array_unique($store_all_id);
        foreach ($chains as $chain) {
            array_push($store_all_id, $chain->getId());
        }

        $this->html .= "<div class='overflow'><ul class='tree'>";
        $this->in_parent(0, $store_all_id);
        $this->html .= "</div>";

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getAutoresponder()
    {

        return $this->_coreRegistry->registry('panda_autoresponder');
    }

    /**
     * @param $in_parent
     * @param $store_all_id
     */
    public function in_parent($in_parent, &$store_all_id)
    {

        if (in_array($in_parent, $store_all_id)) {
            unset($chains);

            $current = $this->getAutoresponder();

            $chains = $this->chainseditCollection->create()
                                                 ->addFieldToFilter('parent_id', $in_parent)
                                                 ->addFieldToFilter('autoresponder_id', $current->getId());

            $this->html .= "<ul>" . PHP_EOL;

            foreach ($chains as $chain) {
                $droppable = $chain->getData('main_condition') == 1 ? '' : ' div_droppable  ';
                $editable = $chain->getData('editable') == 1 ? 'editable' : '';
                $main = $chain->getData('parent_id') == 0 ? 'main_parent' : '';

                $this->html .= "    <li class='{$chain->getData('event')}'>" . PHP_EOL .
                               "<div class=' $droppable $editable $main ' id=" .
                               $chain->getData('chain_id') . "><span class='name'>" .
                               $chain->getData('name') . "</span></div>" . PHP_EOL;
                $this->in_parent($chain->getId(), $store_all_id);
                $this->html .= "    </li>" . PHP_EOL;
            }
        }
        $this->html .= "</ul>" . PHP_EOL;
    }

    /**
     * @return string
     */
    public function getTree()
    {

        return $this->html;
    }
}
