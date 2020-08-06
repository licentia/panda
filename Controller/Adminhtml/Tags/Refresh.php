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

namespace Licentia\Panda\Controller\Adminhtml\Tags;

/**
 * Class Refresh
 *
 * @package Licentia\Panda\Controller\Adminhtml\Tags
 */
class Refresh extends \Licentia\Panda\Controller\Adminhtml\Tags
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        $this->tagsFactory->create()->updateTagsCurrentValue();

        $this->messageManager->addSuccessMessage(__('Tags current values Updated'));

        return $resultRedirect->setPath('*/*/');
    }
}
