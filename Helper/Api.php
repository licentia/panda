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

namespace Licentia\Panda\Helper;

/**
 * Class Api
 *
 * @package Licentia\Panda\Helper
 */
class Api extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * Api constructor.
     *
     * @param \Magento\Framework\HTTP\Client\Curl   $curl
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\Helper\Context $context
    ) {

        parent::__construct($context);

        $this->curl = $curl;
    }

    /**
     *
     */
    const API_URL = 'https://api.greenflyingpanda.com/';

    protected $data = [];

    /**
     * @return string
     */
    public function getApiKey()
    {

        return $this->scopeConfig->getValue('panda_general/info/apikey');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAccountInfo()
    {

        $json = [];
        $json['apikey'] = $this->getApiKey();

        $data = [];

        try {
            $data = $this->request('account/info', $json);
        } catch (\Exception $e) {
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function isAccountActive()
    {

        ####
        ####
        #### I can't be bothered to implement a proper validation system
        ####
        ####

        return true;
    }

    /**
     * @param $cellphone
     *
     * @return mixed
     * @throws \Exception
     */
    public function validateCellphone($cellphone)
    {

        return $this->request('cellphone/validate', ['phone' => $cellphone]);
    }

    /**
     * @return mixed
     */
    public function getNewApiKey()
    {

        return $this->request('account/generateApiKey', []);
    }

    /**
     * @param array $json
     *
     * @return mixed
     * @throws \Exception
     */
    public function deleteAccount(array $json)
    {

        return $this->request('account/delete', $json);
    }

    /**
     * @param array $json
     *
     * @return mixed
     * @throws \Exception
     */
    public function updateAccount(array $json)
    {

        $json['apikey'] = $this->getApiKey();

        return $this->request('account/update', $json);
    }

    /**
     * @param array $json
     *
     * @return mixed
     * @throws \Exception
     */
    public function updateApikey(array $json)
    {

        return $this->request('account/updateApi', $json);
    }

    /**
     * @param array $json
     *
     * @return mixed
     * @throws \Exception
     */
    public function validateApikey(array $json)
    {

        return $this->request('account/validateApi', $json);
    }

    /**
     * @return mixed
     */
    public function cancelAccount()
    {

        return $this->request('account/cancel');
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public function recoverAPI($email)
    {

        return $this->request('account/recover', ['email' => $email]);
    }

    /**
     * @param       $url
     * @param array $data
     *
     * @return bool|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function request($url, $data = [])
    {

        $url = self::API_URL . $url;

        if (isset($this->data[$url])) {
            return $this->data[$url];
        }

        if (!isset($data['apikey'])) {
            $data['apikey'] = $this->getApiKey();
        }
        $data = json_encode($data);

        $this->curl->post($url, $data);
        $remote = $this->curl->getBody();

        if (isset($remote[0]) && ($remote[0] == '[' || $remote[0] == '{')) {
            $remote = json_decode($remote, true);
        }

        if (isset($remote['result']) && $remote['result'] == 'error') {
            if (isset($remote['message'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__($remote['message']));
            }
        }

        $this->data[$url] = $remote;

        return $remote;
    }
}
