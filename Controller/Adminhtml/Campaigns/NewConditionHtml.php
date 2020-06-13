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
 * Class NewConditionHtml
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class NewConditionHtml extends \Licentia\Panda\Controller\Adminhtml\Campaigns
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
