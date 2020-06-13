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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

/**
 * Class Send
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Send extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * Send constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Magento\Backend\App\Action\Context                $context
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Licentia\Panda\Model\SendersFactory               $sendersFactory
     * @param \Magento\Framework\Registry                        $registry
     * @param \Licentia\Panda\Model\CampaignsFactory             $campaignsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory   $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory       $resultLayoutFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->dateFactory = $dateFactory;
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
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    function execute()
    {

        parent::execute();
        $model = $this->registry->registry('panda_campaign');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model->setData('deploy_at', $this->dateFactory->create()->gmtDate())
                      ->setData('status', 'queuing')
                      ->save();

                $this->messageManager->addSuccessMessage(__('Campaign deployment initiated...'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deploying the campaign.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Unable to find a campaign to send.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
