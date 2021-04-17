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

namespace Licentia\Panda\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Senders controller
 */
class Senders extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Panda::senders';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected ?\Magento\Framework\Registry $registry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected \Magento\Framework\View\Result\LayoutFactory $layoutFactory;

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected \Licentia\Panda\Model\SendersFactory $sendersFactory;

    /**
     * @var \Licentia\Panda\Model\ServiceFactory
     */
    protected \Licentia\Panda\Model\ServiceFactory $serviceFactory;

    /**
     * Senders constructor.
     *
     * @param Action\Context                                    $context
     * @param \Licentia\Panda\Model\ServiceFactory              $serviceFactory
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Model\SendersFactory              $sendersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Licentia\Panda\Model\ServiceFactory $serviceFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct($context);
        $this->serviceFactory = $serviceFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->sendersFactory = $sendersFactory;
    }

    /**
     *
     */
    public function execute()
    {

        /** @var \Licentia\Panda\Model\Senders $model */
        $model = $this->sendersFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        if (!$model->getType()) {
            $type = $this->getRequest()->getParam('ctype', 'email');
            $model->setType($type);
        }

        if ($model->getType()) {
            $this->_request->setParams(['ctype' => $model->getType()]);
        }

        $this->registry->register('panda_sender', $model, true);
    }

}
