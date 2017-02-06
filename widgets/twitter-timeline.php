<?php

class Pure_Twitter_Timeline extends WP_Widget {

  /**
   * Widget setup.
   */
  function Pure_Twitter_Timeline() {
    /* Widget settings. */
    $widget_ops = array('classname' => 'clear twitter_timeline', 'description' => __('Widget to display your latest Twitter updates', 'pureness'));

    /* Widget control settings. */
    $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'twitter-timeline-widget');

    /* Create the widget. */
    $this->WP_Widget('twitter-timeline-widget', __('Custom Latest Tweets', 'pureness'), $widget_ops, $control_ops);
  }

  /**
   * How to display the widget on the screen.
   */
  function widget($args, $instance) {
    extract($args);

    $title = apply_filters('widget_title', $instance['title']);
    echo $before_widget;
    ?>
    <?php if ($title) { ?><h3><?php echo $title;?></h3><?php } ?>
    <ul id="latest-tweets"></ul>
    <script type="text/javascript">
      // Copyright (c) 2008 John Resig (jquery.com)
      function prettyDate(time){
        var date = new Date((time || "").replace(/-/g,"/").replace(/TZ/g," ")),
        diff = (((new Date()).getTime() - date.getTime()) / 1000),
        day_diff = Math.floor(diff / 86400);

        if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
          return '';

        return (
        diff < 60 && "<?php _e('just now', 'pureness'); ?>" ||
          diff < 120 && "<?php _e('1 minute ago', 'pureness'); ?>" ||
          diff < 3600 && ("<?php _e('%s minutes ago', 'pureness'); ?>").replace('%s', Math.floor( diff / 60 )) ||
          diff < 7200 && "<?php _e('1 hour ago', 'pureness'); ?>" ||
          diff < 86400 && ("<?php _e('%s hours ago', 'pureness'); ?>").replace('%s', Math.floor( diff / 3600 )) ||
          day_diff == 1 && "<?php _e('Yesterday', 'pureness'); ?>" ||
          day_diff < 7 && ("<?php _e('%s days ago', 'pureness'); ?>").replace('%s', day_diff) ||
          day_diff < 31 && ("<?php _e('%s weeks ago', 'pureness'); ?>").replace('%s', Math.ceil( day_diff / 7 )) || '');
      }

      function pure_show_twitter_timeline(data) {
        //console.log(data[0]);
        var ele = jQuery('#latest-tweets');
        ele.html('');
        if (ele.find('li').length == 0) {
          for (var i=0; i<data.length; i++) {
            ele.append('<li>'+data[i].text.replace(/(https?:\/\/[^\s$,]+)/g, '<a href="$1">$1</a>').replace(/@([\w\d_]+)/g, '<a href="http://twitter.com/$1">@$1</a>')+' &middot; <em><a target="_blank" href="http://twitter.com/'+data[i].user.screen_name+'/status/'+data[i].id_str+'">'+prettyDate(data[i].created_at)+'</a></em></li>');
          }
          ele.after('<iframe class="twitter-follow-button" scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/follow_button.html#_=1313254481770&align=&button=grey&id=twitter_tweet_button_0&lang=<?php echo (WPLANG?substr(WPLANG, 0, 2):'en'); ?>&link_color=6688aa&screen_name='+data[0].user.screen_name+'&show_count=false&show_screen_name=&text_color=ffffff" style="width: 300px; height: 20px;" title="">');
        }
      }
    </script>
    <script type="text/javascript" src="https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name=<?php echo get_option('P_twitter'); ?>&count=1&callback=pure_show_twitter_timeline">
    </script>
    
    <?php
    echo $after_widget;
  }

  /**
   * Update the widget settings.
   */
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['count'] = $new_instance['count'];

    return $instance;
  }

  /**
   * Displays the widget settings controls on the widget panel.
   * Make use of the get_field_id() and get_field_name() function
   * when creating your form elements. This handles the confusing stuff.
   */
  function form($instance) {
    $defaults = array('title' => '', 'count' => 1);
    $instance = wp_parse_args((array) $instance, $defaults);
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'pureness'); ?></label>
      <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />

    </p>
    <p>
      <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of tweets', 'pureness'); ?></label>
      <input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>" style="width:100%;" />

    </p>
    <p><em><?php _e('This widget shows you to display your latest tweets. Twitter account is configured in Pureness Theme Options', 'pureness'); ?></em></p>
    <?php
  }

}
