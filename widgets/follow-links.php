<?php

class Pure_Follow_Links extends WP_Widget {

  /**
   * Widget setup.
   */
  function Pure_Follow_Links() {
    /* Widget settings. */
    $widget_ops = array('classname' => 'subscribe fixed', 'description' => __('Widget for follow links, your social identity: Twitter, Dribbble, Facebook, Google+, LinkedIn, E-Mail and RSS', 'pureness'));
    /* Widget control settings. */
    $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'follow-links-widget');

    /* Create the widget. */
    $this->WP_Widget('follow-links-widget', __('Custom Follow Links', 'pureness'), $widget_ops, $control_ops);
  }

  /**
   * How to display the widget on the screen.
   */
  function widget($args, $instance) {
    extract($args);

    /* Our variables from the widget settings. */
    $title = apply_filters('widget_title', isset($instance['title'])?$instance['title']:'');
    $show_twitter = isset($instance['show_twitter']) ? $instance['show_twitter'] : false;
    $show_dribbble = isset($instance['show_dribbble']) ? $instance['show_dribbble'] : false;
    $show_vimeo = isset($instance['show_vimeo']) ? $instance['show_vimeo'] : false;
    $show_facebook = isset($instance['show_facebook']) ? $instance['show_facebook'] : false;
    $show_linkedin = isset($instance['show_linkedin']) ? $instance['show_linkedin'] : false;
    $show_googleplus = isset($instance['show_googleplus']) ? $instance['show_googleplus'] : false;
    $show_rss = isset($instance['show_rss']) ? $instance['show_rss'] : true;
    $show_email = isset($instance['show_email']) ? $instance['show_email'] : true;
    
    echo $before_widget;

    if (false && $title)
      echo $before_title . $title . $after_title;

    if ($show_twitter || $show_dribbble || $show_vimeo || $show_facebook || $show_linkedin || $show_googleplus || $show_rss || $show_email) {
      ?>
      <p class="subscribe">

        <?php
        if ($show_rss) {

            
      $rss_url = get_option('P_feedburner_username');
      if ($rss_url != "") {
        echo '<a class="rss" title="RSS" href="http://feeds.feedburner.com/' . $rss_url . '">RSS</a>';
      } else {
        ?>
      <a class="rss" href="<?php bloginfo('rss2_url'); ?>" rel="external" title="RSS">RSS</a>
      <?php } 
        
      }
        ?>
        <?php
        if ($show_email) {
           ?>    <span class="email"><?php echo antispambot(get_bloginfo('admin_email')); ?></span> <?php
      }
        ?>
         <?php
        if ($show_googleplus) {
          $googleplus = get_option('P_googleplus');
          if ($googleplus) {
            ?>    <strong><a rel="me" class="external googleplus" title="Google Plus" href="https://plus.google.com/<?php echo $googleplus; ?>?rel=author">Google+</a></strong> 
	<?php
        }
      }
      ?>
	<?php
        if ($show_linkedin) {
          $linkedin = get_option('P_linkedin');
          if ($linkedin) {
            ?>    <a class="external linkedin" title="LinkedIn" href="http://linkedin.com/in/<?php echo $linkedin; ?>">LinkedIn</a> 
            <?php
        }
      }
        ?>
        <?php
        if ($show_facebook) {
          $facebook = get_option('P_facebook');
          if ($facebook) {
            ?>    <a class="external facebook" title="Facebook" href="http://facebook.com/<?php echo $facebook; ?>">Facebook</a> <?php
        }
      }
        ?>
        <?php
        if ($show_vimeo) {
          $vimeo = get_option('P_vimeo');
          if ($vimeo) {
            ?>    <a class="external vimeo" title="Vimeo" href="http://vimeo.com/<?php echo $vimeo; ?>">Vimeo</a> <?php
        }
      }
        ?>
        <?php
        if ($show_dribbble) {
          $dribbble = get_option('P_dribbble');
          if ($dribbble) {
            ?>    <a class="external dribbble" title="Dribbble" href="http://dribbble.com/<?php echo $dribbble; ?>">Dribbble</a> <?php
        }
      }
        ?>
        <?php
        if ($show_twitter) {
          $twitter = get_option('P_twitter');
          if ($twitter) {
            ?>    <a class="external twitter" title="Twitter" href="http://twitter.com/<?php echo $twitter; ?>">Twitter</a> <?php
        }
      }
        ?>
        
      </p>
      <?php
		echo $after_widget;
    }
  }

  /**
   * Update the widget settings.
   */
  function update($new_instance, $old_instance) {
    $instance = $old_instance;

    $instance['title'] = strip_tags($new_instance['title']);
    $instance['picture'] = strip_tags($new_instance['picture']);
    $instance['show_twitter'] = $new_instance['show_twitter'] == 'on';
    $instance['show_dribbble'] = $new_instance['show_dribbble'] == 'on';
    $instance['show_vimeo'] = $new_instance['show_vimeo'] == 'on';
    $instance['show_facebook'] = $new_instance['show_facebook'] == 'on';
    $instance['show_linkedin'] = $new_instance['show_linkedin'] == 'on';
    $instance['show_googleplus'] = $new_instance['show_googleplus'] == 'on';
    $instance['show_rss'] = $new_instance['show_rss'] == 'on';
    $instance['show_email'] = $new_instance['show_email'] == 'on';

    return $instance;
  }

  /**
   * Displays the widget settings controls on the widget panel.
   * Make use of the get_field_id() and get_field_name() function
   * when creating your form elements. This handles the confusing stuff.
   */
  function form($instance) {

    /* Set up some default widget settings. */
    $defaults = array('title' => __('About', 'pureness'), 'show_twitter' => true, 'show_dribbble' => true, 'show_facebook' => true, 'show_linkedin' => true, 'show_googleplus' => true);
    $instance = wp_parse_args((array) $instance, $defaults);
    ?>

    <?php
    $twitter = get_option('P_twitter');
    $dribbble = get_option('P_dribbble');
    $vimeo = get_option('P_vimeo');
    $facebook = get_option('P_facebook');
    $linkedin = get_option('P_linkedin');
    $googleplus = get_option('P_googleplus');
    $email = get_bloginfo('admin_email');
    $rss = get_bloginfo('rss2_url');
    ?>
    <style>.inactive_item {font-style: italic; color: #ccc;} .inactive_item span { color: #F33; }</style>
    <p><?php _e('You must configure your social accounts in Pureness Theme Options', 'pureness'); ?></p>
    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_email'], true); ?> id="<?php echo $this->get_field_id('show_email'); ?>" name="<?php echo $this->get_field_name('show_email'); ?>" /> 
      <label for="<?php echo $this->get_field_id('show_email'); ?>" <?php echo!$email ? ' class="inactive_item" ' : '' ?>><?php _e('Show E-Mail?', 'pureness'); ?> <?php echo!$email ? ' <span>Not configured</span> ' : '' ?></label>
    </p>

    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_twitter'], true); ?> id="<?php echo $this->get_field_id('show_twitter'); ?>" name="<?php echo $this->get_field_name('show_twitter'); ?>" <?php echo!$twitter ? ' disabled="disabled" ' : '' ?>/> 
      <label for="<?php echo $this->get_field_id('show_twitter'); ?>" <?php echo!$twitter ? ' class="inactive_item" ' : '' ?>><?php _e('Show Twitter?', 'pureness'); ?> <?php echo!$twitter ? ' <span>Not configured</span> ' : '' ?></label>
    </p>

    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_dribbble'], true); ?> id="<?php echo $this->get_field_id('show_dribbble'); ?>" name="<?php echo $this->get_field_name('show_dribbble'); ?>" <?php echo!$dribbble ? ' disabled="disabled" ' : '' ?>/> 
      <label for="<?php echo $this->get_field_id('show_dribbble'); ?>" <?php echo!$dribbble ? ' class="inactive_item" ' : '' ?>><?php _e('Show Dribbble?', 'pureness'); ?> <?php echo!$dribbble ? ' <span>Not configured</span> ' : '' ?></label>
    </p>
    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_vimeo'], true); ?> id="<?php echo $this->get_field_id('show_vimeo'); ?>" name="<?php echo $this->get_field_name('show_vimeo'); ?>" <?php echo!$vimeo ? ' disabled="disabled" ' : '' ?>/> 
      <label for="<?php echo $this->get_field_id('show_vimeo'); ?>" <?php echo!$vimeo ? ' class="inactive_item" ' : '' ?>><?php _e('Show Vimeo?', 'pureness'); ?> <?php echo!$vimeo ? ' <span>Not configured</span> ' : '' ?></label>
    </p>
    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_facebook'], true); ?> id="<?php echo $this->get_field_id('show_facebook'); ?>" name="<?php echo $this->get_field_name('show_facebook'); ?>" <?php echo!$facebook ? ' disabled="disabled" ' : '' ?>/> 
      <label for="<?php echo $this->get_field_id('show_facebook'); ?>" <?php echo!$facebook ? ' class="inactive_item" ' : '' ?>><?php _e('Show Facebook?', 'pureness'); ?> <?php echo!$facebook ? ' <span>Not configured</span> ' : '' ?></label>
    </p>
    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_linkedin'], true); ?> id="<?php echo $this->get_field_id('show_linkedin'); ?>" name="<?php echo $this->get_field_name('show_linkedin'); ?>" <?php echo!$linkedin ? ' disabled="disabled" ' : '' ?>/> 
      <label for="<?php echo $this->get_field_id('show_linkedin'); ?>" <?php echo!$linkedin ? ' class="inactive_item" ' : '' ?>><?php _e('Show LinkedIn?', 'pureness'); ?> <?php echo!$linkedin ? ' <span>Not configured</span> ' : '' ?></label>
    </p>
     <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_googleplus'], true); ?> id="<?php echo $this->get_field_id('show_googleplus'); ?>" name="<?php echo $this->get_field_name('show_googleplus'); ?>" <?php echo!$googleplus ? ' disabled="disabled" ' : '' ?>/> 
      <label for="<?php echo $this->get_field_id('show_googleplus'); ?>" <?php echo!$googleplus ? ' class="inactive_item" ' : '' ?>><?php _e('Show Google+?', 'pureness'); ?> <?php echo!$googleplus ? ' <span>Not configured</span> ' : '' ?></label>
    </p>
    <p>
      <input class="checkbox" type="checkbox" <?php checked($instance['show_rss'], true); ?> id="<?php echo $this->get_field_id('show_rss'); ?>" name="<?php echo $this->get_field_name('show_rss'); ?>" <?php echo!$rss ? ' disabled="disabled" ' : '' ?>/> 
      <label for="<?php echo $this->get_field_id('show_rss'); ?>" <?php echo!$rss ? ' class="inactive_item" ' : '' ?>><?php _e('Show RSS?', 'pureness'); ?> <?php echo!$rss ? ' <span>Not configured</span> ' : '' ?></label>
    </p>

    <?php
  }

}