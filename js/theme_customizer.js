function pure_update_colors_customizer(obj, type) {

  var $parent = obj.parents('ul.customize-section-content:first');
  var $labels = $parent.find('label');
  var groups = [];
  var style = '';
  
  $labels.each(function() {
    var $this = jQuery(this);
    if ($this.hasClass('colorextended') || $this.hasClass('gradient') || $this.hasClass('patterns')) {
      var _style = $this.find('input:hidden').data('style');
      if (typeof groups[_style] == 'undefined') {
        groups[_style] = [];
      }
      var c = $this.attr('class').split(' ');
      groups[_style][c[0]] = $this;
    }
  });

  for(var css_path in groups) {
    style = '';
    var $single_color = groups[css_path].colorextended;
    var single_color_input = typeof $single_color != 'undefined'?$single_color.find('input:hidden:first'):'';
    var $gradient = groups[css_path].gradient;
    var gradient_input = typeof $gradient != 'undefined'? $gradient.find('input:hidden:first'):null;
    var $pattern = groups[css_path].patterns;
    var pattern_input = typeof $pattern != 'undefined'? $pattern.find('input:hidden:first'):null;
    var css = ['', '', ''];

    if (single_color_input && single_color_input.val() != "") {
      var extra = single_color_input.data('css_type') == 'text-shadow'? '0 1px 0 ':'';
      css[0] = single_color_input.data('css_type')+': '+extra+single_color_input.val()+';\n';
    }

    if (gradient_input && gradient_input.val() != "") {
      colors = gradient_input.val().split('|');
      css[1] = 'background-color: '+colors[0]+';\n';
      css[1] += 'background-image: -moz-linear-gradient(left, '+colors[0]+' 0%, '+colors[1]+' 100%);\n';
      css[1] += 'background-image: -webkit-gradient(linear, left top, right top, color-stop(0%,'+colors[0]+'), color-stop(100%,'+colors[1]+'));\n';
      css[1] += 'background-image: -webkit-linear-gradient(left, '+colors[0]+' 0%,'+colors[1]+' 100%);\n';
      css[1] += 'background-image: -o-linear-gradient(left, '+colors[0]+' 0%,'+colors[1]+' 100%);\n';
      css[1] += 'background-image: -ms-linear-gradient(left, '+colors[0]+' 0%,'+colors[1]+' 100%);\n';
      css[1] += 'background-image: linear-gradient(left, '+colors[0]+' 0%,'+colors[1]+' 100%);\n';
      css[1] += 'filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\''+colors[0]+'\', endColorstr=\''+colors[1]+'\',GradientType=1 );\n';
    }  

    // Patterns background
    if (pattern_input) {
      jQuery('#' + $parent.parents('.customize-section:first').attr('id')+' .patterns_list div').each(function() {
        var $ele = jQuery(this);

        if (!$ele.data('pattern')) {
          $ele.data('pattern', $ele.css('background-image'));
        }
        var pattern_css = css[0];
        if(css[1] != '') {
          pattern_css += css[1]
            .replace(/background-image:([^;]*);/g, 'background-image: '+$ele.data('pattern')+', $1;')
            .replace('GradientType=1', 'GradientType=0')
            .replace('right top', 'left bottom')
            .replace(/left,/g, 'top,');
        } else {
          pattern_css += 'background-image: '+$ele.data('pattern')+';';
        }
        jQuery(this).attr('style', pattern_css);
      });
      jQuery('#' + $parent.parents('.customize-section:first').attr('id')+' .pattern_selector').each(function() {
        var $this = jQuery(this);
        var $input = jQuery('#' + $parent.parents('.customize-section:first').attr('id')+' .patterns input:hidden');

        var pattern_css = css[0];
        if(css[1] != '') {
          pattern_css += css[1]
            .replace(/background-image:([^;]*);/g, 'background-image: '+$input.val()+', $1;')
            .replace('GradientType=1', 'GradientType=0')
            .replace('right top', 'left bottom')
            .replace(/left,/g, 'top,');
        } else {
          pattern_css += 'background-image: '+$input.val()+';';
        }
        $this.attr('style', pattern_css);
      });
    }

    if (type == 'gradient' && gradient_input) {
      colors = gradient_input.val().split('|');
      obj.css({backgroundColor: obj.next().is('a')?colors[0]:colors[1]});
      if (colors != '') obj.removeClass('transparent').css({backgroundColor: obj.next().is('a')?colors[0]:colors[1]});
      jQuery('head').find('#style-' + $parent.parent().attr('id')).remove();
      jQuery('head').append('<style type="text/css" id="style-' + $parent.parent().attr('id') + '">\n'+
      '#'+obj.parents('[id]:first').attr('id') + ' .color-picker > a ' + ' {\n'+ css[1] +'\n}\n</style>\n');
    }

    if (pattern_input && pattern_input.val() != "") {
      if (css[1] != '') {
        css[1] = css[1].replace(/background-image:/g, 'background-image: '+pattern_input.val()+', ');
      } else {
        css[2] = 'background-image: '+pattern_input.val()+';\n';
      }
    }
    jQuery('iframe').contents().find('#style-' + $parent.parent().attr('id')).remove();
    style += css_path + ' {\n'+
      css
        .join('\n')
        .replace('GradientType=1', 'GradientType=0')
        .replace('right top', 'left bottom')
        .replace(/left,/g, 'top,') +
      '}';
    var name = 
      single_color_input? single_color_input.attr('rel') :
      (gradient_input? gradient_input.attr('rel') :
        (pattern_input? pattern_input.attr('rel'): ''
        )
      );

    style = '<style type="text/css" id="style-' + $parent.parent().attr('id') + '_'+name+'">\n'+
      style + '\n</style>';
    jQuery('iframe').contents().find('#style-' + $parent.parent().attr('id') + '_'+name).remove();
    jQuery('iframe').contents().find('head').append(style);


    wp.customize(name).set(style);
  }
}

jQuery(document).ready(function() {
  jQuery('#customize-section-nav, #customize-section-static_front_page').remove();
  
});



