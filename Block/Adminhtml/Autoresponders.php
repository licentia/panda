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

namespace Licentia\Panda\Block\Adminhtml;

/**
 * Class Autoresponders
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Autoresponders extends \Magento\Backend\Block\Template
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory
     */
    protected $templatesCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected $sendersCollection;

    /**
     * Autoresponders constructor.
     *
     * @param \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory   $sendersCollection
     * @param \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param array                                                           $data
     */
    public function __construct(
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->templatesCollection = $templatesCollection;
        $this->sendersCollection = $sendersCollection;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {

        $templates = $this->templatesCollection->create()
                                               ->addFieldToFilter('is_active', 1)
                                               ->addFieldToFilter('parent_id', ['null' => true]);

        $senders = $this->sendersCollection->create()->getSenders('email');

        if (count($templates) == 0 || count($senders) == 0) {

            return '<script type="text/javascript">var el = document.querySelector("div.page-main-actions");el.parentNode.removeChild(el);</script>';
        }
    }

}
