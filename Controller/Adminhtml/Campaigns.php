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

namespace Licentia\Panda\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Newsletter subscribers controller
 */
class Campaigns extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Panda::campaigns';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected $sendersFactory;

    /**
     * Campaigns constructor.
     *
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Licentia\Panda\Model\SendersFactory              $sendersFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Model\CampaignsFactory            $campaignsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->sendersFactory = $sendersFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->fileFactory = $fileFactory;
        $this->layoutFactory = $resultLayoutFactory;
        $this->campaignsFactory = $campaignsFactory;
        parent::__construct($context);

        if (!$this->getRequest()->getParam('ctype') ||
            !in_array($this->getRequest()->getParam('ctype'), \Licentia\Panda\Model\Campaigns::CAMPAIGN_TYPES)
        ) {
            $this->getRequest()->setParams(['ctype' => \Licentia\Panda\Model\Campaigns::DEFAULT_CAMPAIGN_TYPE]);
        }
    }

    /**
     *
     */
    public function execute()
    {

        /** @var \Licentia\Panda\Model\Campaigns $model */
        $model = $this->campaignsFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
            $this->getRequest()->setParams(['parent_id' => $id, 'campaign_id' => $id]);
        }

        if ($data = $this->_getSession()->getFormData(true)) {
            $model->addData($data);
        }

        if ($id) {
            $model->setId($id);
        }

        if ($model->getType()) {
            $this->_request->setParams(['ctype' => $model->getType()]);
        }

        $this->registry->register('panda_campaign', $model, true);
    }

}
