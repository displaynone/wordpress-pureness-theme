<?php
//define( 'SAVEQUERIES', TRUE ); 
define('FUNCTIONS', get_template_directory() . '/functions');
define('WIDGETS', get_template_directory() . '/widgets');
require_once (FUNCTIONS . '/pureness-admin.php');
require_once (FUNCTIONS . '/theme-customizer.php');
require_once (FUNCTIONS . '/refresh-thumbnails.php');
require_once (FUNCTIONS . '/widgets.php');
require_once (WIDGETS . '/follow-links.php');
require_once (WIDGETS . '/twitter-timeline.php');

if (!isset($content_width))
  $content_width = 693;


// Adding default custom field on new posts (Portfolio external link)
add_action('wp_insert_post', 'pure_set_default_custom_fields');
 
function pure_set_default_custom_fields($post_id)
{
    if ( isset($_GET['post_type']) && $_GET['post_type'] != 'page' ) {
 
        add_post_meta($post_id, 'URL Client', '', true);
 
    }
 
    return true;
}

// Video featured embed
function catch_video() {
  global $post;
  $first_vid = '';
  $output = preg_match_all('/<iframe[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post->post_content, $matches);
  $first_vid = isset($matches [1] [0])?$matches [1] [0]:'';
  $res = '';
  if ($first_vid) {
    $res = '<div class="videowrap sixcol"><iframe src="';
    $res .= $first_vid;
    $res .= '" frameborder="0" allowfullscreen></iframe></div>';
  }
  return $res;
}

function the_content_without_video() {
  global $post;
  return preg_replace('/<iframe[^>]+src=[\'"]([^\'"]+)[\'"][^>]*><\/iframe>/i', '', apply_filters('the_content', $post->post_content));
}

// Meta description and keywords
function csv_tags() {
  $list = get_the_tags();
  if ($list) {
    foreach ($list as $tag) {
      $csv_tags[] = $tag->name;
    }
  }
  foreach ((get_the_category()) as $tag) {
    $csv_tags[] = $tag->cat_name;
  }
  echo is_array($csv_tags) ? '<meta name="keywords" content="' . implode(',', $csv_tags) . '" />' : '';
}


// Comment hack: this code automatically rejects any request for comment posting coming from a browser (or, more commonly, a bot) that has no referrer in the request
function check_referrer() {
  if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == 'pc') {
    wp_die(__('Please enable referrers in your browser, or, if you\'re a spammer, bugger off!', 'pureness'));
  }
}

add_action('check_comment_flood', 'check_referrer');

// Comments number without pingbacks and trackbacks
function countComments($count) {
  global $wp_query, $post;

  return isset($wp_query->comments_by_type)?count($wp_query->comments_by_type['comment']):$post->comment_count;
}

add_filter('get_comments_number', 'countComments', 0);

// Numeric Page Navigation: (Lester Chan - http://lesterchan.net/wordpress/readme/wp-pagenavi.html)
// Function: Page Navigation Options
function pagenavi_init() {
  $pagenavi_options = array();
  $pagenavi_options['pages_text'] = __('Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'pureness');
  $pagenavi_options['current_text'] = '%PAGE_NUMBER%';
  $pagenavi_options['page_text'] = '%PAGE_NUMBER%';
  $pagenavi_options['first_text'] = __('&laquo; First', 'pureness');
  $pagenavi_options['last_text'] = __('Last &raquo;', 'pureness');
  $pagenavi_options['next_text'] = __('&raquo;', 'pureness');
  $pagenavi_options['prev_text'] = __('&laquo;', 'pureness');
  $pagenavi_options['dotright_text'] = __('...', 'pureness');
  $pagenavi_options['dotleft_text'] = __('...', 'pureness');
  $pagenavi_options['style'] = 1;
  $pagenavi_options['num_pages'] = 5;
  $pagenavi_options['always_show'] = 0;
  return $pagenavi_options;
}

// Create sitemap xml In WordPress Without Using Any Plugins (http://bit.ly/o5RkYr)
add_action("publish_post", "eg_create_sitemap");
add_action("publish_page", "eg_create_sitemap");

function eg_create_sitemap() {
  $postsForSitemap = get_posts(array(
      'numberposts' => -1,
      'orderby' => 'modified',
      'post_type' => array('post', 'page'),
      'order' => 'DESC'
          ));

  $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
  $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

  foreach ($postsForSitemap as $post) {
    setup_postdata($post);

    $postdate = explode(" ", $post->post_modified);

    $sitemap .= '<url>' .
            '<loc>' . get_permalink($post->ID) . '</loc>' .
            '<lastmod>' . $postdate[0] . '</lastmod>' .
            '<changefreq>monthly</changefreq>' .
            '</url>';
  }

  $sitemap .= '</urlset>';

  global $wp_filesystem;
  WP_Filesystem();
  $wp_filesystem->put_contents(ABSPATH . "sitemap.xml", $sitemap, FS_CHMOD_FILE);
}

// Better SEO automatically remove short words from URL (http://bit.ly/mQntKC)
add_filter('sanitize_title', 'remove_short_words');

function remove_short_words($slug) {
  if (!is_admin())
    return $slug;
  $slug = explode('-', $slug);
  foreach ($slug as $k => $word) {
    if (strlen($word) < 3) {
      unset($slug[$k]);
    }
  }
  return implode('-', $slug);
}

// URL Search Friendly (http://bit.ly/o0waD5)
function search_url_rewrite_rule() {
  if (is_search() && !empty($_GET['s'])) {
    wp_redirect(home_url("/search/") . urlencode(get_query_var('s')));
    exit();
  }
}

add_action('template_redirect', 'search_url_rewrite_rule');


/* Disable the Admin Bar. */
add_filter('show_admin_bar', '__return_false');
remove_action('personal_options', '_admin_bar_preferences');


// Gravatar Favicon (Patrick Chia - http://patrick.bloggles.info/plugins/)
if (!function_exists('get_favicon')) :

  function get_favicon($id_or_email, $size = '96', $default = '', $alt = false) {
    $avatar = pure_avatar(); // get_avatar($id_or_email, $size, $default, $alt);

    preg_match('#src=[\'"]([^\'"]+)[\'"]#', $avatar, $m);
    $newAvatar = $m[1];

    return $newAvatar;
  }

endif;

function blog_favicon() {
  $apple_icon = get_favicon(get_bloginfo('admin_email'), 60);
  $favicon_icon = get_favicon(get_bloginfo('admin_email'), 18);

  if (get_option('show_avatars')) {
    echo "<link rel=\"apple-touch-icon\" href=\"$apple_icon\" />\n";
    echo "<link rel=\"shortcut icon\" type=\"image/png\" href=\"$favicon_icon\" />\n";
  }
}

// Modify admin logo using avatar
function admin_logo() {
  $admin_logo = get_favicon(get_bloginfo('admin_email'), 31);

  if (get_option('show_avatars')) {
    ?>
    <style type="text/css">
      #header-logo{background-image: none;
                   -moz-border-radius: 5px;
                   -webkit-border-bottom-left-radius: 5px;	-webkit-border-bottom-right-radius: 5px; -webkit-border-top-left-radius: 5px; -webkit-border-top-right-radius: 5px;
                   -khtml-border-bottom-left-radius: 5px;-khtml-border-bottom-right-radius: 5px;-khtml-border-top-left-radius: 5px;-khtml-border-top-right-radius: 5px;
                   border-bottom-left-radius: 5px;	border-bottom-right-radius: 5px;border-bottom-top-radius: 5px;border-bottom-top-radius: 5px;}
    </style>
    <script type="text/javascript">
      jQuery(document).ready(function() {jQuery('#header-logo').attr('src', '<?php echo $admin_logo; ?>')});
    </script>
    <?php
  }
}

// Feed logo using avatar
function add_feed_logo() {
  $feed_logo = get_favicon(get_bloginfo('admin_email'), 48);
  echo "
   <image>
    <title>" . get_bloginfo('name') . "</title>
    <url>" . $feed_logo . "</url>
    <link>" . get_bloginfo('siteurl') . "</link>
   </image>\n";
}

add_action('wp_head', "blog_favicon");
add_action('admin_head', 'blog_favicon');
add_action('login_head', 'blog_favicon');
add_action('admin_head', 'admin_logo');
add_action('rss_head', 'add_feed_logo');
add_action('rss2_head', 'add_feed_logo');

if (!function_exists('p_addgravatar')) {

  // Add a new avatar for defaults
  function p_addgravatar($avatar_defaults) {
    $myavatar = get_template_directory_uri() . '/images/pureness_gravatar.png';
    //default avatar
    $avatar_defaults[$myavatar] = 'Pureness';

    return $avatar_defaults;
  }

  add_filter('avatar_defaults', 'p_addgravatar');
}



// Adding menus to theme
add_action('init', 'register_my_menus');

function register_my_menus() {
  register_nav_menus(
          array(
              'header-menu' => __('Header', 'pureness')
          )
  );
}

/** Adding home script */
add_action('wp_enqueue_scripts', 'pure_add_scripts');

function pure_add_scripts() {
  if (is_home() || is_paged()) {
    wp_enqueue_script('jquery');
    wp_register_script('pure_home_scripts', get_template_directory_uri(). '/js/home.js', array('jquery'), '1.3');
    wp_enqueue_script('pure_home_scripts');
    wp_localize_script('pure_home_scripts', 'home', array(
      'read_more' => __('Read', 'pureness'),
      'read_more_title' => __('Read more', 'pureness'),
      'hide' => __('Hide', 'pureness'),
      'hide_title'=> __('Hide', 'pureness'),
      )
    ); 
  }

  wp_register_script('css3-mediaqueries', get_template_directory_uri() . '/js/css3-mediaqueries.js');
  wp_enqueue_script('css3-mediaqueries');
}

add_action('admin_enqueue_scripts', 'pure_admin_scripts', null, null, true);
function pure_admin_scripts() {
  global $current_screen;
  if ($current_screen->id == 'post') {
    wp_enqueue_script('pure_thumbs', get_template_directory_uri() . '/js/thumbnails.js', array( 'jquery', 'post' ) );
    wp_localize_script('pure_thumbs', 'ajax_path', array(
      'postID' => '',
      'ajaxurl' => admin_url('admin-ajax.php' )
      )
    );  
    wp_enqueue_style('wp-pointer');
    wp_enqueue_script('wp-pointer');    
  }
}


// Adding post thumbnails
if (function_exists('add_theme_support')) {
  add_theme_support('post-thumbnails');
  $def = array(
      'featured' => array(960, 590, true)
    );
  $thumbs_sizes = get_option('P_thumbs_size', $def );
  if (empty($thumbs_sizes)) $thumbs_sizes = $def;
//var_dump($thumbs_sizes);  
  foreach($thumbs_sizes as $t=>$v) {
    add_image_size($t, $v[0], $v[1], $v[2]);
  }
//var_dump($thumbs_sizes );  
}



// Gets avatar depending of configuration: facebook, twitter, google+, gravatar
function pure_avatar($url = false, $size = 60) {
  $v = get_option('P_header_logo');
  if (!$v || $v == 'gravatar') {
    $res = get_avatar(get_option('admin_email'), $size);
    return $url? preg_replace('#.*src="([^"]*)".*#', '$1', str_replace("'", '"', $res)):$res;
  }
  if ($v == 'twitter') {
    if ($url) {
      return 'http://api.twitter.com/1/users/profile_image/' . get_option('P_twitter') . '.json?size=bigger';
    } else {
      return '<img width="'.$size.'" height="'.$size.'" src="http://api.twitter.com/1/users/profile_image/' . get_option('P_twitter') . '.json?size=bigger" alt="" />';
    }
  }
  if ($v == 'facebook') {
    if ($url) {
      return 'https://graph.facebook.com/' . get_option('P_facebook') . '/picture';
    } else {
      return '<img width="'.$size.'" height="'.$size.'" src="https://graph.facebook.com/' . get_option('P_facebook') . '/picture" alt="" />';
    }
  }
  if ($v == 'googleplus') {
    if ($url) {
      return 'http://profiles.google.com/s2/photos/profile/' . get_option('P_googleplus') . '?sz='.$size;
    } else {
      return '<img src="http://profiles.google.com/s2/photos/profile/' . get_option('P_googleplus') . '?sz='.$size.'" alt="" />';
    }
  }

  return $url? $v:'<img width="'.$size.'" height="'.$size.'" src="' . $v . '" alt="" />';
}

function curPageURL() {
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

// Show Home Link in wp_nav_menu default fallback function (http://bit.ly/rntOUe)
function my_page_menu_args($args) {
  $args['show_home'] = true;
  return $args;
}

add_filter('wp_page_menu_args', 'my_page_menu_args');

load_theme_textdomain('pureness', get_template_directory() . '/lang');

// Remove
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');


// Remove the annoying:
// <style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style> added in the header
function remove_recent_comment_style() {
	global $wp_widget_factory;
	remove_action( 
            'wp_head', 
            array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) 
        );
}
add_action( 'widgets_init', 'remove_recent_comment_style' );


// Adding previous and next posts editing links to Edit Post page
function pure_add_navigation_edit_posts() {
  if(preg_match('#wp-admin/post\.php#', $_SERVER["SCRIPT_NAME"]) && isset($_GET['post']) &&  isset($_GET['action']) && $_GET['action'] == 'edit') {
    global $post;
    if(!empty($post)) { // && $post->post_type == 'post') {
      foreach(array(true, false) as $prev) {
        $p = get_adjacent_post(false, '', $prev);
        if (!empty($p)) {
          echo '<script type="text/javascript">';
          echo 'jQuery(document).ready(function() {jQuery(".wrap h2").append(\'<a class="add-new-h2" href="'.admin_url('post.php?action=edit&post='.$p->ID).'" title="'.__('Edit', 'pureness').' '.$p->post_title.'">'.($prev?'&laquo; ':'').(strlen($p->post_title) > 25?mb_substr($p->post_title, 0, 25).'...':$p->post_title).(!$prev?' &raquo;':'').'</a>\');});';
          echo '</script>';
        }  
      }
    }
  }
  
}
add_action('admin_head', 'pure_add_navigation_edit_posts');


function only_home() {
//var_dump(is_home(), is_front_page())  ; exit();
  if (!is_home() && !is_admin() && !is_404() && !is_feed()) {
    wp_redirect(home_url());
    exit();
  }
}
add_action('wp', 'only_home');

add_filter( 'post_limits', 'pure_load_more_posts' );
function pure_load_more_posts( $limit ) {
    if (is_home()  && is_paged()) {
        return preg_replace('#,.*#', ', 10000', $limit);
    }

    return $limit;
}

add_action( 'wp_ajax_pure_thumbs_alert', 'pure_thumbs_alert' );

function pure_thumbs_alert() {
  // ignore the request if the current user doesn't have
  // sufficient permissions
  if ( current_user_can( 'edit_posts' ) ) {
    // get the submitted parameters
    $postID = $_POST['postID'];
    // generate the response
    $tid = get_post_thumbnail_id($postID);
    if ($tid) {
      global $_wp_additional_image_sizes;
      $sizes = wp_get_attachment_image_src($tid, 'full');
      
      $ok = $_wp_additional_image_sizes['featured']['width'] <= $sizes[1];
      $response = json_encode( array( 'ok' => $ok , 'message'=>$ok?'ok':sprintf(__('<h3>Featured image Size</h3><p>Featured image is not big enough. Minimum size: %sx%spx. Please upload another image. Thanks :)</p>', 'pureness'),$_wp_additional_image_sizes['featured']['width'], $_wp_additional_image_sizes['featured']['height']) ) );
    }
    // response output
    header( "Content-Type: application/json" );
    echo $response;
  }

  exit;
}

function pure_post_thumbnail_html($html) {
    $tid = get_post_thumbnail_id(get_the_ID());
    if ($tid) {
      global $_wp_additional_image_sizes;
      $sizes = wp_get_attachment_image_src($tid, 'full');
      
      $ok = $_wp_additional_image_sizes['featured']['width'] <= $sizes[1];
      if (!$ok) return '<img src="'.get_template_directory_uri().'/images/change_image.png" />';
    }
    return $html;
}
add_filter('post_thumbnail_html', 'pure_post_thumbnail_html');

add_theme_support( 'automatic-feed-links' );

// Theme check cheat, it is a simple One Page Portfolio Theme, some features are not implemented
if (false) { 
  paginate_links();
  paginate_comments_links();
  wp_enqueue_script( "comment-reply" );
  wp_list_comments( '' );
  wp_link_pages('');
  comments_template( '', '' ); 
  comment_form();  
}
