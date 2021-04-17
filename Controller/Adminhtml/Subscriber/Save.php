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

namespace Licentia\Panda\Controller\Adminhtml\Subscriber;

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Subscriber
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Subscriber
{

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected \Licentia\Panda\Model\TagsFactory $tagsFactory;

    /**
     * Save constructor.
     *
     * @param Action\Context                                      $context
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Magento\Framework\Registry                         $registry
     * @param \Licentia\Panda\Model\TagsFactory                   $tagsFactory
     * @param \Licentia\Panda\Model\ExtraFieldsFactory            $extraFieldsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory            $subscribersFactory
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory   $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory    $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory        $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $extraFieldsFactory,
            $subscribersFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->listSegmentsFactory = $listSegmentsFactory;
        $this->tagsFactory = $tagsFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {

            foreach ($data as $key => $value) {
                if (substr($key, 0, 6) == 'field_' && is_array($data[$key])) {
                    $data[$key] = array_filter($data[$key]);
                    $data[$key] = array_map('trim', $data[$key]);
                    $data[$key] = implode(",", $data[$key]);
                }
            }

            $id = $this->getRequest()->getParam('id');

            /** @var \Licentia\Panda\Model\Subscribers $model */
            $model = $this->registry->registry('panda_subscriber');
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This subscriber no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            unset($data['created_at']);

            try {
                $model->setData($data);
                $model->setId($id);

                $model->save();

                if (isset($data['segments']) && is_array($data['segments'])) {

                    $this->listSegmentsFactory->create()->clearSubscriberSegments($model->getId());
                    foreach ($data['segments'] as $segmentId) {
                        $this->listSegmentsFactory->create()
                                                  ->addRecordToSegment($model->getId(), $segmentId, 'subscriber_id');
                    }
                }

                if (!isset($data['tags'])) {
                    $data['tags'] = [];
                }
                $this->tagsFactory->create()->updateTags('subscribers', $model, $data['tags']);

                $this->messageManager->addSuccessMessage(__('You saved the subscriber.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'active_tab' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Subscriber. Check the error log for more information')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
