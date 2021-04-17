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

/**
 *  Coupons widget
 */
class Coupons extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Licentia\Panda\Model\CouponsFactory
     */
    protected $couponsFactory;

    /**
     */
    protected $registry;

    /**
     * Coupons constructor.
     *
     * @param \Magento\Framework\Data\Form\FormKey   $formKey
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Licentia\Panda\Helper\Data            $pandaHelper
     * @param \Licentia\Panda\Model\CouponsFactory   $couponsFactory
     * @param array                                  $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Block\Product\Context $context,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\CouponsFactory $couponsFactory,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->couponsFactory = $couponsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->formKey = $formKey;
        $this->registry = $context->getRegistry();

        $this->setData('subscriber_email', $pandaHelper->getSubscriber()->getEmail());
    }

    /**
     * @return \Licentia\Panda\Helper\Data
     */
    public function getPandaHelper()
    {

        return $this->pandaHelper;
    }

    /**
     * @param      $couponCode
     * @param bool $hide
     *
     * @return false|string
     */
    public function canShowCoupon($couponCode, $hide = false)
    {

        return $this->couponsFactory->create()->canShowCoupon($couponCode, $hide);
    }

    /**
     * @param $couponCode
     *
     * @return false|string
     */
    public function expiresAt($couponCode)
    {

        return $this->couponsFactory->create()->getCouponExpirationDate($couponCode);
    }

    /**
     * @param $data
     *
     * @return \Magento\Framework\DataObject|mixed
     */
    public function getWidgetData($data)
    {

        if ($this->getRequest()->getParam('hash')) {
            return $this->couponsFactory->create()
                                        ->getWidgetData($this->getRequest()->getParam('hash'))
                                        ->getData($data);
        }

        return $this->getData($data);
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    public function getRule()
    {

        if ($this->getRequest()->getParam('hash')) {
            return $this->couponsFactory->create()->getCouponFromHash($this->getRequest()->getParam('hash'));
        }

        return $this->couponsFactory->create()->getCoupon($this->getData());
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {

        $cacheEnabled = $this->pandaHelper->isCacheEnabled();

        if ($cacheEnabled && !$this->getRequest()->isPost()) {
            $this->couponsFactory->create()->getCoupon($this->getData());

            $params['hash'] = sha1(json_encode($this->getData()));

            $this->setTemplate('empty.phtml');

            $url = $this->_urlBuilder->getUrl('panda/coupons/get');

            $jsContent = "<script type='text/javascript'>
        
                        require(['jquery', 'domReady!'], function ($) {
                    
                            $.ajax({
                                url: '{$url}',
                                type: 'POST',
                                context: document.body,
                                success: function (responseText) {
                                    $('#panda_coupon').html(responseText);
                                },
                                data:  " . json_encode($params) . "
                            });
                        });
                    
                    </script>
                    <div  id='panda_coupon'>&nbsp;</div> ";

            $this->setContent($jsContent);
        } else {
            try {
                if (!$this->getTemplate()) {
                    $this->setTemplate('widgets/coupon.phtml');
                }
            } catch (\Exception $e) {
            }
        }

        return parent::_toHtml();
    }
}
