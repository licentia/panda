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
