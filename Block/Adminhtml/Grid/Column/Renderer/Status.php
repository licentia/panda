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

namespace Licentia\Panda\Block\Adminhtml\Grid\Column\Renderer;

/**
 * Class Status
 *
 * @package Licentia\Panda\Block\Adminhtml\Grid\Column\Renderer
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * Render indexer status
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {

        $value = $this->_getValue($row);

        if ($value == "standby") {
            return ' <span class="grid-severity-minor"><span>' . __('Stand By') . '</span></span>';
        }

        if ($value == "queuing") {
            return ' <span class="grid-severity-major"><span>' . __('Queuing') . '</span></span>';
        }

        if ($value == "running") {
            return ' <span class="grid-severity-major"><span>' . __('Running') . '</span></span>';
        }

        if ($value == "finished") {
            return ' <span class="grid-severity-notice"><span>' . __('Finished') . '</span></span>';
        }
    }
}
