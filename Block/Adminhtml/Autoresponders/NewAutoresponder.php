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

namespace Licentia\Panda\Block\Adminhtml\Autoresponders;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Autoresponders
 */
class NewAutoresponder extends \Magento\Backend\Block\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * NewAutoresponder constructor.
     *
     * @param \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
     * @param \Magento\Backend\Block\Template\Context     $context
     * @param \Magento\Framework\Registry                 $registry
     * @param array                                       $data
     */
    public function __construct(
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->setTemplate('autoresponders/new.phtml');

        $this->autorespondersFactory = $autorespondersFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get edit form container header text
     *
     * @return array
     */
    public function getAutorespondersTriggersDetails()
    {

        $options = $this->autorespondersFactory->create()->getAutorespondersTriggersDetails();

        asort($options);

        return $options;
    }
}
