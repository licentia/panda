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
 * Class NewAction
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class NewAction extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {

        $type = $this->getRequest()->getParam('ctype');

        $count = count($this->sendersFactory->create()->getSenders($type));

        if ($count == 0) {
            $this->messageManager->addWarningMessage(
                __("You haven't added any %1 Sender. You won't be able to create campaigns without %1 Senders", $type)
            );

            return $this->_redirect('*/senders');
        }

        parent::execute();
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
