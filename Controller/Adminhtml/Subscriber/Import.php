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

namespace Licentia\Panda\Controller\Adminhtml\Subscriber;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Import
 *
 * @package Licentia\Panda\Controller\Adminhtml\Subscriber
 */
class Import extends \Licentia\Panda\Controller\Adminhtml\Subscriber
{

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     *
     * @param \Magento\Backend\App\Action\Context                                                            $context
     * @param \Magento\Framework\View\Result\PageFactory                                                     $resultPageFactory
     * @param \Magento\Framework\Registry                                                                    $registry
     * @param \Licentia\Panda\Model\ExtraFieldsFactory                                                       $extraFieldsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory                                                       $subscribersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory|\Magento\Framework\Controller\Result\Forward $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory                                               $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory                                                   $resultLayoutFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory                                               $uploaderFactory
     * @param \Magento\Framework\Filesystem                                                                  $filesystem
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem
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

        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
    }

    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultPage = $this->_initAction();

        $model = $this->subscribersFactory->create();

        if ($this->getRequest()
                 ->isPost()) {
            try {
                $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);

                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->uploaderFactory->create(['fileId' => 'filename']);
                $uploader->setAllowedExtensions(['csv']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setAllowCreateFolders(true);

                if (!$uploader->save($mediaDir->getAbsolutePath())) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Cannot upload file.'));
                }

                $result = $mediaDir->getAbsolutePath() . $uploader->getUploadedFileName();
                $data = $this->getRequest()->getPost();

                $count = $model->import($result, $data);

                $this->messageManager->addSuccessMessage(
                    __(
                        'A total of %1 where added and %2 where updated',
                        $count['added'],
                        $count['updated']
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultRedirect->setPath('*/*/');
        }

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Subscriber\Import')
        );

        return $resultPage;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {

        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::subscribers')
                   ->addBreadcrumb(__('Subscribers'), __('Subscribers'))
                   ->addBreadcrumb(__('Manage Subscribers'), __('Manage Subscribers'));

        return $resultPage;
    }
}
