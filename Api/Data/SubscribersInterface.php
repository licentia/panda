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

namespace Licentia\Panda\Api\Data;

/**
 * Interface SubscribersInterface
 *
 * @package Licentia\Panda\Api\Data
 */
interface SubscribersInterface
{

    /**
     * @param int $subscriberId
     *
     * @return SubscribersInterface
     */
    public function setSubscriberId($subscriberId);

    /**
     * @param int $customerId
     *
     * @return SubscribersInterface
     */
    public function setCustomerId($customerId);

    /**
     * @param int $storeId
     *
     * @return SubscribersInterface
     */
    public function setStoreId($storeId);

    /**
     * @param string $code
     *
     * @return SubscribersInterface
     */
    public function setCode($code);

    /**
     * @param string $firstname
     *
     * @return SubscribersInterface
     */
    public function setFirstname($firstname);

    /**
     * @param string $lastname
     *
     * @return SubscribersInterface
     */
    public function setLastname($lastname);

    /**
     * @param string $email
     *
     * @return SubscribersInterface
     */
    public function setEmail($email);

    /**
     * @param string $cellphone
     *
     * @return SubscribersInterface
     */
    public function setCellphone($cellphone);

    /**
     * @param string $createdAt
     *
     * @return SubscribersInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @param string $dob
     *
     * @return SubscribersInterface
     */
    public function setDob($dob);

    /**
     * @param int $status
     *
     * @return SubscribersInterface
     */
    public function setStatus($status);

    /**
     * @param int $bounces
     *
     * @return SubscribersInterface
     */
    public function setBounces($bounces);

    /**
     * @param int $sent
     *
     * @return SubscribersInterface
     */
    public function setSent($sent);

    /**
     * @param int $views
     *
     * @return SubscribersInterface
     */
    public function setViews($views);

    /**
     * @param int $clicks
     *
     * @return SubscribersInterface
     */
    public function setClicks($clicks);

    /**
     * @param int $conversionsNumber
     *
     * @return SubscribersInterface
     */
    public function setConversionsNumber($conversionsNumber);

    /**
     * @param double $conversionsAmount
     *
     * @return SubscribersInterface
     */
    public function setConversionsAmount($conversionsAmount);

    /**
     * @param double $conversionsAverage
     *
     * @return SubscribersInterface
     */
    public function setConversionsAverage($conversionsAverage);

    /**
     * @param string $sendTime
     *
     * @return SubscribersInterface
     */
    public function setSendTime($sendTime);

    /**
     * @param int $previousCustomer
     *
     * @return SubscribersInterface
     */
    public function setPreviousCustomer($previousCustomer);

    /**
     * @param string $gender
     *
     * @return SubscribersInterface
     */
    public function setGender($gender);

    /**
     * @param string $lastMessageSentAt
     *
     * @return SubscribersInterface
     */
    public function setLastMessageSentAt($lastMessageSentAt);

    /**
     * @param string $lastMessageOpenAt
     *
     * @return SubscribersInterface
     */
    public function setLastMessageOpenAt($lastMessageOpenAt);

    /**
     * @param string $lastConversionAt
     *
     * @return SubscribersInterface
     */
    public function setLastConversionAt($lastConversionAt);

    /**
     * @param string $lastMessageClickAt
     *
     * @return SubscribersInterface
     */
    public function setLastMessageClickAt($lastMessageClickAt);

    /**
     * @param int $lastOpenCampaignId
     *
     * @return SubscribersInterface
     */
    public function setLastOpenCampaignId($lastOpenCampaignId);

    /**
     * @param int $lastClickCampaignId
     *
     * @return SubscribersInterface
     */
    public function setLastClickCampaignId($lastClickCampaignId);

    /**
     * @param int $lastConversionCampaignId
     *
     * @return SubscribersInterface
     */
    public function setLastConversionCampaignId($lastConversionCampaignId);

    /**
     * @param string $unsubscribedAt
     *
     * @return SubscribersInterface
     */
    public function setUnsubscribedAt($unsubscribedAt);

    /**
     * @param int $formId
     *
     * @return SubscribersInterface
     */
    public function setFormId($formId);

    /**
     * @return int
     */
    public function getSubscriberId();

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getDob();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return int
     */
    public function getBounces();

    /**
     * @return int
     */
    public function getSent();

    /**
     * @return int
     */
    public function getViews();

    /**
     * @return int
     */
    public function getClicks();

    /**
     * @return int
     */
    public function getConversionsNumber();

    /**
     * @return double
     */
    public function getConversionsAmount();

    /**
     * @return double
     */
    public function getConversionsAverage();

    /**
     * @return string
     */
    public function getSendTime();

    /**
     * @return int
     */
    public function getPreviousCustomer();

    /**
     * @return string
     */
    public function getGender();

    /**
     * @return string
     */
    public function getLastMessageSentAt();

    /**
     * @return string
     */
    public function getLastMessageOpenAt();

    /**
     * @return string
     */
    public function getLastConversionAt();

    /**
     * @return string
     */
    public function getLastMessageClickAt();

    /**
     * @return int
     */
    public function getLastOpenCampaignId();

    /**
     * @return int
     */
    public function getLastClickCampaignId();

    /**
     * @return int
     */
    public function getLastConversionCampaignId();

    /**
     * @return string
     */
    public function getUnsubscribedAt();

    /**
     * @return int
     */
    public function getFormId();

    /**
     * @return string
     */
    public function getField1();

    /**
     * @return string
     */
    public function getField2();

    /**
     * @return string
     */
    public function getField3();

    /**
     * @return string
     */
    public function getField4();

    /**
     * @return string
     */
    public function getField5();

    /**
     * @return string
     */
    public function getField6();

    /**
     * @return string
     */
    public function getField7();

    /**
     * @return string
     */
    public function getField8();

    /**
     * @return string
     */
    public function getField9();

    /**
     * @return string
     */
    public function getField10();

    /**
     * @return string
     */
    public function getField11();

    /**
     * @return string
     */
    public function getField12();

    /**
     * @return string
     */
    public function getField13();

    /**
     * @return string
     */
    public function getField14();

    /**
     * @return string
     */
    public function getField15();

    /**
     * @return string
     */
    public function getField1Name();

    /**
     * @return string
     */
    public function getField2Name();

    /**
     * @return string
     */
    public function getField3Name();

    /**
     * @return string
     */
    public function getField4Name();

    /**
     * @return string
     */
    public function getField5Name();

    /**
     * @return string
     */
    public function getField6Name();

    /**
     * @return string
     */
    public function getField7Name();

    /**
     * @return string
     */
    public function getField8Name();

    /**
     * @return string
     */
    public function getField9Name();

    /**
     * @return string
     */
    public function getField10Name();

    /**
     * @return string
     */
    public function getField11Name();

    /**
     * @return string
     */
    public function getField12Name();

    /**
     * @return string
     */
    public function getField13Name();

    /**
     * @return string
     */
    public function getField14Name();

    /**
     * @return string
     */
    public function getField15Name();
}
