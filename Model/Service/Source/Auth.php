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

namespace Licentia\Panda\Model\Service\Source;

/**
 * Class Auth
 *
 * @package Licentia\Panda\Model\Service\Source
 */
class Auth
{

    /**
     * @return array
     */
    public function toOptionArray()
    {

        return [
            "none"    => __('None (ignore username/password)'),
            "login"   => __('Login'),
            "plain"   => __('Plain'),
            "crammd5" => __('CRAM-MD5'),
        ];
    }
}
