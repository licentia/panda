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

namespace Licentia\Panda\Controller\Adminhtml\Account;

/**
 * Class Attributes
 *
 * @package Licentia\Panda\Controller\Adminhtml\Account
 */
class Attributes extends \Licentia\Panda\Controller\Adminhtml\Account
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $resource = $this->sendersFactory->create()->getResource();

            $resource->getConnection()
                     ->delete(
                         $resource->getTable('eav_attribute'),
                         [
                             'attribute_code IN(?)' =>
                                 [
                                     'panda_segments',
                                     'panda_age_prediction',
                                     'panda_gender_prediction',
                                     'panda_price_expression',
                                     'panda_prices_disabled',
                                 ],
                         ]
                     );

            $this->messageManager->addSuccessMessage(__('Attributes Removed. You can now uninstall the extension'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while removing attributes.')
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
