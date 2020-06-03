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
 * @modified   03/06/20, 16:19 GMT
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
