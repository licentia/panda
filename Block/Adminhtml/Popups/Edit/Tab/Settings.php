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

namespace Licentia\Panda\Block\Adminhtml\Popups\Edit\Tab;

/**
 * Class Settings
 *
 * @package Licentia\Panda\Block\Adminhtml\Popups\Edit\Tab
 */
class Settings extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * Settings constructor.
     *
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {

        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('panda_popup');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );
        $fieldset = $form->addFieldset('params_fieldset', ['legend' => __('Settings for %1', $model->getTypeName())]);

        if ($model->getType() == 'modal') {
            $fieldset->addField(
                'opacity',
                "text",
                [
                    "label"    => __('Background Layer Opacity'),
                    "name"     => 'opacity',
                    "required" => true,
                    "note"     => __(
                        'Between 0 and 1 (decimals allowed, 1 fully opaque. 0 fully transparent), How opaque should de background page look like when the modal is displayed (not the actual floating %1)',
                        $model->getTypeName()
                    ),
                    'class'    => 'small_input validate-number validate-number-range number-range-0.1-1',
                ]
            );
            $html = '
                <script type="text/javascript">
                    require(["jquery","jquery/ui"],function ($){
                        $(function() {
                              $("#layer_background_color").attr("type","color");
                        });
                    });
                </script>
                ';

            $fieldset->addField(
                'layer_background_color',
                "text",
                [
                    "label" => __('Background Layer Color'),
                    "name"  => 'layer_background_color',
                    "note"  => __(
                        'Background Color for the back layer when the %1 is displayed.',
                        $model->getTypeName()
                    ),
                    'class' => 'small_input',
                    'value' => '#333333',
                ]
            )
                     ->setAfterElementHtml($html);
        }
        $html = '
                <script type="text/javascript">
                    require(["jquery","jquery/ui"],function ($){
                        $(function() {
                              $("#background_color").attr("type","color");
                        });
                    });
                </script>
                ';

        $fieldset->addField(
            'background_color',
            "text",
            [
                "label" => __('Content Background Color'),
                "name"  => 'background_color',
                "note"  => __(
                    'Background Color for the window that holds the content you added in the General Tab'
                ),
                'class' => 'small_input',
                'value' => '#FFFFFF',
            ]
        )
                 ->setAfterElementHtml($html);

        $fieldset->addField(
            'background_image',
            'image',
            [
                "label" => __('Content Background Image'),
                'name'  => 'background_image',
                'note'  => 'Allowed image types: jpg, jpeg, gif, png',
            ]
        );

        if ($model->getType() != 'sitebar') {
            $fieldset->addField(
                'border_width',
                "text",
                [
                    "label" => __('Border Thickness'),
                    "name"  => 'border_width',
                    "note"  => __(
                        'Thickness of the Border for the Wrapping layer where the %1 is displayed.',
                        $model->getTypeName()
                    ),
                    'class' => 'small_input validate-number validate-number-range number-range-0-10',
                ]
            );

            $fieldset->addField(
                'border_radius',
                "text",
                [
                    "label" => __('Border Radius'),
                    "name"  => 'border_radius',
                    "note"  => __(
                        'Border Radius of the Wrapping layer where the %1 is displayed.',
                        $model->getTypeName()
                    ),
                    'class' => 'small_input validate-number validate-number-range number-range-0-20',
                ]
            );

            $html = '
                <script type="text/javascript">
                    require(["jquery","jquery/ui"],function ($){
                        $(function() {
                              $("#border_color").attr("type","color");
                        });
                    });
                </script>
                ';

            $fieldset->addField(
                'border_color',
                "text",
                [
                    "label" => __('Border Color'),
                    "name"  => 'border_color',
                    "note"  => __(
                        'Border Color for the Wrapping layer when the %1 is displayed.',
                        $model->getTypeName()
                    ),
                    'class' => 'small_input',
                    'value' => '#DEDEDE',
                ]
            )
                     ->setAfterElementHtml($html);

            $html = '<script type="text/javascript">

                require(["jquery"],function ($){
                
                    toggleControlsValidateType = {
                        run: function() {
                            if($("#allow_close").val() == "0" ){
                                    $("div.admin__field.field.field-close_button").hide();
                             }else{
                                    $("div.admin__field.field.field-close_button").show();
                            }
                        }
                    }
                    
                    window.toggleControlsValidateType = toggleControlsValidateType;
                    
                    $(function() {
                        toggleControlsValidateType.run();
                    });
                    
                });
                </script>
         ';

            $fieldset->addField(
                'allow_close',
                "select",
                [
                    "onchange" => 'toggleControlsValidateType.run()',
                    "label"    => __('Allow Close'),
                    "options"  => ['1' => __('Yes'), '0' => __('No')],
                    "name"     => 'allow_close',
                    "note"     => __("If set to 'No', customers won't be able to close the %1", $model->getTypeName()),
                ]
            )
                     ->setAfterElementHtml($html);

            $fieldset->addField(
                'close_button',
                "select",
                [
                    "label"   => __('Show Close Button'),
                    "options" => ['1' => __('Yes'), '0' => __('No')],
                    "name"    => 'close_button',
                    "note"    => __(
                        'Show Close Button? hint: add a css class to an HTML element with the name "panda-popup_close" to close the %1 with a click on that element',
                        $model->getTypeName()
                    ),
                ]
            );

            $fieldset->addField(
                'after_scroll',
                "text",
                [
                    "label" => __('Display After Scroll %'),
                    "name"  => 'after_scroll',
                    'class' => 'small_input',
                    "note"  => __(
                        'After what % of the scrolling bar is moved down we show the %1? (0 to ignore)',
                        $model->getTypeName()
                    ),
                ]
            );

            $fieldset->addField(
                'after_time',
                "text",
                [
                    "label" => __('Display After X Seconds'),
                    "name"  => 'after_time',
                    'class' => 'small_input',
                    "note"  => __(
                        'After how many seconds the Visitor enters the page, should the %1 be displayed?',
                        $model->getTypeName()
                    ),
                ]
            );
        }

        $fieldset->addField(
            'styles',
            "text",
            [
                "label" => __('Additional CSS Styles'),
                "name"  => 'styles',
                "note"  => __('To be applied to the %1 wrapper', $model->getTypeName()),
            ]
        );

        $form->addValues($model->getData());

        $this->setForm($form);

        if (!$model->getId()) {
            $form->addValues(['allow_close' => '1']);
            $form->addValues(['opacity' => '0.7']);
            $form->addValues(['layer_background_color' => '#333333']);
        }

        return parent::_prepareForm();
    }
}
