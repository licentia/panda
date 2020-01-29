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

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Model\FollowupFactory
     */
    protected $followupFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $dateTimeFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */

    protected $dateFilter;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory   $dateFactory
     * @param \Licentia\Panda\Model\TagsFactory                    $tagsFactory
     * @param Action\Context                                       $context
     * @param \Licentia\Panda\Model\SendersFactory                 $sendersFactory
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Licentia\Panda\Model\CampaignsFactory               $campaignsFactory
     * @param \Licentia\Panda\Model\TemplatesFactory               $templatesFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfigInterface
     * @param \Licentia\Panda\Model\FollowupFactory                $followupFactory
     * @param \Licentia\Panda\Helper\Data                          $pandaHelper
     * @param \Magento\Framework\Stdlib\DateTime\Filter\DateTime   $dateTimeFilter
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date       $dateFilter
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory     $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        Action\Context $context,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Panda\Model\FollowupFactory $followupFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateTimeFilter,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $sendersFactory,
            $registry,
            $campaignsFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->tagsFactory = $tagsFactory;
        $this->timezone = $timezone;
        $this->followupFactory = $followupFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaHelper = $pandaHelper;
        $this->dateTimeFilter = $dateTimeFilter;
        $this->dateFilter = $dateFilter;
        $this->templatesFactory = $templatesFactory;
        $this->dateFactory = $dateFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('id');
            /** @var \Licentia\Panda\Model\Campaigns $model */
            $model = $this->registry->registry('panda_campaign');
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This campaign no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['parent_id'])) {
                unset($data['parent_id']);
            }
            try {
                try {
                    $this->timezone->formatDate($data['deploy_at']);
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in Deploy At'));
                }

                try {
                    $this->timezone->formatDate($data['recurring_first_run']);
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in Recurring'));
                }

                $inputFilter = new \Zend_Filter_Input(['deploy_at' => $this->dateTimeFilter], [], $data);
                $data = $inputFilter->getUnescaped();
                $inputFilter = new \Zend_Filter_Input(['recurring_first_run' => $this->dateFilter], [], $data);
                $data = $inputFilter->getUnescaped();

                if (isset($data['status']) && $data['status'] == 'no') {
                    $data['status'] = 'standby';
                }

                if (!$data['deploy_at']) {
                    $data['deploy_at'] = $data['recurring_first_run'];
                }

                if ($this->getRequest()->getParam('op') == 'send') {
                    $data['deploy_at'] = $this->dateFactory->create()->gmtDate();
                    $data['status'] = 'standby';
                }

                if (!isset($data['recurring_daily'])) {
                    $data['recurring_daily'] = range(0, 6);
                }
                $data['recurring_daily'] = implode(',', $data['recurring_daily']);

                if (!isset($data['store_id'])) {
                    $data['store_id'] = [0];
                }
                if (array_search(0, $data['store_id']) !== false) {
                    $data['store_id'] = [];
                }
                $data['store_id'] = implode(',', $data['store_id']);

                if (!isset($data['segments_ids'])) {
                    $data['segments_ids'] = [0];
                }
                if (array_search(0, $data['segments_ids']) !== false) {
                    $data['segments_ids'] = [];
                }
                $data['segments_ids'] = implode(',', $data['segments_ids']);
                $data['controller'] = true;

                if (isset($data['url']) && trim(strlen($data['url'])) > 0) {
                    if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Invalid URL'));
                    }
                    $content = file_get_contents($data['url']);
                    if ($content === false) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Cannot fetch URL content'));
                    }
                    unset($content);
                }

                if ($this->getRequest()->getParam('op') == 'duplicate') {
                    $sendDate = new \DateTime();
                    $sendDate->add(new \DateInterval('P7D'));

                    $model->setId(null);

                    $data['deploy_at'] = $sendDate->format('Y-m-d H:i:s');
                    $data['status'] = 'standby';
                    $data['clicks'] = 0;
                    $data['unique_clicks'] = 0;
                    $data['views'] = 0;
                    $data['unique_views'] = 0;
                    $data['unsent'] = 0;
                    $data['errors'] = 0;
                    $data['status'] = 'draft';
                    $data['total_messages'] = 0;
                    $data['sent'] = 0;
                    $data['bounces'] = 0;
                    $data['unsubscribes'] = 0;
                    unset($data['run_times_left']);
                    $data['conversions_number'] = 0;
                    $data['conversions_amount'] = 0;
                    $data['conversions_average'] = 0;
                }

                if ($model->getId()) {
                    if ($data['recurring'] != '0') {
                        $followupData['is_active'] = '0';
                        $followup = $this->followupFactory->create()->load($model->getId(), 'campaign_id');
                        if ($followup->getId()) {
                            $followup->setData($followupData)
                                     ->save();
                        }
                    }
                }

                if (!isset($data['deploy_at']) || strlen($data['deploy_at']) == 0) {
                    $data['deploy_at'] =
                        $data['recurring_first_run'] . ' ' . str_pad(
                            $data['recurring_time'],
                            2,
                            0,
                            STR_PAD_LEFT
                        ) . '00:00';
                }

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                if (!isset($data['tags'])) {
                    $data['tags'] = [];
                }

                $modelTags = $data['tags'];

                foreach ($data as $key => $datum) {
                    if (is_array($datum) && $key != 'conditions') {
                        unset($data[$key]);
                    }
                }

                $model->loadPost($data);
                $model->setData('controller_panda', true);

                $model->addData($data);

                if ($this->getRequest()->getParam('op') != 'duplicate') {
                    $model->setId($id);
                }

                $model->save();

                $this->tagsFactory->create()->updateTags('campaigns', $model, $modelTags);

                if ($model->getType() == 'email') {
                    $template = $this->templatesFactory->create()->load($model->getId(), 'campaign_id');

                    if (!$template->getCampaignId()) {
                        $template->setCampaignId($model->getId());
                    }

                    $template->setMessage($model->getMessage())
                             ->setStatus(1)
                             ->setName('[' . __('Campaign') . '] ' . $model->getInternalName())
                             ->save();
                }

                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('op') == 'send') {
                    return $resultRedirect->setPath('*/*/send', ['id' => $model->getId()]);
                }

                $extraMsg = '';
                if ($this->scopeConfig->getValue(
                    'panda_nuntius/info/warning',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                )) {
                    $totalRecipients = $model->calculateNumberRecipients();
                    if ($totalRecipients !== false) {
                        $extraMsg = ' ' . __(
                                'Predicted Campaign Recipients: %1.',
                                $totalRecipients
                            );

                        if ($totalRecipients == 0) {
                            $this->messageManager->addErrorMessage(
                                __('Please be advise: There are no recipients expected to receive this campaign.')
                            );
                            $extraMsg = '';
                        }
                    }
                }

                if ($this->getRequest()->getParam('op') == 'duplicate') {
                    $this->messageManager->addSuccessMessage(
                        __('The campaign has been duplicated. You are now working on the duplicated one.' . $extraMsg)
                    );

                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'tab_id' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                } else {
                    $this->messageManager->addSuccessMessage(__('The campaign has been saved.') . $extraMsg);
                }

                $this->_getSession()->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'tab_id' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e,
                    __('Something went wrong while saving the campaign. Check the error log for more information.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $model->getId(),
                    'tab_id' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
