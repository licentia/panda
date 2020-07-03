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

namespace Licentia\Panda\Model\Import;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use \Licentia\Panda\Model\Import\Validator\Customer;

class Subscribers extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{

    const COL_UPDATED_AT = 'updated_at';

    const COL_EMAIL = 'email';

    const COL_STORE_ID = 'store_id';

    const COL_STORE = 'store';

    const COL_STATUS = 'status';

    const TABLE_SUBSCRIBERS = 'panda_subscribers';

    const VALIDATOR_MAIN = 'validator';

    const VALIDATOR_WEBSITE = 'validator_website';

    /**
     * Validation failure message template definitions.
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_SKU_IS_EMPTY => 'Email is empty',
        Customer::ERROR_INVALID_CUSTOMER_ID    => 'Invalid Customer ID',
        Customer::ERROR_INVALID_EMAIL          => 'Invalid Email',
        Customer::ERROR_INVALID_DATE           => 'Invalid Date',
        Customer::ERROR_INVALID_CELLPHONE      => 'Invalid Cellphone',
    ];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory
     */
    protected $_resourceFactory;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\StoreResolver
     */
    protected $_storeResolver;

    /**
     * @var ImportProduct
     */
    protected $_importProduct;

    /**
     * @var array
     */
    protected $_validators = [];

    /**
     * @var array
     */
    protected $cachedEmailsToDelete;

    /**
     * @var array
     */
    protected $oldEmails = null;

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [self::COL_EMAIL];

    /**
     * Catalog product entity
     *
     * @var string
     */
    protected $subscribersTable;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var Validator\Customer
     */
    protected $customerValidator;

    /**
     * Product entity link field
     *
     * @var string
     */
    private $subscribersTablePrimaryKey;

    /**
     * Subscribers constructor.
     *
     * @param \Magento\Framework\Json\Helper\Data                   $jsonHelper
     * @param \Magento\ImportExport\Helper\Data                     $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Framework\App\ResourceConnection             $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper      $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils                 $string
     * @param ProcessingErrorAggregatorInterface                    $errorAggregator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime           $dateTime
     * @param ImportProduct\StoreResolver                           $storeResolver
     * @param \Licentia\Panda\Model\SubscribersFactory              $subscribersFactory
     * @param Validator\Customer                                    $customerValidator
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        Customer $customerValidator
    ) {

        $this->validColumnNames = \Licentia\Panda\Model\Subscribers::AVAILABLE_IMPORT_FIELDS;
        $this->_resourceFactory = $subscribersFactory->create()->getResource();
        $this->dateTime = $dateTime;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection('write');
        $this->_storeResolver = $storeResolver;
        $this->subscribersTable = $this->_resourceFactory->getTable('panda_subscribers');
        $this->oldEmails = $this->retrieveOldEmails();
        $this->errorAggregator = $errorAggregator;
        $this->customerValidator = $customerValidator;

        $this->_messageTemplates[Customer::ERROR_INVALID_STATUS] = 'Invalid Status. Available:' . implode(',',
                \Licentia\Panda\Model\Subscribers::AVAILABLE_STATUS);
        $this->_messageTemplates[Customer::ERROR_INVALID_GENDER] = 'Invalid Gender. Available:' . implode(',',
                \Licentia\Panda\Model\Subscribers::GENDER_LIST);

        foreach (array_merge($this->errorMessageTemplates, $this->_messageTemplates) as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {

        return 'panda_subscribers';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int   $rowNum
     *
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function validateRow(array $rowData, $rowNum)
    {

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        // BEHAVIOR_DELETE use specific validation logic
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($rowData[self::COL_EMAIL])) {
                $this->addRowError(Customer::ERROR_INVALID_EMAIL, $rowNum);

                return false;
            }

            return true;
        }

        if (!$this->customerValidator->isValid($rowData)) {
            foreach ($this->customerValidator->getMessages() as $message) {
                $this->addRowError($message, $rowNum);
            }
        }

        $email = false;
        if (isset($rowData[self::COL_EMAIL])) {
            $email = $rowData[self::COL_EMAIL];
        }

        if (false === $email) {
            $this->addRowError(ValidatorInterface::ERROR_ROW_IS_ORPHAN, $rowNum);
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create Subscribers data from raw data.
     *
     * @return bool Result of operation.
     * @throws \Exception
     */
    protected function _importData()
    {

        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteSubscribers();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceSubscribers();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveSubscribers();
        }

        return true;
    }

    /**
     * Save subscribers
     *
     * @return $this
     * @throws \Exception
     */
    public function saveSubscribers()
    {

        $this->saveAndReplaceSubscribers();

        return $this;
    }

    /**
     * Deletes Subscribers data from raw data.
     *
     * @return $this
     * @throws \Exception
     */
    public function deleteSubscribers()
    {

        $this->cachedEmailsToDelete = null;
        $listEmails = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {

            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $listEmails[] = $rowData[self::COL_EMAIL];
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);

                }
            }
        }

        if ($listEmails) {
            $this->deleteSubscribersFinal(array_unique($listEmails), $this->subscribersTable);
        }

        return $this;
    }

    /**
     * Replace subscribers
     *
     * @return $this
     * @throws \Exception
     */
    public function replaceSubscribers()
    {

        $this->saveAndReplaceSubscribers();

        return $this;
    }

    /**
     * Save and replace advanced subscribers
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws \Exception
     */
    protected function saveAndReplaceSubscribers()
    {

        $behavior = $this->getBehavior();
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
            $this->cachedEmailsToDelete = null;
        }
        $listEmails = [];
        $subscribers = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError('Empty Email', $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowEmail = $rowData[self::COL_EMAIL];
                $listEmails[] = $rowEmail;

                if (!empty($rowData[self::COL_STORE])) {
                    $subscribers[$rowEmail]['store_id'] = $this->getStoreId($rowData[self::COL_STORE]);
                }

                $subscribers[$rowEmail] = array_intersect_key($rowData,
                    array_flip(\Licentia\Panda\Model\Subscribers::AVAILABLE_IMPORT_FIELDS));

                if (!empty($rowData[self::COL_STATUS])) {
                    $subscribers[$rowEmail]['status'] = array_search($subscribers[$rowEmail]['status'],
                        \Licentia\Panda\Model\Subscribers::AVAILABLE_STATUS);
                }
            }

            if (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->processCountExistingSubscribers($subscribers, self::TABLE_SUBSCRIBERS)
                     ->processCountNewSubscribers($subscribers);

                $this->saveSubscribersExecute($subscribers, self::TABLE_SUBSCRIBERS);
                if ($subscribers) {
                    $this->setUpdatedAt($subscribers);
                }
            }
        }

        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {

            if ($subscribers) {
                $this->processCountNewSubscribers($subscribers);
                if ($this->deleteSubscribersFinal(array_unique($subscribers), self::TABLE_SUBSCRIBERS)) {
                    $this->saveSubscribersExecute($subscribers, self::TABLE_SUBSCRIBERS);
                    $this->setUpdatedAt($listEmails);

                }
            }
        }

        return $this;
    }

    /**
     * Save product subscribers.
     *
     * @param array  $subscribers
     * @param string $table
     *
     * @return $this
     * @throws \Exception
     */
    protected function saveSubscribersExecute(array $subscribers, $table)
    {

        if ($subscribers) {
            $tableName = $this->_resourceFactory->getTable($table);
            $this->_connection->insertOnDuplicate($tableName, $subscribers, $this->validColumnNames);
        }

        return $this;
    }

    /**
     * Deletes subscribers subscribers.
     *
     * @param array  $listEmails
     * @param string $table
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deleteSubscribersFinal(array $listEmails, $table)
    {

        $tableName = $this->_resourceFactory->getTable($table);
        $subscriberTablePrimaryKey = $this->getSubscribersTablePrimaryKey();

        if ($tableName && $listEmails) {
            if (!$this->cachedEmailsToDelete) {
                $this->cachedEmailsToDelete = $this->_connection->fetchCol(
                    $this->_connection->select()
                                      ->from($this->subscribersTable, [$subscriberTablePrimaryKey])
                                      ->where('email IN (?)', $listEmails)
                );
            }

            if ($this->cachedEmailsToDelete) {
                try {
                    $this->countItemsDeleted += $this->_connection->delete(
                        $tableName,
                        $this->_connection->quoteInto($subscriberTablePrimaryKey . ' IN (?)',
                            $this->cachedEmailsToDelete)
                    );

                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            } else {
                $this->addRowError('Email is Empty', 0);

                return false;
            }
        }

        return false;
    }

    /**
     * Set updated_at for product
     *
     * @param array $listEmails
     *
     * @return $this
     */
    protected function setUpdatedAt(array $listEmails)
    {

        $updatedAt = $this->dateTime->gmtDate('Y-m-d H:i:s');
        $this->_connection->update(
            $this->subscribersTable,
            [self::COL_UPDATED_AT => $updatedAt],
            $this->_connection->quoteInto('email IN (?)', array_unique($listEmails))
        );

        return $this;
    }

    /**
     * Get store id by code
     *
     * @param string $storeIdCode
     *
     * @return array|int|string
     */
    protected function getStoreId($storeIdCode)
    {

        return $this->_storeResolver->getStoreCodeToId($storeIdCode);
    }

    /**
     * Retrieve product skus
     *
     * @return array
     * @throws \Exception
     */
    protected function retrieveOldEmails()
    {

        if ($this->oldEmails === null) {
            $this->oldEmails = $this->_connection->fetchPairs(
                $this->_connection->select()->from(
                    $this->subscribersTable,
                    ['email', $this->getSubscribersTablePrimaryKey()]
                )
            );
        }

        return $this->oldEmails;
    }

    /**
     * Count existing subscribers
     *
     * @param array  $subscribers
     * @param string $table
     *
     * @return $this
     * @throws \Exception
     */
    protected function processCountExistingSubscribers($subscribers, $table)
    {

        $oldEmails = $this->retrieveOldEmails();
        $existProductIds = array_intersect_key($oldEmails, $subscribers);
        if (!count($existProductIds)) {
            return $this;
        }

        $tableName = $this->_resourceFactory->getTable($table);
        $productEntityLinkField = $this->getSubscribersTablePrimaryKey();
        $existingSubscribers = $this->_connection->fetchAll(
            $this->_connection->select()->from(
                $tableName,
                [$productEntityLinkField, 'email']
            )->where(
                $productEntityLinkField . ' IN (?)',
                $existProductIds
            )
        );
        foreach ($existingSubscribers as $existingSubscriber) {
            foreach ($subscribers as $email => $skuSubscribers) {
                if (isset($oldEmails[$email]) &&
                    $existingSubscriber[$productEntityLinkField] == $oldEmails[$email]) {
                    $this->incrementCounterUpdated($skuSubscribers, $existingSubscriber);
                }
            }
        }

        return $this;
    }

    /**
     * Increment counter of updated items
     *
     * @param array $subscribers
     * @param array $existingSubscribers
     *
     * @return void
     */
    protected function incrementCounterUpdated($subscribers, $existingSubscribers)
    {

        if ($existingSubscribers['email'] == $subscribers['email']) {
            $this->countItemsUpdated++;
        }
    }

    /**
     * Count new subscribers
     *
     * @param array $subscribers
     *
     * @return $this
     */
    protected function processCountNewSubscribers(array $subscribers)
    {

        $this->countItemsCreated = count($subscribers);

        $this->countItemsCreated -= $this->countItemsUpdated;

        return $this;
    }

    /**
     * Get product entity link field
     *
     * @return string
     * @throws \Exception
     */
    private function getSubscribersTablePrimaryKey()
    {

        return 'subscriber_id';
    }
}
