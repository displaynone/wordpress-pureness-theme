<?php
/*
 * When thumbs sizes are changed it's necessary to update them
 */
function pure_refresh_thumbs() {
  global $wpdb, $_wp_additional_image_sizes;

  
  $ids = $wpdb->get_results("select ID, post_title, post_date from $wpdb->posts");
  echo '<pre>';
  $up_dir = wp_upload_dir();
  foreach($ids as $id) {
    $tid = get_post_thumbnail_id($id->ID);
    if ($tid) {
      delete_post_meta($tid, '_wp_attachment_metadata');
      $sizes = wp_get_attachment_image_src($tid, 'full');
      $data = wp_generate_attachment_metadata( $tid, (str_replace($up_dir['baseurl'], $up_dir['basedir'], $sizes[0])) );
      wp_update_attachment_metadata( $tid, $data);
      echo '<p>'.sprintf(__('%s\'s thumbs updated.', 'pureness'), $id->post_title).'</p>';
      flush();
    }
  }
}

