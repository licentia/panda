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

namespace Licentia\Panda\Controller\Adminhtml\Popups;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Popups
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Popups
{

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected \Magento\Framework\Image\AdapterFactory $imageAdapter;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected \Magento\Framework\Filesystem $filesystem;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface $cacheState
     */
    protected \Magento\Framework\App\Cache\StateInterface $cacheState;

    /**
     * Save constructor.
     *
     * @param \Magento\Framework\App\Cache\StateInterface          $cacheState
     * @param \Magento\Framework\App\Cache\TypeListInterface       $typeList
     * @param \Magento\Framework\Image\AdapterFactory              $adapterFactory
     * @param \Magento\Framework\Filesystem                        $filesystem
     * @param \Magento\Backend\App\Action\Context                  $context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory     $uploader
     * @param \Licentia\Panda\Model\PopupsFactory                  $popupsFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date       $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     */
    public function __construct(
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Licentia\Panda\Model\PopupsFactory $popupsFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $popupsFactory,
            $dateFilter,
            $timezoneInterface,
            $resultPageFactory,
            $registry,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->cacheState = $cacheState;
        $this->uploaderFactory = $uploader;
        $this->filesystem = $filesystem;
        $this->imageAdapter = $adapterFactory;
        $this->cacheTypeList = $typeList;
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

        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('id');

            /** @var \Licentia\Panda\Model\Popups $model */
            $model = $this->registry->registry('panda_popup');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This %1 no longer exists.', $model->getTypeName()));

                return $resultRedirect->setPath('*/*/');
            }

            if (array_search(0, $data['segments_ids']) !== false) {
                $data['segments_ids'] = [];
            }
            $data['segments_ids'] = implode(',', $data['segments_ids']);

            if (!isset($data['store_id'])) {
                $data['store_id'] = [0];
            }
            if (array_search(0, $data['store_id']) !== false) {
                $data['store_id'] = [];
            }
            $data['store_id'] = implode(',', $data['store_id']);

            if (isset($data['blocks_ids']) && is_array($data['blocks_ids'])) {
                $data['blocks_ids'] = implode(',', $data['blocks_ids']);
            }

            try {
                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'active_tab' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                $imgs = ['background_image'];

                foreach ($imgs as $img) {
                    $imageRequest = $this->getRequest()->getFiles($img);
                    if ($imageRequest) {
                        if (isset($imageRequest['name'])) {
                            $fileName = $imageRequest['name'];
                        } else {
                            $fileName = '';
                        }
                    } else {
                        $fileName = '';
                    }

                    if ($imageRequest && strlen($fileName)) {
                        /*
                         * Save image upload
                         */
                        try {
                            $uploader = $this->uploaderFactory->create(['fileId' => $img]);
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                            $imageAdapter = $this->imageAdapter->create();

                            $uploader->addValidateCallback('banner_image', $imageAdapter, 'validateUploadFile');
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(true);

                            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                            $result = $uploader->save(
                                $mediaDirectory->getAbsolutePath(\Licentia\Panda\Model\Popups::BASE_MEDIA_PATH)
                            );
                            $data[$img] = \Licentia\Panda\Model\Popups::BASE_MEDIA_PATH . $result['file'];
                        } catch (\Exception $e) {
                            if ($e->getCode() == 0) {
                                $this->messageManager->addErrorMessage($e->getMessage());
                            }
                        }
                    } else {
                        if (isset($data[$img]) && isset($data[$img]['value'])) {
                            if (isset($data[$img]['delete'])) {
                                $data[$img] = null;
                                $data['delete_image'] = true;
                            } elseif (isset($data[$img]['value'])) {
                                $data[$img] = $data[$img]['value'];
                            } else {
                                $data[$img] = null;
                            }
                        }
                    }
                }

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }
                $model->loadPost($data);
                $model->setData('controller_panda', true);

                $model->addData($data);

                $model->save();

                if ($this->cacheState->isEnabled('full_page')) {
                    $this->cacheTypeList->invalidate('full_page');
                }

                $this->messageManager->addSuccessMessage(__('You saved the %1.', $model->getTypeName()));
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
                    __('Something went wrong while saving the %1. Check the error log for more information.',
                        $model->getTypeName())
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(),]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
