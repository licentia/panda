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

namespace Licentia\Panda\Controller\Autoresponders;

/**
 * Class Get
 *
 * @package Licentia\Panda\Controller
 */
class Get extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected $autoresponders;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Get constructor.
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Licentia\Panda\Model\AutorespondersFactory      $autorespondersFactory
     * @param \Magento\Framework\App\Action\Context            $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Framework\App\Action\Context $context
    ) {

        parent::__construct($context);

        $this->autoresponders = $autorespondersFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $this->autoresponders->create()->utmCampaign($this->getRequest()->getParams());
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData([]);
    }
}
