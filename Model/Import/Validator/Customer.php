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

namespace Licentia\Panda\Model\Import\Validator;

use Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing;
use Magento\CatalogImportExport\Model\Import\Product\Validator\AbstractImportValidator;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Customer extends AbstractImportValidator implements RowValidatorInterface
{

    const ERROR_INVALID_CUSTOMER_ID = 'invalidCustID';
    const ERROR_INVALID_EMAIL = 'invalidEmail';

    /**
     * @var array
     */
    protected $customersIds;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * NotSyncedDataProvider constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {

        $this->resourceConnection = $resourceConnection;

        $this->connection = $this->resourceConnection->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function init($context)
    {

        return parent::init($context);
    }

    /**
     * Validate value
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValid($value)
    {

        $this->_clearMessages();
        $valid = true;
        if (isset($value['customer_id']) &&
            !in_array($value['customer_id'], $this->getAllCustomerIds())) {
            $this->_addMessages([self::ERROR_INVALID_CUSTOMER_ID]);
            $valid = false;
        }

        if (!filter_var($value['email'], FILTER_VALIDATE_EMAIL)) {
            $this->_addMessages([self::ERROR_INVALID_EMAIL]);
            $valid = false;
        }

        return $valid;
    }

    public function getAllCustomerIds()
    {

        if (!$this->customersIds) {

            $this->customersIds = $this->connection->fetchCol($this->connection->select()
                                                                               ->from($this->resourceConnection->getTableName('customer_entity',
                                                                                   'entity_id')));
        }

        return $this->customersIds;
    }

}
