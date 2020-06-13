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

namespace Licentia\Panda\Api;

/**
 * Interface PopupsRepositoryInterface
 *
 * @package Licentia\Panda\Api
 */
interface PopupsRepositoryInterface
{

    /**
     * Retrieve Recommendations for the specified zone.
     *
     * @param string $zone       The zone code.
     * @param string $identifier The identifier code.
     * @param int    $customerId The customer ID.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getDisplayWindows($zone, $identifier, $customerId = null);
}
