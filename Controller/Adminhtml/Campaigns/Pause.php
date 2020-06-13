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
 * Class Pause
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Pause extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->registry->registry('panda_campaign');

        $status = $this->getRequest()->getParam('op') == 'resume' ? 'standby' : 'paused';

        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $campaign->setData('status', $status)
                         ->save();

                $this->messageManager->addSuccessMessage(__('The campaign has been %1.', $status));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while %1 the campaign.', $status)
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Unable to find a campaign to %1.', $status));
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}
