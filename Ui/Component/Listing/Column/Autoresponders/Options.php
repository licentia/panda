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

namespace Licentia\Panda\Ui\Component\Listing\Column\Autoresponders;

use Licentia\Panda\Model\AutorespondersFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected array $options;

    /**
     * @var AutorespondersFactory
     */
    protected AutorespondersFactory $autorespondersFactory;

    /**
     * Options constructor.
     *
     * @param AutorespondersFactory $autorespondersFactory
     */
    public function __construct(AutorespondersFactory $autorespondersFactory)
    {

        $this->autorespondersFactory = $autorespondersFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        if ($this->options === null) {
            $result = $this->autorespondersFactory->create()->toOptionValuesAll();

            $this->options = $result;
        }

        return $this->options;
    }
}
