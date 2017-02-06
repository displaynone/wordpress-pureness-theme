<?php

require_once( dirname(__FILE__) . '/../../../../wp-load.php' );


// Updating sidebar options
function pure_sidebar_action($theme=null) {
  
  if (isset($_POST['pure_sidebar']) && isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
    global $current_user;
    if ($current_user->caps["administrator"]) {
      $sidebar_id = $_POST['sidebar_id'];
      if (isset($_POST['visible'])) {
        update_option('sidebar_visible_' . $sidebar_id, $_POST['visible']);
      }
      $sidebar_data['posts'] = isset($_POST['posts']);
      $sidebar_data['categories'] = isset($_POST['categories']);
      $sidebar_data['allpages'] = isset($_POST['allpages']);
      $sidebar_data['pages'] = array();
      $sidebar_data['cats'] = array();
      foreach ($_POST as $k => $v) {
        if (preg_match('/page_/', $k))
          $sidebar_data['pages'][] = str_replace('page_', '', $k);
        if (preg_match('/cat_/', $k))
          $sidebar_data['cats'][] = str_replace('cat_', '', $k);
      }
      update_option("sidebar_" . $sidebar_id, $sidebar_data);
    }
  } else {
    // Widgets Sidebar
    if (function_exists('register_sidebar_widget')) {
      register_sidebar(array(
          'name' => __('Header', 'pureness'),
          'id' => 'sidebar-header',
          'before_widget' => '<aside>',
          'after_widget' => '</aside>',
          'before_title' => '<h2>',
          'after_title' => '</h2>',
          'exclusive' => true,
          'visible' => true
      ));

      register_sidebar(array(
          'name' => __('Info', 'pureness'),
          'id' => 'sidebar-services',
          'before_widget' => '<div class="fourcol">',
          'after_widget' => '</div>',
          'before_title' => '<h2>',
          'after_title' => '</h2>',
          'exclusive' => true,
          'visible' => true
      ));

      register_sidebar(array(
          'name' => __('Footer', 'pureness'),
          'id' => 'sidebar-footer',
          'before_widget' => '<aside>',
          'after_widget' => '</aside>',
          'before_title' => '<h2>',
          'after_title' => '</h2>',
          'exclusive' => true,
          'visible' => true
      ));

      $pure_installed_widgets = get_option('pure_installed_widgets');
      global $wp_widget_factory;


      if (!$pure_installed_widgets) {
        // Theme not instaled, setting to not configurated
        update_option('pure_installed_widgets', 1);
        global $wp_registered_sidebars;
        foreach ($wp_registered_sidebars as $k => $side) {
          update_option('sidebar_visible_' . $k, 'true');
        }
        // Default widgets
        retrieve_widgets();
        global $sidebars_widgets;
        $sidebars_widgets["sidebar-header"][] = "follow-links-widget-13";
        $sidebars_widgets["sidebar-footer"][] = "twitter-timeline-widget-13";
        $sidebars_widgets["sidebar-services"][] = "text-13";
        $sidebars_widgets["sidebar-services"][] = "text-14";
        $sidebars_widgets["sidebar-services"][] = "text-15";
        wp_set_sidebars_widgets($sidebars_widgets);
        // Follow Links
        $ss = get_option('widget_follow-links-widget', array());
        $ss[13] = array('show_twitter' => 'on', 'show_dribbble' => 'on', 'show_linkedin' => 'on', 'show_facebook' => 'on', 'show_googleplus' => 'on', 'show_rss' => 'on');
        update_option('widget_follow-links-widget', $ss);
        // Twitter timeline
        $ss = get_option('widget_twitter-timeline-widget', array());
        $ss[13] = array();
        update_option('widget_twitter-timeline-widget', $ss);
        // Services
        $ss = get_option('widget_text', array());
        $ss[13] = array('title'=>__('About', 'pureness'), 'text'=>__('This is a <strong>3 column widget</strong>.  About. Services. Values. 3 lines recommended. Use strong tag. Name, hometown and experience.', 'pureness'));
        $ss[14] = array('title'=>__('Services', 'pureness'), 'text'=>__('We craft functional and elegant <strong>interfaces</strong>. We have been helping small startups and big tech companies around the world since 2001.', 'pureness'));
        $ss[15] = array('title'=>__('Values', 'pureness'), 'text'=>__('<strong>Less, but better</strong>. Back to simplicity. Back to purity. We work closely with you to develop your projects into memorable brands.', 'pureness'));
        update_option('widget_text', $ss);        

        $sidebar_data['posts'] = true;
        $sidebar_data['categories'] = true;
        $sidebar_data['allpages'] = true;
        $sidebar_data['pages'] = array();
        $sidebar_data['cats'] = array();
        update_option('sidebar_sidebar-main', $sidebar_data);
        update_option('P_modify_excerpt', true);
        update_option('P_excerpt_length', 100);
        update_option('P_publish_form', true);
        $pure_installed_widgets = 1;
      }

      if (!function_exists('pure_admin_sidebar_widgets')) {
        add_action('admin_enqueue_scripts', 'pure_admin_sidebar_widgets');

        function pure_admin_sidebar_widgets() {
          wp_register_script('pure_admin_widgets', get_template_directory_uri() . '/js/admin_widgets.js.php');
          wp_enqueue_script('pure_admin_widgets');
          wp_enqueue_script('jquery-ui-dialog');
          wp_enqueue_style('wp-jquery-ui-dialog');
          wp_register_style('google-jquery-ui-dialog', get_template_directory_uri() . '/css/jquery-ui.css');
          wp_enqueue_style('google-jquery-ui-dialog');
        }
      }
    }
  }
}
add_action('init', 'pure_sidebar_action', 100);
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") pure_sidebar_action();

add_action('widgets_init', 'pure_load_widgets');

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function pure_load_widgets() {
  register_widget('Pure_Twitter_Timeline');
  register_widget('Pure_Follow_Links');
}

// Checks if sidebar is visible or not
function is_visible_sidebar($sidebar) {
//echo '<pre>'; var_dump($sidebar);  
  if (is_string($sidebar)) {
    global $wp_registered_sidebars;
    $sidebar = $wp_registered_sidebars[$sidebar];
  }
  $side = get_option('sidebar_' . $sidebar['id']);
  if (empty($side)) return true;
//var_dump($side, get_option('sidebar_visible_'.$sidebar['id']));
  if ($sidebar['visible'] && get_option('sidebar_visible_' . $sidebar['id']) != 'true')
    return false;
  if (isset($sidebar['pageable']) && $sidebar['pageable']) {
    if ($side['allpages'] && is_page())
      return true;
    if ($side['posts'] && is_single())
      return true;
    if ($side['categories'] && is_category())
      return true;
    if ($side['categories'] && is_single())
      return true;
    $id = get_the_ID();
    if ($side['pages'] && in_array($id, $side['pages']))
      return true;
    if ($id) {
      $cats = get_the_category($id);
      foreach ($cats as $c)
        if (in_array($c->cat_ID, $side['cats']))
          return true;
    }
    return false;
  }
  return true;
}


function pure_reset_widgets($theme) {
    update_option('pure_installed_widgets', 0);
}
add_action('switch_theme', 'pure_reset_widgets');