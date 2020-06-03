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
 * @modified   03/06/20, 16:19 GMT
 *
 */

namespace Licentia\Panda\Controller\Adminhtml;

use Licentia\Panda\Helper\Debug;
use Magento\Backend\App\Action;

/**
 * Newsletter subscribers controller
 */
class Support extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Panda::support';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Model\ServiceFactory
     */
    protected $serviceFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var Debug
     */
    protected $debugHelper;

    /**
     * @param Action\Context                                     $context
     * @param Debug                                              $debugHelper
     * @param \Magento\Framework\Encryption\EncryptorInterface   $encryptor
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Licentia\Panda\Model\ServiceFactory               $serviceFactory
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\Registry                        $registry
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory       $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        Debug $debugHelper,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Licentia\Panda\Model\ServiceFactory $serviceFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->debugHelper = $debugHelper;
        $this->encryptor = $encryptor;
        $this->serviceFactory = $serviceFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->pandaHelper = $pandaHelper;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
    }

}
