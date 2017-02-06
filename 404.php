<?php get_header(); ?>
	<div class="container">
 		 <div class="row content-background">

        		<h1><a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><?php _e('Sorry, page not found', 'pureness'); ?></a></h1>
     

    				
  		</div> <!--end row-->
	</div> <!--end container-->


<footer>
 	<div class="container">
   		<div class="row">
			<p class="twelvecol">

				<a rel="external" class="wordpress" href="http://wordpress.org" title="WordPress">WordPress</a>,

				<?php $copyright_name = get_option('P_copyright_name'); ?> <?php echo date('Y.') ?>
			
			</p>
		

 		</div> <!--end row-->
  	</div><!--end container-->
</footer>


</body>
</html>