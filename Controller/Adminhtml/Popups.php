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

use Magento\Backend\App\Action;

/**
 * Popups controller
 */
class Popups extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Panda::popups';

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
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected $popupsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * Templates constructor.
     *
     * @param Action\Context                                       $context
     * @param \Licentia\Panda\Model\PopupsFactory                  $popupsFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date       $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Licentia\Panda\Model\PopupsFactory $popupsFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->dateFilter = $dateFilter;

        $this->popupsFactory = $popupsFactory;
        $this->timezoneInterface = $timezoneInterface;

        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {

        $model = $this->popupsFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        if (!$model->getType()) {
            $type = $this->getRequest()->getParam('type', 'floating');
            $model->setType($type);
        }

        $types = \Licentia\Panda\Model\Popups::POPUP_TYPES;

        if (isset($types[$model->getType()])) {
            $model->setTypeName($types[$model->getType()]);
        }

        if ($data = $this->_getSession()->getFormData(true)) {
            $model->addData($data);
        }
        $this->registry->register('panda_popup', $model, true);
    }

}
