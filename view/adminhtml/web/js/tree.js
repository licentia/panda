/*
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

define(['jquery', 'jquery/ui'], function ($) {

    $.widget('panda.tree', {
        options: {
            'add_option': true,
            'edit_option': true,
            'url': '',
            'img_url': '',
            'delete_option': true,
            'confirm_before_delete': true,
            'animate_option': [true, 1],
            'align_option': 'center',
            'draggable_option': true
        },

        tree: function () {

            var edit_option = this.options['edit_option'];
            var delete_option = this.options['delete_option'];
            var confirm_before_delete = this.options['confirm_before_delete'];
            var animate_option = this.options['animate_option'];
            var align_option = this.options['align_option'];
            var draggable_option = this.options['draggable_option'];
            var vertical_line_text = '<span class="vertical"></span>';
            var horizontal_line_text = '<span class="horizontal"></span>';
            var edit_action_text = edit_option == true ? '<span class="edit_action" title="Click to Edit"></span>' : '';
            var delete_action_text = delete_option == true ? '<span class="delete_action" title="Click to Delete"></span>' : '';
            var class_name = 'tree';
            var event_name = 'pageload';
            if (align_option != 'center') {
                $('.' + class_name + ' li').css({'text-align': align_option});
            }

            function prepend_data(target) {
                target.prepend(vertical_line_text + horizontal_line_text).children('div.editable').prepend(delete_action_text + edit_action_text);
            }

            function draw_line(target) {
                var tree_offset_left = $('.' + class_name + '').offset().left;
                tree_offset_left = parseInt(tree_offset_left, 10);

                var child_width = target.children('div').outerWidth(true) / 2;

                var child_left = target.children('div').offset().left;
                if (target.parents('li').offset() != null) {
                    var parent_child_height = target.parents('li').offset().top;
                }
                vertical_height = (target.offset().top - parent_child_height) - target.parents('li').children('div').outerHeight(true) / 2;
                target.children('span.vertical').css({
                    'height': vertical_height,
                    'margin-top': -vertical_height,
                    'margin-left': child_width,
                    'left': child_left - tree_offset_left
                });
                if (target.parents('li').offset() == null) {
                    var width = 0;
                } else {
                    var parents_width = target.parents('li').children('div').offset().left + (target.parents('li').children('div').width() / 2);
                    var current_width = child_left + (target.children('div').width() / 2);
                    var width = parents_width - current_width;
                }
                var horizontal_left_margin = width < 0 ? -Math.abs(width) + child_width : child_width;
                target.children('span.horizontal').css({
                    'width': Math.abs(width),
                    'margin-top': -vertical_height,
                    'margin-left': horizontal_left_margin,
                    'left': child_left - tree_offset_left
                });
            }

            if (animate_option[0] == true) {
                function animate_call_structure() {
                    $timeout = setInterval(function () {
                        animate_li();
                    }, animate_option[1]);
                }

                var length = $('.' + class_name + ' li').length;
                var i = 0;

                function animate_li() {
                    prepend_data($('.' + class_name + ' li').eq(i));
                    draw_line($('.' + class_name + ' li').eq(i));
                    i++;
                    if (i == length) {
                        i = 0;
                        clearInterval($timeout);
                    }

                }
            }

            function call_structure() {
                $('.' + class_name + ' li').each(function () {
                    if (event_name == 'pageload') {
                        prepend_data($(this));
                    }
                    draw_line($(this));
                });
            }

            function over_actions() {
                $('.' + class_name + ' li > div').hover(function (event) {
                    if (event.type == 'mouseenter' || event.type == 'mouseover') {
                        $('.' + class_name + ' li > div.current').removeClass('current');
                        $(this).addClass('current');
                        $(this).children('span.delete_action, span.edit_action').show();
                    } else {
                        $(this).removeClass('current');
                        $(this).children('span.delete_action, span.edit_action').hide();
                    }
                });
            }

            over_actions();

            animate_option[0] ? animate_call_structure() : call_structure();
            event_name = 'others';
            $(window).resize(function () {
                call_structure();
            });

            function find_parent(_this) {
                if (_this.length > 0) {
                    _this.children('div').addClass('parent');
                    _this = _this.closest('li').closest('ul').closest('li');
                    return find_parent(_this);
                }
            }

            if (edit_option) {
                $(document).on("click", '.' + class_name + ' span.edit_action', function () {
                    $('#loading-mask').show();
                    if ($('form.add_data').length > 0) {
                        $('form.add_data').remove();
                    }
                    if ($('form.edit_data').length > 0) {
                        $('form.edit_data').remove();
                    }
                    var edit_string = $(this).closest('div').clone();
                    if (edit_string.children('span.delete_action').length > 0) {
                        edit_string.children('span.delete_action').remove();
                    }
                    if (edit_string.children('span.add_action').length > 0) {
                        edit_string.children('span.add_action').remove();
                    }
                    if (edit_string.children('span.edit_action').length > 0) {
                        edit_string.children('span.edit_action').remove();
                    }
                    var edit_ele_id = $(this).closest("div").attr("id");
                    var _this = $(this);
                    var data = "op=editform&id=" + edit_ele_id + "";
                    $.ajax({
                        type: 'GET',
                        url: panda_url_action,
                        data: data,
                        success: function (data) {
                            $('#loading-mask').hide();
                            var editquery = data;
                            if (_this.closest('div').children('form.edit_data').length == 0) {
                                _this.closest('div').append(editquery);

                                if ((_this.closest('div').children('form').position().top + _this.closest('div').children('form').outerHeight()) > $(window).height()) {
                                    _this.closest('div').children('form').css({
                                        'margin-top': -_this.closest('div').children('form').outerHeight()
                                    });
                                }
                                if ((_this.closest('div').children('form').offset().left + _this.closest('div').children('form').outerWidth()) > $(window).width()) {
                                    _this.closest('div').children('form').css({
                                        'margin-left': -_this.closest('div').children('form').outerWidth()
                                    });
                                }
                                _this.closest('div').closest('li').closest('ul').children('li').children('div').addClass('zindex');
                            }

                        }
                    });
                    $(document).on("click", "input.edit", function (event) {
                        $('#loading-mask').show();
                        var form = $("#formTriggers");
                        if (form) {
                            form.validate();
                            if (!form.valid()) {
                                return false;
                            }
                        }
                        var _editthis = $(this);
                        event.preventDefault();
                        var data = "op=edit&id=" + _editthis.closest('div').attr('id') + "&";
                        data += _editthis.closest('form').serialize();
                        $.ajax({
                            type: 'GET',
                            url: panda_url_action,
                            data: data,
                            success: function (data) {
                                $(document).off("click", "input.edit");
                                var element_target = _editthis.closest('form.edit_data').closest('div');
                                edit_html = "<span class='name'>" + data + "</span>";
                                element_target.children('span.edit_action').nextAll().remove();
                                if (element_target.text().length > 0) {
                                    element_target.html(element_target.html().replace(element_target.text(), ''));
                                }
                                element_target.append(edit_html);
                                element_target.children('span.delete_action, span.edit_action').hide();
                                $('li > div.zindex').removeClass('zindex');
                                call_structure();
                                $('#loading-mask').hide();
                            }
                        });
                    });
                    $(document).on("click", "img.close", function () {
                        $(this).closest('form.edit_data').closest('div').children('span.delete_action, span.edit_action').hide();
                        $(this).closest('form.edit_data').remove();
                        $('li > div.zindex').removeClass('zindex');
                    });
                });
            }
            if (delete_option) {
                $(document).on("click", '.' + class_name + ' span.delete_action', function (url) {
                    var _deletethis = $(this);
                    var target_element = $(this).closest('li').closest('ul').closest('li');
                    confirm_message = 1;
                    if (confirm_before_delete) {
                        var confirm_text = "Remove Action?";
                        confirm_message = confirm(confirm_text);
                    }
                    if ($(this).closest('div').attr('id') == 1) {
                        alert("You cant delete root person");
                    } else {
                        if (confirm_message) {
                            $('#loading-mask').show();
                            $(this).closest('li').addClass('ajax_delete_all');
                            ajax_delete_id = Array();
                            ajax_delete_id.push($(this).closest('div').attr('id'));

                            total_lis = $(this).closest('div').parent('li').children('ul')
                                .children('li').length;

                            if (total_lis > 1) {
                                $('.ajax_delete_all li').each(function () {
                                    ajax_delete_id.push($(this).children('div').attr('id'));
                                });
                            }
                            $(this).closest('li').removeClass('ajax_delete_all');
                            var data = "op=delete&id=" + ajax_delete_id + "";
                            $.ajax({
                                type: 'GET',
                                url: panda_url_action,
                                data: data,
                                success: function (data) {
                                    $('#loading-mask').hide();
                                    if (total_lis > 1) {
                                        _deletethis.closest('li').fadeOut().remove();
                                    } else {
                                        _deletethis.closest('div').parent().children('span').fadeOut().remove();
                                        _deletethis.closest('div').fadeOut().remove();
                                    }
                                    call_structure();
                                    if (target_element.children('ul').children('li').length == 0) {
                                        target_element.children('ul').remove();
                                    }
                                }
                            });
                        }
                    }
                });
            }
            if (draggable_option) {
                function draggable_event() {
                    droppable_event();
                    $('div.draggable').draggable({
                        cursor: 'move',
                        zIndex: 5,
                        revert: true,
                        revertDuration: 100,
                        start: function (event, ui) {
                            $('li.li_children').removeClass('li_children');
                            $(this).closest('li').addClass('li_children');
                        },
                        stop: function (event, ul) {
                            droppable_event();
                        }
                    });
                }

                function droppable_event() {
                    $('.' + class_name + ' div.div_droppable').droppable({
                        accept: 'div.draggable',
                        hoverClass: "hoverClass",
                        activeClass: "activeClass",
                        drop: function (event, ui) {
                            $('#loading-mask').show();
                            $('div.check_div').removeClass('check_div');
                            $('.li_children div').addClass('check_div');
                            if ($(this).hasClass('check_div')) {
                                alert('Cant Move on Child Element.');
                            } else {
                                if ($('form.add_data').length > 0) {
                                    $('form.add_data').remove();
                                }
                                if ($('form.edit_data').length > 0) {
                                    $('form.edit_data').remove();
                                }
                                var _this = $(this);
                                var data = "op=renderAdd&parentid=" + $(this).closest('div').attr('id') + "&type=" + $(ui.draggable[0]).attr('id');
                                $.ajax({
                                    type: 'GET',
                                    url: panda_url_action,
                                    data: data,
                                    success: function (data) {
                                        $('#loading-mask').hide();
                                        var addquery = data;
                                        if (_this.closest('div').children('form.add_data').length == 0) {
                                            _this.append(addquery);
                                            if ((_this.closest('div').children('form').position().top + _this.closest('div').children('form').outerHeight()) > $(window).height()) {
                                                _this.closest('div').children('form').css({
                                                    'margin-top': -_this.closest('div').children('form').outerHeight()
                                                });
                                            }
                                            if ((_this.closest('div').children('form').offset().left + _this.closest('div').children('form').outerWidth()) > $(window).width()) {
                                                _this.closest('div').children('form').css({'margin-left': -_this.closest('div').children('form').outerWidth()});
                                            }
                                            _this.closest('div').closest('li').closest('ul').children('li').children('div').addClass('zindex');
                                        }

                                    }
                                });
                                $(document).on("click", "input.submit", function (event) {
                                    var form = $("#formTriggers");
                                    if (form) {
                                        form.validate();
                                        if (!form.valid()) {
                                            return false;
                                        }
                                    }

                                    $('#loading-mask').show();
                                    var _addthis = $(this);
                                    event.preventDefault();
                                    var parentid = _addthis.closest('div').attr('id');
                                    var data = "op=add&parentid=" + parentid + "&type=" + $(ui.draggable[0]).attr('id') + "&";
                                    data += _addthis.closest('form').serialize();
                                    $.ajax({
                                        type: 'GET',
                                        url: panda_url_action,
                                        data: data,
                                        success: function (data) {
                                            $(document).off("click", "input.submit");
                                            _addthis.closest('li').children('ul').length > 0 ? _addthis.closest('li').find('ul:first').prepend('<ul>' + data + '</ul>') : _addthis.closest('li').append('<ul>' + data + '</ul>');
                                            _addthis.closest('form.add_data').closest('div').children('span.delete_action, span.edit_action').hide();
                                            _addthis.closest('form.add_data').remove();
                                            $('li > div.zindex').removeClass('zindex');
                                            $('#loading-mask').hide();
                                            event_name = 'pageload';
                                            call_structure();
                                            event_name = 'others';
                                            draggable_event();
                                            over_actions();

                                        }
                                    });
                                });
                                $(document).on("click", "img.close", function () {
                                    $(this).closest('form.add_data').closest('div').children('span.delete_action, span.edit_action').hide();
                                    $(this).closest('form.add_data').remove();
                                    $('li > div.zindex').removeClass('zindex');
                                });
                            }
                        }
                    });
                }

                $('.' + class_name + ' li > div').disableSelection();
                draggable_event();
            }
        }
    });

    return $.panda.tree;
});