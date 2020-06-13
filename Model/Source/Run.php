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

namespace Licentia\Panda\Model\Source;

/**
 * Class Run
 *
 * @package Licentia\Panda\Model\Source
 */
class Run
{

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $return = [];

        for ($i = 0; $i <= 23; $i++) {
            $return[] = [
                'value' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'label' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
            ];
        }

        return $return;
    }
}
