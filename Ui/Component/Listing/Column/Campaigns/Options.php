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
