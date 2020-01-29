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
    protected $listSegmentsFactory;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

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
                            'tab_id' => $this->getRequest()->getParam('active_tab'),
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
