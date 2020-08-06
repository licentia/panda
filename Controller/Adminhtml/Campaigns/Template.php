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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

use Magento\Backend\App\Action;

/**
 * Class Template
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Template extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     *
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Model\CampaignsFactory            $campaignsFactory
     * @param \Licentia\Panda\Model\SendersFactory              $sendersFactory
     * @param \Licentia\Panda\Model\TemplatesFactory            $templatesFactory
     * @param \Magento\Framework\Json\Helper\Data               $jsonHelper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $sendersFactory,
            $registry,
            $campaignsFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->templatesFactory = $templatesFactory;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     *
     */
    public function execute()
    {

        $templateCode = $this->getRequest()->getParam('code');

        $template = $this->templatesFactory->create()->load($templateCode);

        if (!$template->getId()) {
            return;
        }

        $template->setData(
            [
                $this->getRequest()->getParam('field', 'message') => $template->getMessage(),
            ]
        );

        $this->getResponse()
             ->representJson(
                 $this->jsonHelper->jsonEncode($template->getData())
             );
    }
}
