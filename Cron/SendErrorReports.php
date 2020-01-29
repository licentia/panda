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

namespace Licentia\Panda\Cron;

/**
 * Class SendErrorReports
 *
 * @package Licentia\Panda\Cron
 */
class SendErrorReports
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Licentia\Panda\Helper\Debug
     */
    protected $debug;

    /**
     * SendErrorReports constructor.
     *
     * @param \Licentia\Panda\Helper\Debug                       $debug
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Licentia\Panda\Logger\Logger                      $pandaLogger
     */
    public function __construct(
        \Licentia\Panda\Helper\Debug $debug,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Panda\Logger\Logger $pandaLogger
    ) {

        $this->debug = $debug;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaLogger = $pandaLogger;
    }

    /**
     * @return bool
     */
    public function execute()
    {

        if (!$this->scopeConfig->isSetFlag('panda_general/reports/send')) {
            return false;
        }
        $file = $this->debug->getFileLocation();

        $url = \Licentia\Panda\Helper\Debug::REPORT_URL;

        $postfields = [];
        $postfields["file"] = curl_file_create($file, 'plain/text');
        $headers = ["Content-Type" => "multipart/form-data"];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        if (curl_errno($ch)) {
            $this->pandaLogger->critical(curl_error($ch));
        }
        curl_close($ch);

        return true;
    }
}
