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

namespace Licentia\Panda\Model;

/**
 * Class Session
 *
 * @package Licentia\Panda\Model
 */
class Session extends \Magento\Framework\Session\SessionManager
{

    /**
     * @param $pandaAcquisitionCampaign
     *
     * @return $this
     */
    public function setPandaAcquisitionCampaign($pandaAcquisitionCampaign)
    {

        return $this->setData('panda_acquisition_campaign', $pandaAcquisitionCampaign);
    }

    /**
     * @return mixed
     */
    public function getPandaAcquisitionCampaign()
    {

        return $this->getData('panda_acquisition_campaign');
    }
}
