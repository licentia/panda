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

namespace Licentia\Panda\Block\Adminhtml\Autoresponders;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Autoresponders
 */
class NewAutoresponder extends \Magento\Backend\Block\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * NewAutoresponder constructor.
     *
     * @param \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
     * @param \Magento\Backend\Block\Template\Context     $context
     * @param \Magento\Framework\Registry                 $registry
     * @param array                                       $data
     */
    public function __construct(
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->setTemplate('autoresponders/new.phtml');

        $this->autorespondersFactory = $autorespondersFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get edit form container header text
     *
     * @return array
     */
    public function getAutorespondersTriggersDetails()
    {

        $options = $this->autorespondersFactory->create()->getAutorespondersTriggersDetails();

        asort($options);

        return $options;
    }
}
