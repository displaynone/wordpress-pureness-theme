<!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <?php load_theme_textdomain('pureness', get_template_directory() . '/lang'); ?>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <meta name="twitter:card" content="summary">  
    <?php $twitter = get_option('P_twitter'); if ($twitter) { ?>
    <meta name="twitter:creator" value="@<?php echo $twitter; ?>">
   
    <meta name="twitter:site" value="@<?php echo $twitter; ?>">  
      <?php } ?>
    <?php if (is_front_page()) : ?>
      <title><?php bloginfo('name'); ?></title>
      <meta property="og:title" content="<?php bloginfo('name'); ?>" />
      <meta name="twitter:title" value="<?php bloginfo('name'); ?>" />
    <?php elseif (is_404()) : ?>
      <title><?php _e('Page not found', 'pureness'); ?> &middot; <?php bloginfo('name'); ?></title>
      <meta property="og:title" content="<?php _e('Page not found', 'pureness'); ?> &middot; <?php bloginfo('name'); ?>" />
      <meta name="twitter:title" value="<?php _e('Page not found', 'pureness'); ?> &middot; <?php bloginfo('name'); ?>" />
    <?php else : ?>
      <title><?php wp_title($sep = ''); ?> &middot; <?php bloginfo('name'); ?></title>
      <meta property="og:title" content="<?php wp_title($sep = ''); ?> &middot; <?php bloginfo('name'); ?>" />
      <meta name="twitter:title" value="<?php wp_title($sep = ''); ?> &middot; <?php bloginfo('name'); ?>" />
    <?php endif; ?>
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  	<![endif]-->
    <!-- Basic Meta Data -->	
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

      <meta name="description" content="<?php strip_tags(bloginfo('name')); ?>. <?php echo strip_tags(html_entity_decode(get_bloginfo('description')));?>" />
      <meta property="og:description" content="<?php echo strip_tags(html_entity_decode(get_bloginfo('description')));?>" />
      <meta name="twitter:description" value="<?php echo strip_tags(html_entity_decode(get_bloginfo('description')));?>" />


    <!--Stylesheets-->
    	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />
        
    <!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/css3-mediaqueries.js"></script>
      <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/1_default_colors.css" type="text/css" media="screen" />
   

    <!-- Feeds -->
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?>RSS 2.0 Feed" href="<?php bloginfo('rss2_url'); ?>" />
    
  
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
      <meta property="og:image" content="<?php echo pure_avatar(true, 250); ?>" />
      <meta name="twitter:image" content="<?php echo pure_avatar(true, 250); ?>" />
    <?php global $wp; $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) ); ?>
    <meta property="og:url" content="<?php echo curPageURL(); ?>" />
    <?php if (get_option('P_googleplus')) { ?><link href="https://plus.google.com/<?php echo get_option('P_googleplus'); ?>" rel="publisher" /><?php } ?>        

    <?php wp_head(); ?>
  </head>

<body <?php body_class(); ?> <?php language_attributes(); ?>>

    <header>
      <div class="container">
        <div class="row vcard"> 
				

          <a class="photo" href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><?php echo pure_avatar(); ?></a>

<?php if (is_home())
  echo('<h1 id="logo" class="sixcol">'); else
  echo('<div id="logo" class="sixcol">'); ?><a class="url" href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><span class="fn"><?php bloginfo('name'); ?></span></a><?php if (is_home())
  echo('</h1>'); else
  echo('</div>'); ?>



				<div class="sixcol last">
      
      					 <h2><?php echo html_entity_decode(get_bloginfo('description'));?></h2>

                   	 		 <?php if(is_visible_sidebar('sidebar-header')) {dynamic_sidebar('sidebar-header');} ?>
   				</div>


        </div><!--end row vcard-->
      </div><!--container-->

    </header>
