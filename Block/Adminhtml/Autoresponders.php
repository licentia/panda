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
