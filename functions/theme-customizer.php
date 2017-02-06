<?php
if (class_exists('WP_Customize_Control')) {

    // Gradient Control
    class WP_Customize_Gradient_Control extends WP_Customize_Control {

        public $type = 'gradient';
        public $style = '';

        public function enqueue() {
            wp_register_script('theme_customizer', get_template_directory_uri() . '/js/theme_customizer.js');
            wp_enqueue_script('theme_customizer');

            wp_enqueue_script('farbtastic');
            wp_enqueue_style('farbtastic');
        }

        public function to_json() {
            parent::to_json();
        }

        public function _value() {
            $val = parent::value();

            if (preg_match('@^#[0-9A-F]+\|#[0-9A-F]+@i', $val))
                return $val;
            preg_match('@top, (#[0-9A-F]+) 0%,(#[0-9A-F]+) 100%@i', $val, $m);

            return isset($m[2]) ? $m[1] . '|' . $m[2] : '';
        }

        public function render_content() {
            $name = '_customize-gradient-' . $this->id;
            global $wp_customize;
            ?>    
            <style type="text/css">
                .gradient .customize-control-title, .gradient .color-picker {xfloat: right; display: block;}
                .gradient .customize-control-title, .gradient .color-picker span {xfloat: left;}
                .gradient {clear: both;}
                .gradient a {
                    float: left;
                    margin-top: 0px;
                    line-height: 28px;
                    display: block;
                    background-color: #FFFFFF;
                    border: 3px solid rgba(0, 0, 0, 0.1);
                    border-radius: 3px 3px 3px 3px;
                    height: 20px;          
                    width: 40px;
                    margin-right: 5px;
                }
                a.transparent {background-image: url(<?php echo get_template_directory_uri(); ?>/images/pseudo_transparent.png);}
                .customize-section .gradient .color-picker span a {display: inline; border: 0px; width: auto; background: transparent;}
                .inactive {opacity: 0.3; filter: alpha(opacity = 30);}
                .gradient .farbtastic-placeholder {clear: left;}
                #gradient-<?php echo $this->id; ?> {width: 62px; border-color: #E5E5E5;}
                .gradient .color-picker-controls {margin-top: 30px; display: none;}
                .gradient .color-picker-controls a:first-child {margin-left: 140px; float: left;}
                .gradient .color-picker-hex input {width: 60px;}
            </style>
            <label class="gradient">
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <div class="color-picker">
                    <input type="hidden" value="<?php echo esc_attr($this->_value()); ?>" id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>"  rel="<?php echo esc_attr($this->settings['default']->id); ?>" autocomplete="off" />
                    <a href="#" id="gradient-<?php echo $this->id; ?>" class="transparent"></a> <span><a href="#" rel="delete_gradient" title="<?php _e('Reset gradient', 'pureness'); ?>" class="inactive"><img src="<?php echo get_template_directory_uri(); ?>/images/icons/reset.png" alt="<?php _e('Reset gradient', 'pureness'); ?>"></a></span>
                </div>
                <div class="color-picker-controls">
                    <a href="#" rel="1" class="transparent"></a> <a href="#" rel="2" class="transparent"></a>
                    <div class="farbtastic-placeholder"></div>
                    <div class="color-picker-details">
                        <div class="color-picker-hex">
                            <span>#</span>
                            <input type="text" <?php //$this->link();  ?> />
                        </div>
                    </div>
                </div>
            </label>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    var f = jQuery.farbtastic('#customize-control-<?php echo $this->id; ?> .gradient .farbtastic-placeholder');
                    jQuery('#gradient-<?php echo $this->id; ?>').click(function() {
                        jQuery('#customize-control-<?php echo $this->id; ?> .gradient .color-picker-controls').toggle();
               
                        //f.linkTo(jQuery('#customize-control-<?php echo $this->id; ?> .gradient .color-picker-controls a:first'));
                        jQuery('#customize-control-<?php echo $this->id; ?> .gradient .color-picker-controls a:first').focus();
                    });
                    jQuery('#customize-control-<?php echo $this->id; ?> .gradient a[rel!=delete_gradient]:gt(0)').focus(function() {
                        var $this = jQuery(this);
                        var colors = jQuery('#<?php echo esc_attr($name); ?>').val().split('|');              
                        if (colors.length == 2) {
                            jQuery('#customize-control-<?php echo $this->id; ?> .gradient input:text').val(colors[$this.attr('rel')-1].replace('#', ''));
                        }
                        f.linkTo(function(color) {
                            jQuery('#gradient-<?php echo $this->id; ?>').removeClass('transparent');
                            jQuery('#customize-control-<?php echo $this->id; ?> .gradient a[rel=delete_gradient]').removeClass('inactive');
                            if (colors.length == 1) colors = new Array('#FFFFFF', '#FFFFFF');
                            colors[$this.attr('rel')-1] = color;
                            jQuery('#<?php echo esc_attr($name); ?>').val(colors.join('|'));
                            jQuery('#customize-control-<?php echo $this->id; ?> .gradient input:text').val(color.replace('#', ''));

                            pure_update_colors_customizer($this, 'gradient');
                        });
                        if (colors.length == 2) {
                            f.setColor(colors[$this.attr('rel')-1]);
                        }
                        
                    });
                    jQuery('#customize-control-<?php echo $this->id; ?> .gradient input:text').keyup(function() {
                        f.setColor('#'+this.value);
                    });
                    jQuery('#<?php echo esc_attr($name); ?>')
                    .data('style', '<?php echo $this->style; ?>')
                    .data('css_type', '<?php echo $this->css_type; ?>');
                    jQuery('#customize-control-<?php echo $this->id; ?> .gradient a[rel=delete_gradient]').click(function() {
                        jQuery('#<?php echo esc_attr($name); ?>').val("");
                        jQuery('#customize-control-<?php echo $this->id; ?> .gradient input:text').val("");
                        jQuery('#customize-control-<?php echo $this->id; ?> .gradient input:hidden').val("");
                        jQuery('iframe').contents().find('#gradient-<?php echo $this->id; ?>-style').remove();
                        jQuery('#gradient-<?php echo $this->id; ?>-style').remove();
                        jQuery('#customize-control-<?php echo $this->id; ?> .color-picker-controls a[rel]').css({backgroundColor: 'transparent'}).addClass('transparent');
                        jQuery(this).addClass('inactive');
                        jQuery('#customize-control-<?php echo $this->id; ?> a[rel!=delete_gradient]').addClass('transparent');
                        pure_update_colors_customizer(jQuery('#customize-control-<?php echo $this->id; ?> .gradient a[rel!=delete_gradient]:gt(0)'), 'gradient');
                    })
            <?php if ($this->_value()) { ?>
                            jQuery(document).ready(function() {
                                jQuery('#customize-control-<?php echo $this->id; ?> .gradient a[rel!=delete_gradient]:gt(0)').focus();
                            });
            <?php } ?>
                    });
            </script>
            <?php
        }

    }

    // Color Control, it appends js actions to WP control
    class WP_Customize_Color_Extended_Control extends WP_Customize_Control {

        public $type = 'color_extended';
        public $css_type = 'background-color';
        public $style = '';

        public function enqueue() {
            wp_register_script('theme_customizer', get_template_directory_uri() . '/js/theme_customizer.js', false);
            wp_enqueue_script('theme_customizer');

            wp_enqueue_script('farbtastic');
            wp_enqueue_style('farbtastic');
        }

        public function _value() {
            $val = parent::value();
//var_dump(555, $val);            
            if (preg_match('@^#[0-9A-F]+@i', $val))
                return $val;
            preg_match('@' . $this->css_type . ': ([^;]+);@', $val, $m);
            return isset($m[1]) ? $m[1] : '';
        }

        public function to_json() {
            parent::to_json();

            /*
              $this->json['removed'] = $this->removed;

              if ( $this->context )
              $this->json['context'] = $this->context;
             */
        }

        public function render_content() {
            $name = '_customize-color-extended-' . $this->id;
            ?>    
            <style type="text/css">
                #customize-control-<?php echo $this->id; ?> .colorextended .color-picker span a {display: inline; border: 0px; width: auto; background: transparent;}
                #customize-control-<?php echo $this->id; ?> .customize-control-title, #customize-control-<?php echo $this->id; ?> .color-picker {xfloat: right; display: block;}
                #customize-control-<?php echo $this->id; ?> a {
                    float: left;
                    line-height: 28px;
                    display: block;
                    background-color: #FFFFFF;
                    border: 3px solid rgba(0, 0, 0, 0.1);
                    border-radius: 3px 3px 3px 3px;
                    height: 20px;          
                    width: 60px;
                    margin-right: 5px;
                    background-image: url(<?php echo get_template_directory_uri(); ?>/images/pseudo_transparent.png);
                }
                #customize-control-<?php echo $this->id; ?> .customize-control-title {xfloat: left;}
                #customize-control-<?php echo $this->id; ?> .color-picker-controls {clear: both; display: none;}
                #customize-control-<?php echo $this->id; ?> .color-picker-hex input {width: 60px;}

            </style>
            <label class="colorextended">
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <div class="color-picker">
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr(preg_replace('/.*#/', '#', $this->_value())); ?>"  rel="<?php echo esc_attr($this->settings['default']->id); ?>" autocomplete="off" />
                    <a href="#"></a> <span><a href="#" rel="delete_gradient" title="<?php _e('Reset color', 'pureness'); ?>" class="inactive"><img src="<?php echo get_template_directory_uri(); ?>/images/icons/reset.png" alt="<?php _e('Reset color', 'pureness'); ?>"></a></span> 
                    <div class="color-picker-controls">
                        <div class="farbtastic-placeholder"></div>
                        <div class="color-picker-details">
                            <div class="color-picker-hex">
                                <span>#</span>
                                <input type="text" <?php if (($val = $this->_value())) { ?>value="<?php echo substr($val, 1); ?>"<?php } ?> />
                            </div>
                        </div>
                    </div>
                </div>
            </label>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    var f = jQuery.farbtastic('#customize-control-<?php echo $this->id; ?> .farbtastic-placeholder');
                    var dolink = function(color) {
                        var $this = jQuery('#customize-control-<?php echo $this->id; ?> .color-picker a[rel!=delete_gradient]');
                        jQuery("#<?php echo esc_attr($name); ?>").val(color);
                        $this.css({backgroundColor: color, backgroundImage: 'none'});
                        jQuery('#customize-control-<?php echo $this->id; ?> .colorextended a[rel=delete_gradient]').removeClass('inactive');
                        jQuery('#customize-control-<?php echo $this->id; ?> input:text').val(color.replace('#', ''));
                        jQuery('iframe').contents().find('#color-extended-<?php echo $this->id; ?>-style').remove();
                        jQuery('#color-extended-<?php echo $this->id; ?>-style').remove();
                        var style = '<?php echo $this->css_type; ?>: '+color+';\n';
                        var css = '<style id="color-extended-<?php echo $this->id; ?>-style" type="text/css" >';
                        css += '<?php echo $this->style; ?> {\n';
                        css += style;
                        css += '} </style>';
                        //              jQuery('iframe').contents().find('head').append(css);
                        pure_update_colors_customizer($this, 'colorextenden');

                    }
                    jQuery('#customize-control-<?php echo $this->id; ?> .color-picker a[rel!=delete_gradient]').click(function() {
                        jQuery('#customize-control-<?php echo $this->id; ?> .color-picker-controls').toggle();
                        jQuery(this).focus();
                    }).focus(function() {
                        f.linkTo(dolink);
                    });
                    jQuery('#customize-control-<?php echo $this->id; ?> input:text').bind('change keyup', function() {
                        f.setColor('#'+this.value);
                    });
                    jQuery('#<?php echo esc_attr($name); ?>')
                    .data('style', '<?php echo $this->style; ?>')
                    .data('css_type', '<?php echo $this->css_type; ?>');
                    jQuery('#customize-control-<?php echo $this->id; ?> .colorextended a[rel=delete_gradient]').click(function() {
                        jQuery(this).addClass('inactive');
                        jQuery('#<?php echo esc_attr($name); ?>').val("");
                        jQuery('#customize-control-<?php echo $this->id; ?> .colorextended input:text').val("");
                        jQuery('iframe').contents().find('#color-extended-<?php echo $this->id; ?>-style').remove();
                        jQuery('#color-extended-<?php echo $this->id; ?>-style').remove();
                        jQuery('#customize-control-<?php echo $this->id; ?> .color-picker a:first').css({backgroundColor: 'transparent', backgroundImage: 'url(<?php echo get_template_directory_uri(); ?>/images/pseudo_transparent.png)'});
                        pure_update_colors_customizer(jQuery('#customize-control-<?php echo $this->id; ?> .color-picker-controls'), 'colorextenden');

                    });
            <?php if (($val = preg_replace('/.*#/', '#', $this->_value()))) { ?>jQuery(document).ready(function() {f.setColor('<?php echo $val; ?>'); dolink('<?php echo $val; ?>'); });<?php } ?>
                    });
            </script>
            <?php
        }

    }

    // Patterns backgrounds
    class WP_Customize_Patterns_Control extends WP_Customize_Control {

        public $type = 'patterns';
        public $style = '';
        public $css_type = 'background-image';

        public function enqueue() {
            
        }

        public function to_json() {
            parent::to_json();
        }

        public function _value() {
            $val = urldecode(parent::value());

            if (preg_match('@^http.*@i', $val))
                return $val;
            preg_match('@url\("([^"]+)"\)@i', $val, $m);
            return isset($m[1]) ? 'url("' . $m[1] . '")' : '';
        }

        public function render_content() {
            $name = '_customize-patterns-' . $this->id;
            ?>    
            <style type="text/css">
                #customize-control-<?php echo $this->id; ?> .patterns_list div {width: 230px; border: 3px solid rgba(0,0,0,0.1); height: 30px; border-radius: 2px; margin-bottom: 5px; clear: both; cursor: pointer;}
                #customize-control-<?php echo $this->id; ?> .patterns_list {display: none; clear: both; padding-top: 10px;}
                #customize-control-<?php echo $this->id; ?> a {
                    float: left;
                    line-height: 28px;
                    display: block;
                    background-color: #FFFFFF;
                    border: 3px solid rgba(0, 0, 0, 0.1);
                    border-radius: 3px 3px 3px 3px;
                    height: 20px;          
                    width: 62px;
                    margin-right: 5px;
                    background-image: url(<?php echo get_template_directory_uri(); ?>/images/pseudo_transparent.png);
                }
                #customize-control-<?php echo $this->id; ?> .patterns span a {
                    background: none repeat scroll 0 0 transparent;
                    border: 0 none;
                    display: inline;
                    width: auto;
                }       
                #customize-control-<?php echo $this->id; ?> .patterns .customize-control-title {
                    display: block;
                }
            </style>

            <label class="patterns <?php echo $this->setting->id; ?>">
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <input type="hidden" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($this->_value()); ?>" rel="<?php echo esc_attr($this->settings['default']->id); ?>" autocomplete="off" />
                <a href="#" class="pattern_selector"></a> <span><a href="#" rel="delete_gradient" title="<?php _e('Reset pattern', 'pureness'); ?>" class="inactive"><img src="<?php echo get_template_directory_uri(); ?>/images/icons/reset.png" alt="<?php _e('Reset pattern', 'pureness'); ?>"></a></span>
                <div class="patterns_list">
                    <?php
                    $patterns = scandir(TEMPLATEPATH . '/images/patterns');
                    foreach ($patterns as $p) {
                        if (preg_match('/\.png/', $p)) {
                            echo '<div style="background-image:url(' . get_template_directory_uri() . '/images/patterns/' . $p . '); background-color: #ccc;"></div>';
                        }
                    }
                    ?>
                </div>
            </label>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('#customize-control-<?php echo $this->id; ?> a[rel!=delete_gradient]').click(function() {
                        jQuery('#customize-control-<?php echo $this->id; ?> .patterns_list').toggle();
                    });
                      
                    jQuery('#customize-control-<?php echo $this->id; ?> a[rel=delete_gradient]').click(function() {
                        var $this = jQuery(this);
                        jQuery('#customize-control-<?php echo $this->id; ?> a[rel=delete_gradient]').addClass('inactive');
                        $this.parents('label:first').find('.pattern_selector').css('backgroundImage', '');    
                        jQuery('#<?php echo esc_attr($name); ?>').val('');
                        pure_update_colors_customizer($this, 'pattern');
                    });

                    jQuery('#<?php echo esc_attr($name); ?>')
                    .data('style', '<?php echo $this->style; ?>')
                    .data('css_type', '<?php echo $this->css_type; ?>');
                      
                    jQuery('#customize-control-<?php echo $this->id; ?> .patterns_list div')
                    .each(function() {var $this = jQuery(this); $this.data('backgroundImage', $this.css('backgroundImage')); })
                    .click(function() {
                        jQuery('#customize-control-<?php echo $this->id; ?> a[rel=delete_gradient]').removeClass('inactive');
                        var $this = jQuery(this);
                        var $parent = $this.parents('label:first');
                        var $selector = $parent.find('a.pattern_selector');
                        jQuery('#<?php echo esc_attr($name); ?>').val($this.data('backgroundImage'));
                        //$selector.css('backgroundImage', $selector.css('backgroundImage').replace(/url\([^\)]+\)/, $this.css('backgroundImage')));            
                        $selector.css('backgroundColor', $this.css('backgroundColor'));     
                        $selector.css('backgroundImage', $this.css('backgroundImage'));     
                        pure_update_colors_customizer($this, 'pattern');

                    });
            <?php if ($this->_value()) { ?>jQuery(document).ready(function() {jQuery('#customize-control-<?php echo $this->id; ?> a[rel=delete_gradient]').removeClass('inactive');});<?php } ?>
                    });
            </script>
            <?php
        }

    }

    // Font Family Control
    class WP_Customize_Font_Family_Control extends WP_Customize_Control {

        public $type = 'font_radio';
        public $fonts = array();

        public function enqueue() {
            //wp_enqueue_script( 'wp-plupload' );
        }

        public function to_json() {
            parent::to_json();

            /*
              $this->json['removed'] = $this->removed;

              if ( $this->context )
              $this->json['context'] = $this->context;
             */
        }

        public function render_content() {
            if (empty($this->fonts))
                return;

            $name = '_customize-font-radio-' . $this->id;
            ?>
            <link href='http://fonts.googleapis.com/css?family=<?php echo implode('|', $this->fonts); ?>' rel='stylesheet' type='text/css'>
            <style type="text/css">
                input[type=button].reset_button {
                    background: none repeat scroll 0 0 #C90000;
                    border-color: #660000;
                    color: #FFFFFF;
                    padding: 6px 10px;
                }
            </style>
            <span class="customize-control-title"><?php echo $this->label; ?></span>
            <?php
            foreach ($this->fonts as $f) {
                ?>
                <label style="font-family: '<?php echo preg_replace('/(.*)(:.*)/', '$1', $f); ?>';">
                    <input type="radio" value="<?php echo $f; ?>" name="<?php echo esc_attr($name); ?>" <?php $this->link(); ?> <?php checked($this->value(), $f); ?> />
                <?php echo $f; ?><br/>
                </label>
                <?php
            }
            ?>
            <label><input type="button" value="<?php _e('Reset', 'pureness'); ?>" class="reset_button" /></label>
            <script type="text/javascript">
                jQuery('input.reset_button').click(function() {
                    jQuery('[data-customize-setting-link=<?php echo esc_attr($this->settings['default']->id); ?>]').each(function() {
                        var $this = jQuery(this);
                        if ($this.is(':radio')) $this.attr('checked', false);
                        if ($this.is(':text')) $this.val('').change();
                    });
                      
                });
            </script>
            <?php
        }

    }

    // Font Family Control
    class WP_Customize_Font_Size_Control extends WP_Customize_Control {

        public $type = 'font_size';

        public function enqueue() {
            //wp_enqueue_script( 'wp-plupload' );
        }

        public function to_json() {
            parent::to_json();

            /*
              $this->json['removed'] = $this->removed;

              if ( $this->context )
              $this->json['context'] = $this->context;
             */
        }

        public function render_content() {
            $name = '_customize-font-size-' . $this->id;
            ?>
            <span class="customize-control-title" style="clear: both"><?php echo $this->label; ?></span>
            <div id="font-slider"></div>
            <select class="font-size-combo" id="font-size-combo<?php echo $this->id; ?>" name="<?php echo esc_attr($name); ?>_measure">
                <option value="px">px</option>
                <option value="pt">pt</option>
                <option value="em">em</option>
                <option value="%">%</option>
            </select><br />
            <input type="text" name="<?php echo esc_attr($name); ?>" <?php /* $this->link(); */ ?> id="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($this->value()); ?>" />
            <style type="text/css">
                .customize-control select.font-size-combo {float: right; margin-top: -20px; min-width: 20px;}
                #font-slider {width: 180px; margin-top: 35px;}
                #customize-control-pure_font_size {width: 100%;}
            </style>  
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    function pure_change_font_size_value(v) {
                        var amount = v.replace(/(.*)(em|pt|px|%)/, '$1');
                        var units = v.replace(/(.*)(em|pt|px|%)/, '$2');
                        var min = 0;
                        var max = 10;
                        var steps = 1;
                        switch(units) {
                            case 'px':
                                min = 8;
                                max = 40;
                                steps = 1;
                                break;
                            case 'pt':
                                min = 5;
                                max = 40;
                                steps = 1;
                                break;
                            case 'em':
                                min = 5;
                                max = 40;
                                steps = 1;
                                amount *= 10;
                                break;
                            case '%':
                                min = 5;
                                max = 40;
                                steps = 1;
                                amount /= 10;
                                break;
                        }
                        pure_font_size_slider.slider(
                        "option", {"value": amount, "min": min, "max": max, "steps": steps})
                        .bind("slidestop", function(event, ui) {
                            var u = jQuery('#font-size-combo<?php echo $this->id; ?>').val();
                            var value = ui.value;
                            if (u=='em') {
                                value = ui.value/10;
                            } else if (u=='%') {
                                value = ui.value*10;
                            } 
                            jQuery('#<?php echo esc_attr($name); ?>').val(value+u).change();
                        }
                    );
                        jQuery('#font-size-combo<?php echo $this->id; ?>').val(units);
                    }

                    var pure_font_size_slider = jQuery('#font-slider').slider();
                    pure_change_font_size_value("<?php echo esc_attr($this->value()); ?>");
                    jQuery('#font-size-combo<?php echo $this->id; ?>').change(function() {
                        var v = jQuery('#<?php echo esc_attr($name); ?>').val();
                        var amount = v.replace(/(.*)(em|pt|px|%)/, '$1');
                        var units = v.replace(/(.*)(em|pt|px|%)/, '$2');          
                        var value = jQuery(this).val();
                        var em = amount;

                        if (value != units) {
                            // Convert to em
                            switch(units) {
                                case '%':
                                    em /= 100;
                                    break;
                                case 'px':
                                    em = parseInt(em*0.5/8*10)/10;
                                    break;
                                case 'pt':
                                    em = parseInt(em*0.5/6*10)/10;
                                    break;
                            }
                            // Convert to unit
                            switch(value) {
                                case '%':
                                    em *= 100;
                                    break;
                                case 'px':
                                    em = Math.round(em/0.5*8);
                                    break;
                                case 'pt':
                                    em = parseInt(em/0.5*6*10)/10;
                                    if (em*10%5 != 0) {
                                        em = Math.round(em);
                                    }
                                    break;
                            }

                            jQuery('#<?php echo esc_attr($name); ?>').val(em+value).change();
                            pure_change_font_size_value(em+value);
                        }
                    });
                    jQuery('#<?php echo esc_attr($name); ?>').change(function() {
                        pure_change_font_size_value(this.value);
                        wp.customize._value['<?php echo esc_attr($this->id); ?>'].set(this.value);
                        jQuery('iframe').contents().find('body').css({fontSize: this.value})
                    });
                });
            </script>
            <?php
        }

    }

    // Save As Option
    class WP_Customize_Save_As_Control extends WP_Customize_Control {

        public $type = 'save_as';

        public function enqueue() {
            //wp_enqueue_script( 'wp-plupload' );
        }

        public function to_json() {
            parent::to_json();
        }

        public function _value() {
            return get_option('pure_saved_designs');
        }

        public function render_content() {
            $name = '_customize-font-size-' . $this->id;
            ?>
            <label class="saveas <?php echo $this->setting->id; ?>">
                <p><strong><small><?php _e('By selecting one of these skins, you change your site design instantly', 'pureness'); ?></small></strong></p>
                <!--span class="customize-control-title"><?php echo esc_html($this->label); ?></span-->
                <input type="hidden" name="_<?php echo esc_attr($name); ?>" id="_<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($this->_value()); ?>" autocomplete="off" />
                <ul id="pure_save_saves">
            <?php if (!$this->_value()) { ?>
                        <li class="saveas_empty"><?php _e('There are not any customized options saved yet', 'pureness'); ?></li>
            <?php } ?>
                </ul>
            </label>
            <style type="text/css">
                #pure_new_customizing {width: 180px;}
                #pure_new_customizing.error {border: 2px solid #ac0b17;} 
            </style>
            <script type="text/javascript">
                var pure_saved_designs = <?php echo $this->_value() ? stripslashes($this->_value()) : '[]'; ?>;
                // Adding Save As action
                jQuery(document).ready(function () {
                    if (pure_saved_designs.length > 0) {
                        var html = '';
                        jQuery('li.saveas_empty').remove();
                        for(var i=0; i<pure_saved_designs.length; i++) {
                            html += '<li><a href="#" rel="'+(i)+'">'+pure_saved_designs[i].title+'</a></li>';
                        }
                        jQuery('#pure_save_saves').append(html);
                    }

                    jQuery('#customize-header-actions').append('<input type="text" id="pure_new_customizing" value="<?php _e('\\\'Save as\\\' new name...', 'pureness'); ?>" /> <a href="#" class="button" id="pure_save_as">Save As</a>');
                    jQuery('.wp-full-overlay-sidebar-content').css('top', '90px');
                    jQuery('#customize-header-actions').css('height', '90px');
                    jQuery('#pure_new_customizing')
                    .focus(function() {
                        if (this.value == '<?php _e('\\\'Save as\\\' new name...', 'pureness'); ?>') this.value = '';
                    }).blur(function() {
                        if (this.value == '') this.value = '<?php _e('\\\'Save as\\\' new name...', 'pureness'); ?>';
                    });
                    jQuery('#pure_save_as').unbind('click').live('click', function() {
                        var $input = jQuery('#_<?php echo esc_attr($name); ?>');
                        var $name = jQuery('#pure_new_customizing');
                        if ($name.val() == '<?php _e('\\\'Save as\\\' new name...', 'pureness'); ?>' || $name.val() == '') {
                            $name.addClass('error').focus().val('');
                            return false;
                        } else {
                            $input.removeClass('error')
                            var _new = {title: $name.val(), settings: wp.customize.get()};
                            _new.settings.pure_saves_list = '';
                            var changeit = true;
                            for (var i = 0; i<pure_saved_designs.length; i++) {
                              if (pure_saved_designs[i].title == _new.title) {
                                pure_saved_designs[i] = _new;
                                changeit = false;
                              }
                            }
                            if (changeit) pure_saved_designs.push(_new);
                            jQuery('li.saveas_empty').remove();
                            if (changeit) $input.next().append('<li><a href="#" rel="'+(pure_saved_designs.length-1)+'">'+$name.val()+'</a></li>');
                         
                            $input.val(JSON.stringify(pure_saved_designs));
                            wp.customize('<?php echo $this->setting->id; ?>').set($input.val());
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {action: 'update_saved_designs', pure_saves_list: $input.val()}, function(response) {});

                            return false;
                        }
                    });
                    jQuery('#pure_save_saves a').live('click', function() {
                        $this = jQuery(this);
                        var $input = jQuery('#_<?php echo esc_attr($name); ?>');
                        var settings = pure_saved_designs[$this.attr('rel')];
                        for(var set in settings.settings) wp.customize(set).set(settings.settings[set]);
                        wp.customize("pure_saves_list").set(JSON.stringify(pure_saved_designs));
                        jQuery('#save').click();
                        setTimeout(function() {document.location.reload();}, 3000);
                    });
                });
            </script>
            <?php
        }

    }

}



add_action('customize_register', 'pure_theme_customizer', 1);

function pure_theme_customizer() {
    global $wp_customize;
    $customize = $wp_customize;

    if ($customize) {

        // Links
        $customize->add_section('pure_links', array(
            'title' => __('Links', 'pureness'),
            'priority' => 13
        ));

        $customize->add_setting('pure_links_color', array(
            'control' => 'color_extended',
            'type' => 'option',
            'transport' => 'none'
        ));

        $customize->add_control(new WP_Customize_Color_Extended_Control($customize, 'pure_links_color_color', array(
                    'settings' => 'pure_links_color',
                    'label' => __('Color', 'pureness'),
                    'section' => 'pure_links',
                    'type' => 'color_extended',
                    'style' => 'a',
                    'css_type' => 'color'
                        )));

        // Body font color
        $customize->add_section('pure_body_font', array(
            'title' => __('Body font color', 'pureness'),
            'priority' => 13
        ));

        $customize->add_setting('pure_body_font_color', array(
            'control' => 'color_extended',
            'type' => 'option',
            'transport' => 'none'
        ));

        $customize->add_control(new WP_Customize_Color_Extended_Control($customize, 'pure_body_font_color_color', array(
                    'settings' => 'pure_body_font_color',
                    'label' => __('Color', 'pureness'),
                    'section' => 'pure_body_font',
                    'type' => 'color_extended',
                    'style' => 'body',
                    'css_type' => 'color'
                        )));


        $customize->add_setting('pure_body_font_color2', array(
            'control' => 'color_extended',
            'type' => 'option',
            'transport' => 'none'
        ));

        $customize->add_control(new WP_Customize_Color_Extended_Control($customize, 'pure_body_font_color_shadow', array(
                    'settings' => 'pure_body_font_color2',
                    'label' => __('Text Shadow Color', 'pureness'),
                    'section' => 'pure_body_font',
                    'type' => 'color_extended',
                    'style' => 'body, .nonexists',
                    'css_type' => 'text-shadow'
                        )));
        
        // Body font
        $customize->add_section('pure_font', array(
            'title' => __('Font', 'pureness'),
            'priority' => 7
        ));

        $customize->add_setting('pure_font_family', array(
            'control' => 'text',
            'type' => 'option',
            'transport' => 'refresh'
        ));

        $customize->add_control(new WP_Customize_Font_Family_Control($customize, 'pure_font_family_list', array(
                    'settings' => 'pure_font_family',
                    'label' => __('Select one <a href="http://www.google.com/webfonts">Google web fonts</a>', 'pureness'),
                    'section' => 'pure_font',
                    'type' => 'font_radio',
                    'fonts' => array('Merriweather:400,300,700', 'Open Sans:300,400,600', 'Oxygen', 'Asap', 'Lato:300,400,700')
                        )));

        $customize->add_control('pure_font_family_text', array(
            'settings' => 'pure_font_family',
            'label' => __('or write the name (p.s. Droid Sans)', 'pureness'),
            'section' => 'pure_font',
            'type' => 'text'
        ));

        $customize->add_setting('pure_font_size', array(
            'control' => 'text',
            'type' => 'option',
            'default' => '',
            'transport' => 'none'
        ));

        $customize->add_control(new WP_Customize_Font_Size_Control($customize, 'pure_font_size', array(
                    'settings' => 'pure_font_size',
                    'label' => __('Size <small>(only for experts)</small>', 'pureness'),
                    'section' => 'pure_font',
                    'type' => 'font_size'
                        )));

        // Background
        $customize->add_section('pure_background', array(
            'title' => __('Background', 'pureness'),
            'priority' => 10
        ));

        $customize->add_setting('pure_background_background', array(
            'control' => 'color_extended',
            'type' => 'option',
            'transport' => 'none'
        ));

        $customize->add_control(new WP_Customize_Color_Extended_Control($customize, 'pure_background_background_color', array(
                    'settings' => 'pure_background_background',
                    'label' => __('Color', 'pureness'),
                    'section' => 'pure_background',
                    'type' => 'color_extended',
                    'style' => 'body'
                        )));

        
        $customize->add_setting('pure_background_background_2', array(
            'control' => 'color_extended',
            'type' => 'option',
            'transport' => 'none'
        ));

        $customize->add_control(new WP_Customize_Patterns_Control($customize, 'pure_background_background_patterns', array(
                    'settings' => 'pure_background_background_2',
                    'label' => __('Patterns', 'pureness'),
                    'section' => 'pure_background',
                    'type' => 'patterns',
                    'style' => 'body'
                        )));

        // Customized saves
        $customize->add_section('pure_saves', array(
            'title' => __('Skins', 'pureness'),
            'priority' => 1
        ));

        $customize->add_setting('pure_saves_list', array(
            'control' => 'save_as',
            'type' => 'option',
            'transport' => 'none'
        ));

        $customize->add_control(new WP_Customize_Save_As_Control($customize, 'pure_saves_list_items', array(
                    'settings' => 'pure_saves_list',
                    'label' => __('List', 'pureness'),
                    'section' => 'pure_saves',
                    'type' => 'save_as'
                        )));

        //$customize->prepare_controls();
    }
}

add_action('customize_render_control', 'pure_render_fonts_control');

function pure_render_fonts_control($control) {
    switch ($control->type) {
        case 'font_size':
            break;
    }
}

add_action('wp_head', 'pure_custome_styles');

function pure_custome_styles() {

    $option = get_option('pure_links_color');
    if ($option) {
        echo $option;
    }

    $option = get_option('pure_body_font_color');
    if ($option) {
        echo $option;
    }

    $option = get_option('pure_body_font_color2');
    if ($option) {
        echo $option;
    }

    $option = get_option('pure_background_background');
    if ($option) {
        echo $option;
    }

    $option = get_option('pure_background_background_2');
    if ($option) {
        echo $option;
    }

    $option = get_option('pure_font_family');
    $option = apply_filters('option_pure_font_family', $option);
    if ($option) {
        echo "<link href='http://fonts.googleapis.com/css?family=" . urlencode($option) . "' rel='stylesheet' type='text/css'>";
        echo '<style type="text/css">html * {font-family: \'' . preg_replace('/(.*)(:.*)/', '$1', $option) . '\';} </style>';
    }

    $option = get_option('pure_font_size');

    $option = apply_filters('option_pure_font_size', $option);
    if ($option) {
        add_filter('body_class', 'pure_body_add_font_size_class');
        echo '<style type="text/css" id="pureness_body_theme">body.font_size {font-size: ' . $option . ';} </style>';
    }
}

function pure_body_add_font_size_class($c) {
    $c[] = 'font_size';
    return $c;
}

add_action('customize_controls_print_scripts', 'pure_customizer_scripts', 100);

function pure_customizer_scripts() {
    ?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
    <?php
}

add_action('customize_controls_print_styles', 'pure_customizer_styles', 100);

function pure_customizer_styles() {
    ?>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <?php
}

add_action('wp_ajax_update_saved_designs', 'pure_update_saved_designs');

function pure_update_saved_designs() {
    $saved = $_POST['pure_saves_list'];
    if (!empty($saved)) {
        update_option('pure_saved_designs', $saved);
    }
    die;
}

add_action('after_setup_theme', 'pure_install_default_designs');

function pure_install_default_designs() {
    $saved = get_option('pure_saved_designs');
    if (!$saved) {
        update_option('pure_saved_designs', '[{\"title\":\"Blue\",\"settings\":{\"pure_links_color\":\"\",\"pure_font_family\":\"\",\"pure_font_size\":\"\",\"pure_background_background\":\"<style type=\\\\\\"text/css\\\\\\" id=\\\\\\"style-customize-section-pure_background_pure_background_background\\\\\\">\\\\nbody {\\\\nbackground-color: #cdd7db;\\\\n\\\\n\\\\n}\\\\n</style>\",\"pure_background_background_2\":\"<style type=\\\\\\"text/css\\\\\\" id=\\\\\\"style-customize-section-pure_body_font_pure_body_font_color2\\\\\\">\\\\nbody, .nonexists {\\\\ntext-shadow: 0 1px 0 #eee;\\\\n\\\\n\\\\n}\\\\n</style>\",\"pure_saves_list\":\"\",\"pure_body_font_color\":\"\",\"pure_body_font_color2\":\"\"}},{\"title\":\"Default skin\",\"settings\":{\"pure_links_color\":\"\",\"pure_font_family\":\"\",\"pure_font_size\":\"\",\"pure_background_background\":\"\",\"pure_background_background_2\":\"\",\"pure_saves_list\":\"\",\"pure_body_font_color\":\"\",\"pure_body_font_color2\":\"\"}}]');
    }
}
