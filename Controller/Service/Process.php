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

namespace Licentia\Panda\Controller\Service;

/**
 * Class Get
 *
 * @package Licentia\Panda\Controller
 */
class Process extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected $autoresponders;

    /**
     * @var \Licentia\Equity\Model\MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Process constructor.
     *
     * @param \Licentia\Equity\Model\MetadataFactory           $metadataFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Licentia\Panda\Model\AutorespondersFactory      $autorespondersFactory
     * @param \Magento\Framework\App\Action\Context            $context
     */
    public function __construct(
        \Licentia\Equity\Model\MetadataFactory $metadataFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Framework\App\Action\Context $context
    ) {

        parent::__construct($context);

        $this->autoresponders = $autorespondersFactory;
        $this->metadataFactory = $metadataFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $params = $this->getRequest()->getParams();

        if (isset($params['utm_campaign']) ||
            isset($params['utm_source']) ||
            isset($params['utm_medium']) ||
            isset($params['utm_term']) ||
            isset($params['utm_content'])) {

            $this->autoresponders->create()->utmCampaign($params);
        }

        if (isset($params['m']) && $params['m'] == 'catalog' &&
            isset($params['c']) && $params['c'] == 'product' &&
            isset($params['a']) && $params['a'] == 'view' &&
            isset($params['id'])) {
            $this->metadataFactory->create()->productRelated($params['id']);
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData($this->getRequest()->getParams());
    }
}
