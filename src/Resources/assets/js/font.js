/*!
 * font.js v0.0.1
 * https://github.com/GetOlympus/olympus-dionysos-field-font
 *
 * This plugin updates font canvas preview.
 *
 * Example of JS:
 *      $('.font').dionysosFont({
 *          color: '.input-color',                 // node element which contains color input
 *          family: '.input-family',               // node element which contains family input
 *          letterspacing: '.input-letterspacing', // node element which contains letterspacing input
 *          lineheight: '.input-lineheight',       // node element which contains lineheight input
 *          size: '.input-size',                   // node element which contains size input
 *          subset: '.input-subset',               // node element which contains subset input
 *          variant: '.input-variant',             // node element which contains variant input
 *
 *          ajax: 'font-identifier',               // WordPress ajax call action
 *          canvas: '.canvas p',                   // node element which contains preview canvas
 *          mode: 'googlefonts',                   // way to build dynamic CSS stylesheets
 *          settings: {
 *              color: {}                          // options to build wpColor object
 *          }
 *      });
 *
 * Example of HTML:
 *      <div id="font-identifier" class="font">
 *          <input type="hidden" name="ctm" value="" />
 *
 *          <select name="ctm[family]" class="input-family"></select>
 *          <input type="text" name="ctm[backup]" value="" class="input-backup" />
 *          <select name="ctm[subset]" class="input-subset"></select>
 *          <select name="ctm[variant]" class="input-variant"></select>
 *          <input type="text" name="ctm[size]" value="" class="input-size" />
 *          <input type="text" name="ctm[lineheight]" value="" class="input-lineheight" />
 *          <input type="text" name="ctm[letterspacing]" value="" class="input-letterspacing" />
 *          <input type="text" name="ctm[color]" value="" class="input-color" />
 *
 *          <div class="canvas">
 *              <label>Preview:</label>
 *              <p>My preview text</p>
 *          </div>
 *      </div>
 *
 * Copyright 2016 Achraf Chouk
 * Achraf Chouk (https://github.com/crewstyle)
 */

(function ($){
    "use strict";

    /**
     * Constructor
     * @param {nodeElement} $el
     * @param {array}       options
     */
    var Font = function ($el,options){
        // vars
        var _this = this;

        _this.$el = $el;
        _this.options = options;

        // update settings
        _this.settings = _this.options.settings;

        // update elements list
        _this.$family = _this.$el.find(_this.options.family);
        _this.$subset = _this.$el.find(_this.options.subset);
        _this.$variant = _this.$el.find(_this.options.variant);
        _this.$size = _this.$el.find(_this.options.size);
        _this.$lineheight = _this.$el.find(_this.options.lineheight);
        _this.$letterspacing = _this.$el.find(_this.options.letterspacing);
        _this.$color = _this.$el.find(_this.options.color);
        _this.$canvas = _this.$el.find(_this.options.canvas);

        // use wpColor
        _this.$color.wpColorPicker($.extend(_this.settings.color, {
            change: function (e,ui){
                _this.update_canvas('color', ui.color.toString());
            },
            clear: function (e){
                _this.update_canvas('color', '#000000');
            }
        }));

        // bind family event
        _this.$family.on('change', $.proxy(_this.update_subset_variant, _this));

        // bind other events
        $([_this.$size, _this.$lineheight, _this.$letterspacing]).each(function (){
            $(this).on('keyup', _this.delay($.proxy(_this.input_keyup, _this), 500));
        });
        $([_this.$family, _this.$subset, _this.$variant]).each(function (){
            $(this).on('change', $.proxy(_this.select_change, _this));
        });

        // initialize preview
        _this.update_subset_variant();
        _this.update_canvas();
    };

    /**
     * @type {nodeElement}
     */
    Font.prototype.$canvas = null;

    /**
     * @type {nodeElement}
     */
    Font.prototype.$color = null;

    /**
     * @type {nodeElement}
     */
    Font.prototype.$el = null;

    /**
     * @type {nodeElement}
     */
    Font.prototype.$family = null;

    /**
     * @type {nodeElement}
     */
    Font.prototype.$letterspacing = null;

    /**
     * @type {nodeElement}
     */
    Font.prototype.$lineheight = null;

    /**
     * @type {array}
     */
    Font.prototype.options = null;

    /**
     * @type {Object}
     */
    Font.prototype.settings = null;

    /**
     * @type {nodeElement}
     */
    Font.prototype.$subset = null;

    /**
     * @type {nodeElement}
     */
    Font.prototype.$variant = null;

    /**
     * Delay before event
     * @param {callback} func
     * @param {int}      ms
     */
    Font.prototype.delay = function (func,ms){
        var _timer = 0;

        return function (){
            var _context = this,
                _args = arguments;

            clearTimeout(_timer);
            _timer = setTimeout(function (){
                func.apply(_context,_args);
            }, ms || 0);
        };
    };

    /**
     * Keyup event on inputs
     * @param {event} e
     */
    Font.prototype.input_keyup = function (e){
        e.preventDefault();
        var _this = this;

        var $input = $(e.target || e.currentTarget),
            _attr = $input.attr('data-style'),
            _val = $input.val();

        // update canvas css
        if (typeof _attr !== typeof undefined && _attr !== false) {
            _this.update_canvas(_attr, _val);
        }
    };

    /**
     * Change event on selects
     * @param {event} e
     */
    Font.prototype.select_change = function (e){
        e.preventDefault();
        var _this = this;

        var $select = $(e.target || e.currentTarget),
            _attr = $select.attr('data-style'),
            _val = $select.children('option:selected').val();

        // update canvas css
        _this.update_canvas(_attr, _val);
    };

    /**
     * Update canvas after keyup / change events
     * @param {string} attribute
     * @param {string} value
     */
    Font.prototype.update_canvas = function (attribute,value){
        var _this = this;

        // check vars
        if (2 === arguments.length && '' !== value) {
            _this.$canvas.css(attribute, value);
        } else {
            // inputs
            $([_this.$backup, _this.$size, _this.$lineheight, _this.$letterspacing, _this.$color]).each(function (){
                var $self = $(this);
                _this.$canvas.css($self.attr('data-style'), $self.val());
            });
            // selects
            $([_this.$family, _this.$subset, _this.$variant]).each(function (){
                var $self = $(this),
                    _attr = $self.attr('data-style');

                _this.$canvas.css(_attr, $self.children('option:selected').val());
            });
        }

        // get stylesheet link
        var $link = _this.$canvas.find('link'),
            _family = _this.$family.children('option:selected').val().replace(/ /g, '+');

        // check link
        if ($link.length) {
            $link.remove();
        }

        // Google fonts mode
        if ('googlefonts' === _this.options.mode && '' !== _family) {
            var _url = 'https://fonts.googleapis.com/css?family=',
                _variant = _this.$variant.children('option:selected').val(),
                _subset = _this.$subset.children('option:selected').val();

            // build all together
            _url += _family;
            _url += '' === _variant ? '' : ':' + _variant;
            _url += '' === _subset ? '' : '&' + _subset;

            // add link
            _this.$canvas.append('<link rel="stylesheet" href="' + _url + '">');
        }
    };

    /**
     * Update subset and variant depending on family
     */
    Font.prototype.update_subset_variant = function (){
        var _this = this,
            _family = _this.$family.children('option:selected').val();

        // check family
        if ('' === _family) {
            return;
        }

        // make the AJAX request
        $.ajax(ajaxurl, {
            type: 'POST',
            data: {
                action: _this.options.ajax,
                family: _this.$family.children('option:selected').val(),
                mode: _this.options.mode
            },
            dataType: 'json'
        }).done(function (x){
            // no results
            if (!x.success) {
                return;
            }

            var _data = JSON.parse(x.data),
                _subset = '',
                _variant = '';

            // works on subset
            _this.$subset.html('');
            $.each(_data.subsets, function (idx,s){
                _this.$subset.append('<option value="' + s + '">' + s + '</option>');
            });

            // works on variant
            _this.$variant.html('');
            $.each(_data.variants, function (idx,v){
                _this.$variant.append('<option value="' + v + '">' + v + '</option>');
            });

            _this.update_canvas();
        });
    };

    var methods = {
        init: function (options){
            if (!this.length) {
                return false;
            }

            var settings = {
                color: '.input-color',
                family: '.input-family',
                letterspacing: '.input-letterspacing',
                lineheight: '.input-lineheight',
                size: '.input-size',
                subset: '.input-subset',
                variant: '.input-variant',

                ajax: 'font-identifier',
                canvas: '.canvas p',
                mode: 'googlefonts',
                settings: {
                    color: {
                        "defaultColor": true,
                        "hide": true,
                        "palettes": true,
                        "width": 255,
                        "mode": "hsv",
                        "type": "full",
                        "slider": "horizontal"
                    }
                }
            };

            return this.each(function (){
                if (options) {
                    $.extend(settings, options);
                }

                new Font($(this), settings);
            });
        }
    };

    $.fn.dionysosFont = function (method){
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method '+method+' does not exist on dionysosFont');
            return false;
        }
    };
})(window.jQuery);
