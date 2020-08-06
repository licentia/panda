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

    const ERROR_INVALID_DATE = 'invalidDate';

    const ERROR_INVALID_STATUS = 'invalidStatus';

    const ERROR_INVALID_GENDER = 'invalidGender';

    const ERROR_INVALID_CELLPHONE = 'invalidCellphone';

    /**
     * @var array
     */
    protected $customersIds;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * Customer constructor.
     *
     * @param \Licentia\Panda\Helper\Data $pandaHelper
     * @param ResourceConnection          $resourceConnection
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        ResourceConnection $resourceConnection
    ) {

        $this->resourceConnection = $resourceConnection;
        $this->pandaHelper = $pandaHelper;
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

        if (isset($value['dob']) &&
            !$this->validateDate($value['dob'], 'Y-m-d')) {
            $this->_addMessages([self::ERROR_INVALID_DATE]);
            $valid = false;
        }

        if (isset($value['created_at']) &&
            !$this->validateDate($value['created_at'])) {
            $this->_addMessages([self::ERROR_INVALID_DATE]);
            $valid = false;
        }

        if (isset($value['unsubscribed_at']) &&
            !$this->validateDate($value['unsubscribed_at'])) {
            $this->_addMessages([self::ERROR_INVALID_DATE]);
            $valid = false;
        }

        if (isset($value['gender']) &&
            !in_array($value['gender'], \Licentia\Panda\Model\Subscribers::GENDER_LIST)) {
            $this->_addMessages([self::ERROR_INVALID_GENDER]);
            $valid = false;
        }
        if (isset($value['status']) &&
            !in_array($value['status'], \Licentia\Panda\Model\Subscribers::AVAILABLE_STATUS)) {
            $this->_addMessages([self::ERROR_INVALID_STATUS]);
            $valid = false;
        }

        if (isset($value['cellphone']) &&
            !$this->pandaHelper->isPhoneNumberValid($value['cellphone'])) {
            $this->_addMessages([self::ERROR_INVALID_CELLPHONE]);
            $valid = false;
        }

        if (isset($value['updated_at']) &&
            !$this->validateDate($value['updated_at'])) {
            $this->_addMessages([self::ERROR_INVALID_DATE]);
            $valid = false;
        }

        if (!filter_var($value['email'], FILTER_VALIDATE_EMAIL)) {
            $this->_addMessages([self::ERROR_INVALID_EMAIL]);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param        $date
     * @param string $format
     *
     * @return bool
     */
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {

        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * @return array
     */
    public function getAllCustomerIds()
    {

        if (!$this->customersIds) {

            $this->customersIds = $this->connection->fetchCol(
                $this->connection->select()
                                 ->from(
                                     $this->resourceConnection->getTableName('customer_entity'), 'entity_id'
                                 )
            );
        }

        return $this->customersIds;
    }

}
