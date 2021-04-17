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
 * Newsletter subscribers controller
 */
class Templates extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Panda::templates';

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
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templateFactory;

    /**
     * @var \Magento\Newsletter\Model\Template\Filter
     */
    protected $filter;

    /**
     * @var \Licentia\Panda\Model\Service
     */
    protected $service;

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected $sendersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Newsletter\Model\TemplateFactory
     */
    protected $newsletterTemplateFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesGlobalFactory
     */
    protected $templatesGlobalFactory;

    /**
     * Templates constructor.
     *
     * @param Action\Context                                    $context
     * @param \Magento\Newsletter\Model\TemplateFactory         $newsletterTemplateFactory
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Licentia\Panda\Model\SendersFactory              $sendersFactory
     * @param \Licentia\Panda\Model\TemplatesGlobalFactory      $templatesGlobalFactory
     * @param \Magento\Newsletter\Model\Template\Filter         $filter
     * @param \Licentia\Panda\Model\Service                     $service
     * @param \Licentia\Panda\Helper\Data                       $helperData
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Model\TemplatesFactory            $templatesFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Newsletter\Model\TemplateFactory $newsletterTemplateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory,
        \Magento\Newsletter\Model\Template\Filter $filter,
        \Licentia\Panda\Model\Service $service,
        \Licentia\Panda\Helper\Data $helperData,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->sendersFactory = $sendersFactory;
        $this->service = $service;
        $this->filter = $filter;
        $this->pandaHelper = $helperData;

        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->templateFactory = $templatesFactory;
        $this->templatesGlobalFactory = $templatesGlobalFactory;

        $this->storeManager = $storeManager;
        $this->newsletterTemplateFactory = $newsletterTemplateFactory;

        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {

        $model = $this->templateFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        if ($data = $this->_getSession()
                         ->getFormData(true)) {
            $model->addData($data);
        }
        $this->registry->register('panda_template', $model, true);
    }

}
