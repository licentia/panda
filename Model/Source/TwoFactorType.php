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

namespace Licentia\Panda\Model\Source;

/**
 * Class TwoFactorType
 *
 * @package Licentia\Panda\Model\Source
 */
class TwoFactorType implements \Magento\Framework\Option\ArrayInterface
{

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {

        $return = [];
        $return[] = ['value' => 'sms', 'label' => __('SMS')];
        $return[] = ['value' => 'email', 'label' => __('E-Mail')];

        return $return;
    }
}
