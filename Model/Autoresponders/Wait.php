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

namespace Licentia\Panda\Model\Autoresponders;

/**
 * Class Wait
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Wait extends AbstractModel
{

    /**
     * @return string
     * @throws \Exception
     */
    public function add()
    {

        $params = $this->getData('params');

        $parentId = '';
        $data = [];
        $data['chain_id'] = $this->getData('chain_id');
        $data['autoresponder_id'] = $params['autoresponder_id'];
        $data['name'] = "Wait: {$params['days']} days,  {$params['hours']} hours,  {$params['minutes']} minutes";
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

        $return = '<li><div class="div_droppable editable" id = "' . $chain->getId() .
                  '" ><span class="name" > ' . $data['name'] . ' </span ></div ></li>';

        return $return;
    }

    /**
     * @return string
     */
    public function render()
    {

        $list = \Zend_Locale::getTranslationList('Days');
        $daysList = [];
        $i = 1;
        foreach ($list['format']['wide'] as $name) {
            $daysList[$i] = $name;
            $i++;
        }

        $select = "<select multiple='multiple' name='skip_days[]'>";

        $array = (array) $this->getData('extra_data/skip_days');

        foreach ($daysList as $key => $value) {
            $selected = '';

            if (in_array($key, $array)) {
                $selected = ' selected="selected" ';
            }

            $select .= "<option $selected value='{$key}'>{$value}</option>";
        }

        $select .= "</select>";

        $class = $this->getData('chain_id') ? 'edit' : 'submit';
        $label = $this->getData('chain_id') ? 'Edit' : 'Add';
        $form = $this->getData('chain_id') ? 'edit_data' : 'add_data';
        $img = $this->template->getViewFileUrl('Licentia_Panda::images/close.png');

        $t = function ($t) {

            return __($t);
        };

        $return = <<<EOL
        <span class="formTriggersWrapper">
        <form class="$form" id="formTriggers" method="post" action="">
        <img class="close" src="$img" />
        <label>{$t('Days')}</label>
        <input type="text" class="required digits" value="{$this->getData(
            'extra_data/days'
        )}" name="days" placeholder="{$t('Days to Wait')}">
        <label>{$t('Hours')}</label>
        <input type="text" class="required digits" name="hours" value="{$this->getData(
            'extra_data/hours'
        )}" placeholder="{$t('Hours to Wait')}">
        <label>{$t('Minutes')}</label>
        <input type="text" class="required digits" name="minutes" value="{$this->getData(
            'extra_data/minutes'
        )}" placeholder="M{$t('inutes to Wait')}">
        <label>{$t('Skip Days')}</label>
        $select
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

        return false;
    }
}
