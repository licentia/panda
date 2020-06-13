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

namespace Licentia\Panda\Model\Autoresponders;

/**
 * Class Customer
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Magento\Framework\Model;

/**
 * Class Customer
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Customer extends AbstractModel
{

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $configFactory;

    /**
     * Customer constructor.
     *
     * @param \Magento\Eav\Model\ConfigFactory                                 $configFactory
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Licentia\Panda\Model\ChainseditFactory                          $chainseditFactory
     * @param \Magento\Backend\Block\Template                                  $block
     * @param \Licentia\Panda\Model\ChainsFactory                              $chainsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection
     * @param Model\Context                                                    $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param Model\ResourceModel\AbstractResource|null                        $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null               $resourceCollection
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Eav\Model\ConfigFactory $configFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        \Magento\Backend\Block\Template $block,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->configFactory = $configFactory;
        $this->customerFactory = $customerFactory;

        parent::__construct(
            $chainseditFactory,
            $block,
            $chainsFactory,
            $chainseditCollection,
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function add()
    {

        $params = $this->getData('params');
        $parentId = '';
        $obj = $this->configFactory->create()->getAttribute('customer', $params['attribute']);

        $data = [];
        $data['chain_id'] = $this->getData('chain_id');
        $data['autoresponder_id'] = $params['autoresponder_id'];
        $data['name'] = "Change: " . $obj->getFrontendLabel() . " To: " . $params['value'];
        $data['extra_data'] = json_encode($params);

        if (!isset($data['chain_id'])) {
            $parentId = $params['parentid'];
            $data['parent_id'] = $parentId;
            $data['event'] = $params['type'];
        }

        $chain = $this->chainseditFactory->create()
                                         ->setData($data)
                                         ->save();

        if ($this->getMode() == 'edit') {
            return $data['name'];
        }

        $this->chainseditFactory->create()
                                ->getResource()
                                ->getConnection()
                                ->update(
                                    $this->chainseditFactory->create()
                                                            ->getResource()
                                                            ->getTable('panda_autoresponders_chains_edit'),
                                    ['parent_id' => $chain->getId()],
                                    ['parent_id=?' => $parentId, 'chain_id !=?' => $chain->getId()]
                                );

        $return = '<li><div id = "' . $chain->getId() . '" ><span class="name" > ' . $data['name'] .
                  ' </span ></div ></li>';

        return $return;
    }

    /**
     * @return string
     */
    public function render()
    {

        $customerAttributes = $this->customerFactory->create()
                                                    ->getResource()->loadAllAttributes()->getAttributesByCode();

        $attrToRemove =
            [
                'increment_id',
                'confirmation',
                'created_at',
                'updated_at',
                'attribute_set_id',
                'entity_type_id',
                'entity_id',
                'first_failure',
                'failures_num',
                'website_id',
                'confirmation',
                'created_in',
                'default_billing',
                'rp_token',
                'rp_token_created_at',
                'default_shipping',
                'password_hash',
            ];

        $attributes = [];
        foreach ($customerAttributes as $attribute) {
            if (in_array($attribute->getAttributeCode(), $attrToRemove)) {
                continue;
            }

            if (strlen($attribute->getFrontendLabel()) == 0) {
                continue;
            }

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        asort($attributes);

        $select = "<select  name='attribute'>";
        foreach ($attributes as $key => $value) {
            $selected = '';

            if ($this->getData('extra_data/attribute') == $key) {
                $selected = ' selected="select" ';
            }

            $select .= "<option $selected value='{$key}'>{$value}</option>";
        }

        $select .= "</select>";

        $img = $this->template->getViewFileUrl('Licentia_Panda::images/close.png');

        $class = $this->getData('chain_id') ? 'edit' : 'submit';
        $label = $this->getData('chain_id') ? __('Edit') : __('Add');
        $form = $this->getData('chain_id') ? 'edit_data' : 'add_data';

        $t = function ($t) {

            return __($t);
        };

        $return = <<<EOL
        <span class="formTriggersWrapper">
        <form class="$form" id="formTriggers" method="post" action="">
        <img class="close" src="$img" />
        <label>{$t('Change Customer Attribute Value')}</label>
        $select
        <label>To</label>
        <input type="text" class="required" value="{$this->getData('extra_data/value')}"  name="value" placeholder="{$t(
            'Change value to'
        )}">
        <input type="submit" class="$class" name="send" value="$label">
        </form>
        </span>

EOL;

        return $return;
    }

    /**
     * @param \Licentia\Panda\Model\Autoresponders $autoresponder
     * @param \Licentia\Panda\Model\Subscribers    $subscriber
     * @param \Licentia\Panda\Model\Events         $event
     * @param \Licentia\Panda\Model\Chains         $chain
     *
     * @return bool|void
     */
    public function run(
        \Licentia\Panda\Model\Autoresponders $autoresponder,
        \Licentia\Panda\Model\Subscribers $subscriber,
        \Licentia\Panda\Model\Events $event,
        \Licentia\Panda\Model\Chains $chain
    ) {

        $data = json_decode($chain->getExtraData(), true);

        if (!$data) {
            return false;
        }

        $customerId = $subscriber->getCustomerId();
        if (!$customerId) {
            return false;
        }

        $customer = $this->customerFactory->create()->load($customerId);

        if (!$customer->getId()) {
            return false;
        }

        $customer->setData($data['attribute'], $data['value']);

        $customer->save();

        return true;
    }
}
