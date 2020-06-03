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
 * @modified   03/06/20, 16:01 GMT
 *
 */

namespace Licentia\Panda\Controller\Adminhtml\Exceptions;

/**
 * Class Index
 *
 * @package Licentia\Panda\Controller\Adminhtml\Exceptions
 */
class Index extends \Licentia\Panda\Controller\Adminhtml\Exceptions
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::exceptions');
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Exceptions'));
        $resultPage->addBreadcrumb(__('Sales Automation'), __('Sales Automation'));
        $resultPage->addBreadcrumb(__('Exceptions'), __('Exceptions'));

        return $resultPage;
    }
}
