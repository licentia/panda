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

namespace Licentia\Panda\Ui\Component\Listing\Column\Splits;

use Licentia\Panda\Model\SplitsFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Winners implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var SplitsFactory
     */
    protected $splitsFactory;

    /**
     * Options constructor.
     *
     * @param SplitsFactory $splitsFactory
     */
    public function __construct(SplitsFactory $splitsFactory)
    {

        $this->splitsFactory = $splitsFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        if ($this->options === null) {
            $result = $this->splitsFactory->create()->getWinnerOptions();

            foreach ($result as $key => $value) {
                $this->options[] = ['label' => $value, 'value' => $key];
            }
        }

        return $this->options;
    }
}
