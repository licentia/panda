<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
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
