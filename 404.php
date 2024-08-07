<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package LD-theme
 * @author WisdmLabs
 * @since LD-theme 1.0
 */
get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div class="error-404 not-found">
			<div class="<?php echo get_local_layout_type() ?>">
				<div class="row">
					<div class="col-md-12 wdm-404-content">
                        <h6 class="underline_orange"><?php _e("Houston, we have a problem",'elumine'); ?></h6>
                        <h1 class="tts-page-title"><?php _e("That page can’t be found.",'elumine'); ?></h1>
                        <div class="wdm-404-content-custom-img post-thumbnail" style="max-width: 300px; margin-top: 1rem;">
                        	<img width="490" height="400" src="<?php echo get_stylesheet_directory_uri(); ?>/images/blackhole_1.gif" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="">
                        </div>
					</div>
				</div><!-- .row -->

		  </div><!-- .container -->
		</div><!-- .error-404 -->

	</main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
