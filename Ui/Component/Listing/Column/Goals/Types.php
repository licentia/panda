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

namespace Licentia\Panda\Ui\Component\Listing\Column\Goals;

use Licentia\Panda\Model\GoalsFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Types implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var GoalsFactory
     */
    protected $goalsFactory;

    /**
     * Types constructor.
     *
     * @param GoalsFactory $goalsFactory
     */
    public function __construct(GoalsFactory $goalsFactory)
    {

        $this->goalsFactory = $goalsFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        if ($this->options === null) {
            $types = \Licentia\Panda\Model\Goals::getGoalTypes();

            $result = [];
            foreach ($types as $key => $type) {
                $result[] = ['label' => $type, 'value' => $key];
            }

            $this->options = $result;
        }

        return $this->options;
    }
}
