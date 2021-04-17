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

namespace Licentia\Panda\Plugin;

use Magento\Customer\Block\Newsletter;

/**
 * Class NewsletterSave
 *
 * @package Licentia\Panda\Observer
 */
class NewsletterSave
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected \Magento\Framework\UrlInterface $url;

    /**
     * NewsletterSave constructor.
     *
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url
    ) {

        $this->url = $url;
    }

    /**
     * @param Newsletter                         $subject
     * @param                                    $result
     *
     * @return string
     */
    public function afterGetAction(Newsletter $subject, $result)
    {

        return $this->url->getUrl('panda/subscriber/save');
    }
}
