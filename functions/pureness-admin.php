<?php
if (get_option('twitter_username') && !get_option('P_twitter'))
  update_option('P_twitter', 'http://twitter.com/' . get_option('twitter_username'));

$themename = "Pureness Theme";
$shortname = "P";
$theme_current_version = "1.4";
$theme_url = "";



// Stylesheet Auto Detect
$alt_stylesheet_path = get_template_directory();

$alt_stylesheets = array();

if (is_dir($alt_stylesheet_path)) {
  if ($alt_stylesheet_dir = opendir($alt_stylesheet_path)) {
    while (($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false) {
      if (stristr($alt_stylesheet_file, '.css') !== false && $alt_stylesheet_file != 'style.css') {
        $alt_stylesheets[] = $alt_stylesheet_file;
      }
    }
  }
}

asort($alt_stylesheets);
array_unshift($alt_stylesheets, 'Select a stylesheet:');

$options = array();

add_action('init', 'pure_init_options_theme');

function pure_init_options_theme() {
  global $options, $themename, $shortname, $alt_stylesheets;
  $options = array(
 
      // HEADER LOGO
      array("name" => __("Header Logo", 'pureness'),
          "type" => "subhead"),
      array("name" => __("Your logo and favicon", 'pureness'),
          "id" => $shortname . "_header_logo",
          "std" => "",
          "desc" => __('If you configure and SAVE your Twitter, Facebook or Google Plus account you will have more avatars to select from', 'pureness'),
          "type" => "avatar"),
      //SOCIAL PROFILES. FOLLOW LINKS
      array("name" => __("Identity", 'pureness'),
          "type" => "subhead"),
      array("name" => __("<a href=\"http://twitter.com/\">Twitter</a> (Username Only)", 'pureness'),
          "id" => $shortname . "_twitter",
          "desc" => __("http://twitter.com/<strong>username</strong>", 'pureness'),
          "type" => "text",
          "std" => "",
          "style" => "width: 300px",
          "row_style" => ""),
      array("name" => __("<a href=\"http://dribbble.com/\">Dribbble</a> (Username Only)", 'pureness'),
          "id" => $shortname . "_dribbble",
          "desc" => __("http://dribbble.com/<strong>id</strong>", 'pureness'),
          "type" => "text",
          "std" => "",
          "style" => "width: 300px",
          "row_style" => ""),
      array("name" => __("<a href=\"http://vimeo.com/\">Vimeo</a> (Username Only)", 'pureness'),
          "id" => $shortname . "_vimeo",
          "desc" => __("http://vimeo.com/<strong>username</strong>", 'pureness'),
          "type" => "text",
          "std" => "",
          "style" => "width: 300px",
          "row_style" => ""),
      array("name" => __("<a href=\"http://facebook.com/\">Facebook</a> (ID/Username Only)", 'pureness'),
          "id" => $shortname . "_facebook",
          "desc" => __("http://facebook.com/<strong>id</strong>", 'pureness'),
          "type" => "text",
          "std" => "",
          "style" => "width: 300px",
          "row_style" => ""),
      array("name" => __("<a href=\"http://linkedin.com/\">Linkedin</a> (Username Only)", 'pureness'),
          "id" => $shortname . "_linkedin",
          "desc" => __("http://linkedin.com/in/<strong>username</strong>", 'pureness'),
          "type" => "text",
          "std" => "",
          "style" => "width: 300px",
          "row_style" => ""),
 	array("name" => __("<a href=\"http://plus.google.com/\">Google+</a> (ID/Username Only)", 'youare'),
          "id" => $shortname . "_googleplus",
          "desc" => __("https://plus.google.com/<strong>id</strong>", 'pureness'),
          "type" => "text",
          "std" => "",
          "style" => "width: 300px",
          "row_style" => ""),
      
     
      // PORTFOLIO FEATURED IMAGE - THUMBNAILS
      array("name" => __("Portfolio Featured Image. Thumbnails sizes", 'pureness'),
          "type" => "subhead"),
      array("name" => __("Modify/add thumbs sizes", 'pureness'),
          "id" => $shortname . "_thumbs_size",
          "std" => array(
              'featured' => array(800, 525, true), 
            ),
          "desc" => __("There are different thumbs sizes used in the theme, you can modify them or add new ones", 'pureness'),
          "type" => "thumbs"),
      
      
      //FOOTER CREDITS AND STATS CODE
      array("name" => __("Footer Credits / Stats Code", 'pureness'),
          "type" => "subhead"),
      array("name" => __("Stats code", 'pureness'),
          "id" => $shortname . "_stats_code",
          "desc" => __("Paste your Google Analytics (or other) tracking code here<br>", 'pureness'),
          "std" => "",
          "type" => "textarea",
          "options" => array("rows" => "4",
              "cols" => "80")),
  );
}

// Adding admin menu option
function mytheme_add_admin() {

  global $themename, $shortname, $options;
  
  if (isset($_REQUEST['P_thumbs_size'])) {
    $val = explode('$$', $_REQUEST['P_thumbs_size']);
    $thumbs = array();
    foreach($val as $v) {
      if (!$v) break;
      $t = explode('#', $v);
      $thumbs[$t[0]] = array($t[1], $t[2], $t[3] == 'on');
    }
    $_REQUEST['P_thumbs_size'] = $thumbs;
  
  }

  if (isset($_GET['page']) && $_GET['page'] == basename(__FILE__)) {

    if (isset($_REQUEST['action']) && 'save' == $_REQUEST['action']) {

      foreach ($options as $value) {
        update_option($value['id'], $_REQUEST[$value['id']]);
      }

      foreach ($options as $value) {
        if (isset($_REQUEST[$value['id']])) {
          update_option($value['id'], $_REQUEST[$value['id']]);
        } else {
          delete_option($value['id']);
        }
      }

      header("Location: themes.php?page=pureness-admin.php&saved=true");
      die;
    } else if (isset($_REQUEST['action']) && 'reset' == $_REQUEST['action']) {

      foreach ($options as $value) {
        delete_option($value['id']);
      }

      header("Location: themes.php?page=pureness-admin.php&reset=true");
      die;
    }
  }

  add_theme_page($themename . __(" Options", 'pureness'), $themename . __(" Options", 'pureness'), 'edit_themes', basename(__FILE__), 'mytheme_admin');
  add_theme_page(__("Refresh Thumbs", 'pureness'), __("Refresh Thumbs", 'pureness'), 'edit_themes', 'refresh-thumbnails.php', 'pure_refresh_thumbs');
  
}

//add_theme_page($themename . 'Header Options', 'Header Options', 'edit_themes', basename(__FILE__), 'headimage_admin');

function headimage_admin() {
  
}

// Pureness theme options
function mytheme_admin() {

  global $themename, $shortname, $options;

  if (isset($_REQUEST['saved']) && $_REQUEST['saved'])
    echo '<div id="message" class="updated fade"><p><strong>' . $themename . __(' settings saved.', 'pureness') . '</strong></p></div>';
  if (isset($_REQUEST['reset']) && $_REQUEST['reset'])
    echo '<div id="message" class="updated fade"><p><strong>' . $themename . __(' settings reset.', 'pureness') . '.</strong></p></div>';
  ?>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery('a[href$=preview_css]').click(function() {this.target = '_blank'; this.href='/?css_creator=true&pure_css='+jQuery('#P_alt_stylesheet').val();})
    });
  </script>
  <div class="wrap">
    <h2 class="updatehook"><?php echo $themename . __(' Options', 'pureness'); ?></h2>

    <table class="widefat" style="margin: 20px 0 0;">
      <thead>

        <tr>
          <th scope="col" style="width: 50%; font: 1.6em Baskerville, palatino, georgia, times, serif;"><?php _e('About Pureness Theme', 'pureness'); ?></th>
          <th scope="col" style="font: 1.6em Baskerville, palatino, georgia, times, serif;"><?php _e('Support', 'pureness'); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr style="background: #f1f1f1; color: #222">
          <td><?php _e('Pureness works in WordPress 3.4+ and is released under GPL License.', 'pureness'); ?> 
          </td>
        </tr>
      </tbody>
    </table>

    <form method="post">

      <form method="post">


  <?php
  foreach ($options as $value) {

    switch ($value['type']) {
      case 'subhead':
        ?>
              </table>

              <hr style="border: 1px dotted #dfdfdf; margin: 20px 0">

              <table class="widefat">

                <thead>
                  <tr>
                    <th scope="col" style="width:20%" class="column-title"><?php echo $value['name']; ?></th>
                    <th scope="col"></th>
                  </tr>
                </thead>

        <?php
        break;

      case 'text':
        ?>
                <tr valign="top" style="<?php echo $value['row_style']; ?>"> 
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td>
                    <input style="<?php echo $value['style']; ?>" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if (get_option($value['id']) != "") {
          echo get_option($value['id']);
        } else {
          echo $value['std'];
        } ?>" />
        <?php echo $value['desc']; ?>
                  </td>
                </tr>
        <?php
        break;

              case 'thumbs':
        ?>
                <tr valign="top" style="<?php echo $value['row_style']; ?>"> 
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                </tr>
                <tr><td>
                  <?php 
                    $thumbs = get_option($value['id'])? get_option($value['id']): $value['std'];
                  ?>
                    <table id="thumbs-options">
                      <tr><th><?php _e('Thumb ID', 'pureness'); ?></th><th><?php _e('Width', 'pureness'); ?></th><th> </th><th><?php _e('Height', 'pureness'); ?></th><th><?php _e('Crop', 'pureness'); ?></th><th> </th></tr>
                  <?php
                    foreach($thumbs as $t=>$v) {
                  ?>
                      <tr class="active"><td><input type="text" value="<?php echo $t; ?>" class="alpha" /></td><td><input type="text" value="<?php echo $v[0]; ?>" style="width: 50px" class="num" /></td><td>x</td><td><input type="text" value="<?php echo $v[1]; ?>" style="width: 50px" class="num" /></td><td><input type="checkbox" <?php echo $v[2]?'checked="checked"':''; ?> /></td><td><input type="button" value="<?php _e('Delete', 'pureness'); ?>" class="del" /></td></tr>
                  <?php } ?>
                      <tr><td><input type="text" value="" class="alpha" /></td><td><input type="text" value="" style="width: 50px" class="num" /></td><td>x</td><td><input type="text" value="" style="width: 50px" class="num" /></td><td><input type="checkbox" /></td><td><input type="button" value="<?php _e('Add', 'pureness'); ?>" class="add" /></td></tr>
                    </table>
                <p><?php _e('You must update thumbs for getting new image sizes', 'pureness'); ?> </p>
                <input type="hidden" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" />
                <script type="text/javascript">
                  jQuery('#thumbs-options').on('change', 'input', function() {
                    change_thumbs_option();
                  }).on('change', 'input.alpha', function() {
                    this.value = this.value.replace(/\s/, '-').replace(/[^a-z0-9_\-]/ig, '');
                  }).on('change', 'input.num', function() {
                    this.value = this.value.replace(/[^0-9]/ig, '');
                  }).on('click', 'input.add', function() {
                    jQuery(this)
                      .attr('value', '<?php _e('Delete', 'pureness'); ?>')
                      .attr('class', 'del')
                      .parents('tr:first').addClass('active')
                      .parent().append('<tr><td><input type="text" value="" class="alpha" /></td><td><input type="text" value="" style="width: 50px" class="num" /></td><td>x</td><td><input type="text" value="" style="width: 50px" class="num" /></td><td><input type="checkbox" /></td><td><input type="button" value="<?php _e('Add', 'pureness'); ?>" class="add" /></td></tr>');
                    change_thumbs_option();
                  }).on('click', 'input.del', function() {
                    jQuery(this).parents('tr:first').fadeOut('slow', function(){jQuery(this).remove()});
                    change_thumbs_option();
                  });
                  function change_thumbs_option() {
                    var val = '';
                    var ok = true;

                    jQuery('#thumbs-options tr.active input').each(function() {
                      var $this = jQuery(this);
                      if (ok) {
                        if ($this.val() == '') {
                          ok = false;
                        } else {
                          if ($this.attr('type') != 'button') {
                            val += ($this.attr('type') == 'checkbox' ? ($this.is(':checked')?'on':'off'):$this.val())+'#';
                            if ($this.attr('type') == 'checkbox') {
                              val += '$$';
                            }
                          }
                        }
                      }
                    });
                  
                    val = val.match(/(([^\$]+\$\$)+)/);
                    val = val[0];
                   
                    jQuery('#P_thumbs_size').val(val);
                  }
                </script>                
                  </td>
                </tr>
        <?php
        break;

      case 'select':
        ?>
                <tr valign="top"> 
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td>
                    <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
              <?php foreach ($value['options'] as $option) { ?>
                        <option<?php if (get_option($value['id']) == $option) {
            echo ' selected="selected"';
          } elseif ($option == $value['std']) {
            echo ' selected="selected"';
          } ?>><?php echo $option; ?></option>
        <?php } ?>
                    </select>
        <?php echo $value['desc']; ?>
                  </td>
                </tr>
        <?php
        break;

      case 'textarea':
        $ta_options = $value['options'];
        ?>
                <tr valign="top"> 
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td>
                <?php echo $value['desc']; ?>
                    <textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="<?php echo $ta_options['cols']; ?>" rows="<?php echo $ta_options['rows']; ?>"><?php
        if (get_option($value['id']) != "") {
          echo stripslashes(get_option($value['id']));
        } else {
          echo $value['std'];
        }
                ?></textarea>
                  </td>
                </tr>
                <?php
                break;

              case "radio":
                ?>
                <tr valign="top"> 
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td><?php if (isset($value['desc'])) { ?><p><?php echo $value['desc']; ?></p><?php } ?>
                      <?php
                      foreach ($value['options'] as $key => $option) {
                        $radio_setting = get_option($value['id']);
                        if ($radio_setting != '') {
                          if ($key == get_option($value['id'])) {
                            $checked = "checked=\"checked\"";
                          } else {
                            $checked = "";
                          }
                        } else {
                          if ($key == $value['std']) {
                            $checked = "checked=\"checked\"";
                          } else {
                            $checked = "";
                          }
                        }
                        ?>
                      <input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><?php echo $option; ?><br />
                      <?php } ?>
                  </td>
                </tr>
                      <?php
                      break;

                    case "checkbox":
                      ?>
                <tr valign="top"> 
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td>
                <?php
                if (get_option($value['id'])) {
                  $checked = "checked=\"checked\"";
                } else {
                  $checked = "";
                }
                ?>
                    <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />

                    <?php echo $value['desc']; ?>
                  </td>
                </tr>
                    <?php
                    break;

                  case "cats_ids":
                    ?>
                <tr valign="top"> 
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td>
                    <p>	<?php
            $pages = get_pages('depth=1&orderby=ID&hide_empty=0');
            //print_r($pages);
            echo __('<strong>Page IDs and Names</strong> (<em>Archives Page</em> you can\'t exclude).<br />', 'pureness');
            foreach ($pages as $page) {
              echo $page->ID . ' = ' . $page->post_name . '<br />';
            }
                    ?>
                    </p>
                  </td>
                </tr>
                    <?php
                    break;

                  case "page":
                    ?>
                <tr valign="top">
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td>
                    <?php wp_dropdown_pages('name=' . $value['id'] . '&selected=' . (get_option($value['id']) != "" ? get_option($value['id']) : $value['std'])); ?>
                  </td>
                </tr>
                <?php
                break;

              case "avatar":
                $val = get_option($value['id']);
                ?>
                <tr valign="top">
                  <th scope="row"><?php echo $value['name']; ?>:</th>
                  <td><p><?php echo $value['desc']; ?></p>
                    <div style="float:left; margin-right: 40px"><input type="radio" name="<?php echo $value['id']; ?>" value="gravatar" <?php if ($val == 'gravatar' || !$val) {
          echo 'checked="checked"';
        } ?>/> Gravatar <br /><?php echo get_avatar(get_option('admin_email'), '60'); ?> </div>   
                      <?php
                      if (get_option('P_twitter')) {
                        ?>
                      <div style="float:left; margin-right: 40px"><input type="radio" name="<?php echo $value['id']; ?>" value="twitter" <?php if ($val == 'twitter') {
                echo 'checked="checked"';
              } ?>/> Twitter <br /><img width="60" height="60" src="http://api.twitter.com/1/users/profile_image/<?php echo get_option('P_twitter'); ?>.json?size=bigger" /> </div>
          <?php
        }
        if (get_option('P_facebook')) {
          ?>
                      <div style="float:left; margin-right: 40px"><input type="radio" name="<?php echo $value['id']; ?>" value="facebook" <?php if ($val == 'facebook') {
            echo 'checked="checked"';
          } ?>/> Facebook <br /> <img width="60" height="60" src="https://graph.facebook.com/<?php echo get_option('P_facebook'); ?>/picture" /> </div>

         
         <?php
        }
        if (get_option('P_googleplus')) {
          ?>
                      <div style="float:left; margin-right: 40px"><input type="radio" name="<?php echo $value['id']; ?>" value="googleplus" <?php if ($val == 'googleplus') {
            echo 'checked="checked"';
          } ?> /> Google Plus <br /><img src="http://profiles.google.com/s2/photos/profile/<?php echo get_option('P_googleplus'); ?>?sz=60" /> </div>

                  <?php
                }
                ?>
                    <div style="float:left; margin-right: 40px"><input type="radio" name="<?php echo $value['id']; ?>" value="" id="your_own_picture" <?php if (!in_array($val, array('gravatar', 'twitter', 'facebook')) && $val) {
          echo 'checked="checked"';
        } ?>/> <?php _e('Your own picture (Size: 60x60px)', 'pureness'); ?> <br />URL: <input type="text" value="<?php echo (!in_array($val, array('gravatar', 'twitter', 'facebook', 'googleplus')) && $val) ? $val : ''; ?>" onKeyUp="document.getElementById('your_own_picture').checked = 'checked'; document.getElementById('your_own_picture').value = this.value"  onChange="document.getElementById('your_own_picture').checked = 'checked'; document.getElementById('your_own_picture').value = this.value" />
                    </div>
                  </td></tr>
                    <?php
                    break;


                  default:

                    break;
                }
              }
              ?>

        </table>

        <p class="submit">
          <input name="save" type="submit" value="<?php _e('Save changes', 'pureness'); ?>" />    
          <input type="hidden" name="action" value="save" />
        </p>
      </form>
      <form method="post">
        <p class="submit">
          <input name="reset" type="submit" value="<?php _e('Reset', 'pureness'); ?>" />
          <input type="hidden" name="action" value="reset" />
        </p>
      </form>
          <?php
        }

        function option_wrapper_header($values) {
          ?>
      <tr valign="top"> 
        <th scope="row"><?php echo $values['name']; ?>:</th>
        <td>
  <?php
}

function option_wrapper_footer($values) {
  ?>
          <br />
  <?php echo $values['desc']; ?>
        </td>
      </tr>
  <?php
}

function option_wrapper_footer_nobreak($values) {
  ?>
      <?php echo $values['desc']; ?>
      </td>
      </tr>
      <?php
    }

    add_action('admin_menu', 'mytheme_add_admin');
    
add_action( 'admin_enqueue_scripts', 'pure_admin_theme_options_scripts' );    
function pure_admin_theme_options_scripts() {

  if (isset($_GET['page']) && $_GET['page'] == 'pureness-admin.php' && isset($_GET['saved']) && $_GET['saved'] == 'true') {
    wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
    add_action( 'admin_print_footer_scripts', 'pure_admin_theme_options_footer_scripts' );
  }
}
function pure_admin_theme_options_footer_scripts() {    
?>
<script type="text/javascript">
jQuery('#menu-appearance a:eq(4)').pointer({
	        content: <?php _e("'<h3>Refresh Thumbs</h3><p>If you have changed the thumb sizes don\'t forget to refresh them</p>'", 'pureness'); ?>,
	        position: 'left',
	        close: function() {
	            // Click in close button
	        }
	      }).pointer('open');
</script>        
<?php 
}