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

namespace Licentia\Panda\Controller\Adminhtml\Popups;

/**
 * Class NewConditionHtml
 *
 * @package Licentia\Panda\Controller\Adminhtml\Popups
 */
class NewConditionHtml extends \Licentia\Panda\Controller\Adminhtml\Popups
{

    /**
     * @return void
     */
    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $typeArr = explode(
            '|',
            str_replace(
                '-',
                '/',
                $this->getRequest()->getParam('type')
            )
        );
        $type = $typeArr[0];

        $model = $this->_objectManager->create($type)
                                      ->setId($id)
                                      ->setType($type)
                                      ->setRule($this->_objectManager->create('Magento\SalesRule\Model\Rule'))
                                      ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject(
                $this->getRequest()->getParam('form')
            );
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
