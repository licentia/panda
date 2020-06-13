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

namespace Licentia\Panda\Controller\Adminhtml\Events;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 *
 * @package Licentia\Panda\Controller\Adminhtml\Events
 */
class MassDelete extends \Licentia\Panda\Controller\Adminhtml\Events
{

    /**
     * @var \Licentia\Panda\Model\EventsFactory
     */
    protected $eventsFactory;

    /**
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Model\AutorespondersFactory       $autorespondersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     * @param \Licentia\Panda\Model\EventsFactory               $eventsFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Licentia\Panda\Model\EventsFactory $eventsFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $autorespondersFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->eventsFactory = $eventsFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        $resultRedirect = $this->resultRedirectFactory->create();
        $subscribersIds = $this->getRequest()->getParam('events');

        if (!is_array($subscribersIds)) {
            $this->messageManager->addErrorMessage(__('Please select one or more events.'));
        } else {
            try {
                foreach ($subscribersIds as $record) {
                    $delete = $this->eventsFactory->create()->load($record);

                    $id = $delete->getAutoresponderId();
                    if ($delete->getExecuted() == 1) {
                        $ar = $this->autorespondersFactory->create()->load($id);
                        $ar->setData('total_messages', $ar->getData('total_messages') - 1)
                           ->save();
                    }
                    $delete->delete();
                }
                $this->messageManager->addSuccessMessage(
                    __(
                        'Total of %1 record(s) were deleted.',
                        count($subscribersIds)
                    )
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting the events.')
                );
            }
        }

        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }
}
