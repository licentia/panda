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
 * Class DomHelper
 *
 * @package Licentia\Panda\Helper
 */
class DomHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param $id
     */
    public function autoresponderEventDelete($id)
    {

        $paths = [];
        $paths[] = realpath(dirname(__FILE__) . '/../etc/frontend/events.xml');
        $paths[] = realpath(dirname(__FILE__) . '/../etc/webapi_rest/events.xml');

        foreach ($paths as $path) {
            $file = simplexml_load_file($path);

            foreach ($file->xpath('/config/event/observer[@name="panda_autoresponder_internal_event_' . $id . '"]') as
                     $torm) {
                unset($torm[0]);
            }

            foreach ($file->xpath('/config/event') as $torm) {
                $child = $torm->children();
                if (!isset($child->observer)) {
                    unset($torm[0]);
                }
            }

            $dom = new \DOMDocument("1.0");
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($file->asXML());
            $dom->save($path);
        }
    }

    /**
     * @param $observers
     * @param $id
     */
    public function autoresponderEventSave($observers, $id)
    {

        $paths = [];
        $paths[] = realpath(dirname(__FILE__) . '/../etc/frontend/events.xml');
        $paths[] = realpath(dirname(__FILE__) . '/../etc/webapi_rest/events.xml');

        foreach ($paths as $path) {
            $file = simplexml_load_file($path);

            foreach ($file->xpath(
                '/config/event/observer[@name="panda_autoresponder_internal_event_' . $id . '"]'
            ) as $torm) {
                unset($torm[0]);
            }

            foreach ($file->xpath('/config/event') as $torm) {
                $child = $torm->children();

                if (!isset($child->observer)) {
                    unset($torm[0]);
                }
            }

            foreach ($observers as $element) {
                if ($node = $file->xpath('/config/event[@name="' . $element . '"]')) {
                    $node = $node[0];

                    $event = $node->addChild('observer');
                    $event->addAttribute('name', 'panda_autoresponder_internal_event_' . $id);
                    $event->addAttribute('instance', 'Licentia\Panda\Observer\InternalEvent');
                } else {
                    $observer = $file->addChild('event');
                    $observer->addAttribute('name', $element);

                    $event = $observer->addChild('observer');
                    $event->addAttribute('name', 'panda_autoresponder_internal_event_' . $id);
                    $event->addAttribute('instance', 'Licentia\Panda\Observer\InternalEvent');
                }
            }

            $dom = new \DOMDocument("1.0");
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($file->asXML());
            $dom->save($path);
        }
    }
}
