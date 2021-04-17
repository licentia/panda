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

namespace Licentia\Panda\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Popup
 *
 * @package Licentia\Panda\Block
 */
class Popup extends Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected \Magento\Customer\Model\Session $session;

    /**
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected \Licentia\Panda\Model\PopupsFactory $popupsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected \Magento\Checkout\Model\Session $checkoutSession;

    /**
     * Popup constructor.
     *
     * @param \Magento\Checkout\Model\Session     $checkoutSession
     * @param Template\Context                    $context
     * @param \Licentia\Panda\Helper\Data         $helper
     * @param \Licentia\Panda\Model\PopupsFactory $popupsFactory
     * @param \Magento\Customer\Model\Session     $session
     * @param array                               $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        Template\Context $context,
        \Licentia\Panda\Helper\Data $helper,
        \Licentia\Panda\Model\PopupsFactory $popupsFactory,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->checkoutSession = $checkoutSession;
        $this->pandaHelper = $helper;
        $this->session = $session;
        $this->popupsFactory = $popupsFactory;
    }

    /**
     * @return mixed
     */
    public function getMediaDir()
    {

        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {

        if ($this->pandaHelper->isCacheEnabled() && !$this->getRequest()->getParam('identifier')) {
            $this->setTemplate('popup.phtml');
        } else {
            $this->setTemplate('windows.phtml');
        }

        return parent::_toHtml();
    }

    /**
     * @return bool
     */
    public function hasActivePopups()
    {

        return $this->popupsFactory->create()
                                   ->hasActivePopups(
                                       $this->getRequest()->getServer('HTTP_USER_AGENT'),
                                       $this->_storeManager->getStore()->getId()
                                   );
    }

    /**
     * @return array|bool|\Licentia\Panda\model\Popups
     */
    public function getPopup()
    {

        $params = json_decode($this->getRequest()->getParam('params'), true);

        if (($this->getRequest()->getParam('panda_emulated_popup') ||
             isset($params['e']) && (int) $params['e'] > 0) &&
            isset($_COOKIE['panda_emulated_popup'])) {

            $id = $this->getRequest()->getParam('panda_emulated_popup') ?? $params['e'];

            return $this->popupsFactory->create()->getPopupToEmulate($id);
        }

        $session = $this->session;
        $popups = [];

        try {
            if (!$this->pandaHelper->isCacheEnabled()) {
                $params = [
                    'c' => $this->getRequest()
                                ->getControllerName(),
                    'e' => $this->getRequest()
                                ->getParam('panda_emulated_popup', 0),
                    'a' => $this->getRequest()
                                ->getActionName(),
                    'm' => $this->getRequest()
                                ->getModuleName(),
                    'i' => $this->getRequest()
                                ->getParam('id', 0),
                ];
                $params = json_encode($params);

                $this->getRequest()->setParam('params', $params);
                $this->getRequest()->setParam('identifier', $this->getRequest()->getServer('REQUEST_URI'));
                $this->getRequest()->setParam('referer', $this->getRequest()->getServer('HTTP_REFERER'));
            }

            $session->setData('panda_popups_everywhere', $session->getData('panda_popups_everywhere') + 1);

            $this->pandaHelper->registerCurrentScope();

            /** @var \Licentia\Panda\model\Popups $popups */
            $popups = $this->popupsFactory->create()
                                          ->getPopupForDisplay(
                                              [
                                                  'params'    => $this->getRequest()->getParam('params'),
                                                  'uri'       => $this->getRequest()->getParam('identifier'),
                                                  'referer'   => $this->getRequest()->getParam('referer'),
                                                  'useragent' => $this->getRequest()->getServer('HTTP_USER_AGENT'),
                                              ],
                                              $this->_storeManager->getStore()->getId(),
                                              false
                                          );
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return $popups;
    }
}
