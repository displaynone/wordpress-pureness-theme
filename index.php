<?php get_header(); ?>
<div class="container" id="content">
  <div class="row content-background">
    <section id="featured" class="twelvecol sticky">
      <?php

      if (have_posts()) {
        $cont = 0;
        $dif = 1;
        while (have_posts()) {
          the_post();
          $cont++;
          $perm = get_post_meta($post->ID, 'URL Client', true);
          
          if ($cont == 1 && !is_paged()) { // Sticky post
            $has_thumb = has_post_thumbnail();
            $catch = catch_video(); 
            ?>
            <!-- The 1st post only if it's not paged -->
       <article <?php post_class((!$has_thumb && !$catch)?'text':null); ?> id="post-<?php the_ID(); ?>">
         <?php if ($has_thumb) { ?>
            <div class="sixcol">
                        <figure>
                          <?php if($perm) { ?><a rel="external" href="<?php echo $perm ?>" title="<?php the_title_attribute(); ?>" ><?php } ?><?php the_post_thumbnail('featured', array('title' => "")); ?><figcaption><?php _e('Featured', 'pureness'); ?></figcaption><?php if($perm) { ?></a><?php } ?>
                    </figure>
                </div><!--end sixcol-->
              <?php } else { ?>
              
                  <?php echo $catch; ?>
              <?php } ?>
                  
                <div class="excerpt">
                                <h2 <?php if(!$perm) { echo 'class="nourl"'; } ?>><?php if($perm) { ?><a rel="external" href="<?php echo $perm ?>" title="<?php the_title_attribute(); ?>"><?php } ?><?php the_title(); ?><?php if($perm) { ?></a><?php } ?></h2>
                                
                                         
                                        
                                      <time datetime="<?php the_time('Y-m-d') ?>" pubdate><?php the_date(); ?><?php the_date(); ?> <?php edit_post_link(__('Edit This', 'pureness'), '<strong>', '</strong>'); ?></time>
                         
                                    <?php if ($has_thumb || $catch) { ?><div class="hide"><?php } ?>
                                        <?php echo the_content_without_video(); ?>
                                    <?php if ($has_thumb || $catch) { ?></div><?php } ?>
              
              


              
                  </div><!--end excerpt-->
              
            </article>
          </section> <!--end featured-->
          
    <?php } else { // Next posts [2, 3, 4], [5, 6, 7] if it's paged or all posts if it's paged ?>
      <?php
      if (($cont == 2 && !is_paged()) || ($cont == 1 && is_paged())) {
?>
        <?php if (is_paged()) {
          $dif = 0;
          ?>
          <p><?php _e('Page ', 'pureness');
          echo $paged;
          ?></p>

        <?php } else { ?>
      <section id="work" class="twelvecol">
     <?php } } 
     $has_thumb = has_post_thumbnail();
     $catch = catch_video(); 
    
     ?>
                    <article <?php post_class((!$has_thumb && !$catch)?'text':null); ?>  id="post-<?php the_ID(); ?>">
      <?php if ($has_thumb) { ?>
                        <div class="sixcol">
                                 <figure>
                                    <?php if($perm) { ?><a rel="external" href="<?php echo $perm ?>" title="<?php the_title_attribute(); ?>" ><?php } ?>
        <?php the_post_thumbnail('featured', array('title' => "")); ?>
                                  <?php if($perm) { ?></a><?php } ?>
                                </figure>
                        </div>
     <?php } else { ?>
     
                         <?php echo $catch; ?>
     <?php } ?>
                            
                            
                            <div class="excerpt">
                                <h2 <?php if(!$perm) { echo 'class="nourl"'; } ?>><?php if($perm) { ?><a rel="external" href="<?php echo $perm ?>" title="<?php the_title_attribute(); ?>"><?php } ?><?php the_title(); ?><?php if($perm) { ?></a><?php } ?></h2>
                                        
                                        <time datetime="<?php the_time('Y-m-d') ?>" pubdate><?php the_date(); ?><?php the_date(); ?> <?php edit_post_link(__('Edit This', 'pureness'), '<strong>', '</strong>'); ?></time>
                         
                                    
                                        <?php if ($has_thumb || $catch) { ?><div class="hide"><?php } ?>
                                        <?php echo the_content_without_video(); ?>
                                    <?php if ($has_thumb || $catch) { ?></div><?php } ?>
                                    
                                    

                           </div><!--end excerpt-->
                  </article>
            <?php
          }
        }
      }
      ?>
      
      
      
      
                     
           			 <?php global $wp_query;           
$max_num_pages = $wp_query->max_num_pages;
$paged = get_query_var('paged');
if ($paged < 2 && $cont == $wp_query->query_vars['posts_per_page']) {

  echo '<div class="wp-pagenavi">';
  echo '<a href="'. next_posts( $max_num_pages, false ).'">'.__('More', 'pureness').'</a>';
  echo '</div> <!--end wp-pagenavi-->';
}
?>
           		 
                         
     <?php if ($cont > 1) { ?>
            </section> <!--end work-->
     <?php } ?>
    
  </div> <!--end row-->
</div><!--end container-->

<section id="services" class="container">
      <div class="row">
            <?php if (is_visible_sidebar('sidebar-services')) {
  dynamic_sidebar('sidebar-services');
} ?>

    </div> <!--end row-->
</section>

<?php if (get_option('P_twitter')) { ?>
<section class="container">
  <div class="row">
    <div id="timeline" class="twelvecol">
<?php if (is_visible_sidebar('sidebar-footer')) {
  dynamic_sidebar('sidebar-footer');
} ?>

                <!--Follow links (vimeo dribbble, linkedin...) tablet/mobile only-->
                
                       <?php if(is_visible_sidebar('sidebar-header')) {dynamic_sidebar('sidebar-header');} ?>
                
    </div> <!--end timeline-->
  </div> <!--end row-->
</section><!--end section-->
<?php } ?>

<?php get_footer(); ?>