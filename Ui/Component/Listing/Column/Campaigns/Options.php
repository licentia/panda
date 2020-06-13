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

namespace Licentia\Panda\Ui\Component\Listing\Column\Campaigns;

use Licentia\Panda\Model\CampaignsFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * Options constructor.
     *
     * @param CampaignsFactory $campaignsFactory
     */
    public function __construct(CampaignsFactory $campaignsFactory)
    {

        $this->campaignsFactory = $campaignsFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $return = [];

        $campaigns = $this->campaignsFactory->create()->toFormValues();

        foreach ($campaigns as $id => $campaign) {
            $return[] = ['label' => $campaign, 'value' => $id];
        }

        return $return;
    }
}
