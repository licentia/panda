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

namespace Licentia\Panda\Controller\Adminhtml\Goals;

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Goals
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Goals
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Save constructor.
     *
     * @param Action\Context                                       $context
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date       $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\Registry                          $registry
     * @param \Licentia\Panda\Helper\Data                          $pandaHelper
     * @param \Licentia\Panda\Model\GoalsFactory                   $goalsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\GoalsFactory $goalsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $goalsFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->dateFilter = $dateFilter;
        $this->localeDate = $timezoneInterface;
    }

    /**
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('id');

            $model = $this->registry->registry('panda_goal');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Goal no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            try {
                $model->setData($data);
                if ($id) {
                    $model->setId($id);
                }

                $date = $this->localeDate->date()->format('m/d/Y');

                if (!$id && $data['from_date'] < $date) {
                    $data['from_date'] = $date;
                    $model->setData('from_date', $date);
                    $this->messageManager->addNoticeMessage(
                        __('The start date cannot be earlier than today. Date changed')
                    );
                }

                $model->setData('controller', 1);
                $model->save();

                $this->_getSession()->setFormData(false);
                $this->messageManager->addSuccessMessage(__('You saved the Goal.'));

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
                    __('Something went wrong while saving the goal. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $model->getId(),
                    'tab_id' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

}
