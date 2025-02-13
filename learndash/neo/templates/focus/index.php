<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$cuser     = wp_get_current_user();
		$course_id = learndash_get_course_id();
		$user_id   = ( is_user_logged_in() ? $cuser->ID : false );

		/**
		 * Fires before the header in the focus template.
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-focus-header-before', $course_id, $user_id );

		learndash_get_template_part(
			'focus/header.php',
			array(
				'course_id' => $course_id,
				'user_id'   => $user_id,
				'context'   => 'focus',
			),
			true
		);

		/**
		 * Fires before the sidebar in the focus template.
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-focus-sidebar-before', $course_id, $user_id );

		learndash_get_template_part(
			'focus/sidebar.php',
			array(
				'course_id' => $course_id,
				'user_id'   => $user_id,
				'context'   => 'focus',
			),
			true
		); ?>
	
	<div class="ld-focus-main">

		<?php
		/**
		 * Fires before the masthead in the focus template.
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-focus-masthead-before', $course_id, $user_id );

		learndash_get_template_part(
			'focus/masthead.php',
			array(
				'course_id' => $course_id,
				'user_id'   => $user_id,
				'context'   => 'focus',
			),
			true
		);

		/**
		 * Fires after the masthead in the focus template.
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-focus-masthead-after', $course_id, $user_id );
		?>
		<?php 
			$focus_width = apply_filters( 'learndash_30_focus_mode_width', LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'focus_mode_content_width' ) );
		?>
		<div class="ld-focus-content" style="width: <?php echo $focus_width; ?>">

			<?php
			/**
			 * Fires before the title in the focus template.
			 *
			 * @param int $course_id Course ID.
			 * @param int $user_id   User ID.
			 */
			do_action( 'learndash-focus-content-title-before', $course_id, $user_id );
			?>

			<h1><?php the_title(); ?></h1>

			<?php
			/**
			 * Fires before the content in the focus template.
			 *
			 * @param int $course_id Course ID.
			 * @param int $user_id   User ID.
			 */
			do_action( 'learndash-focus-content-content-before', $course_id, $user_id );
			?>

			<?php the_content(); ?>

			<?php
			/**
			 * Fires after the content in the focus template.
			 *
			 * @param int $course_id Course ID.
			 * @param int $user_id   User ID.
			 */
			do_action( 'learndash-focus-content-content-after', $course_id, $user_id );
			?>

			<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'learndash' ),
						'after'  => '</div>',
					)
				);
			?>

			<?php
			/**
			 * Filters whether to load comments in focus mode or not.
			 *
			 * @param boolean $load_focus_comments Whether to comments in focus mode or not.
			 */
			if ( apply_filters( 'learndash_focus_mode_can_view_comments', is_user_logged_in() ) && comments_open() ) :
				learndash_get_template_part(
					'focus/comments.php',
					array(
						'course_id' => $course_id,
						'user_id'   => $user_id,
						'context'   => 'focus',
					),
					true
				);
				endif;
			?>

			<?php
			/**
			 * Fires at the focus mode content end.
			 *
			 * @param int $course_id Course ID.
			 * @param int $user_id   User ID.
			 */
			do_action( 'learndash-focus-content-end', $course_id, $user_id );
			?>
		</div> <!--/.ld-focus-content-->

	</div> <!--/.ld-focus-main-->

		<?php
		/**
		 * Fires before the footer in the focus template.
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-focus-content-footer-before', $course_id, $user_id );

		learndash_get_template_part(
			'focus/footer.php',
			array(
				'course_id' => $course_id,
				'user_id'   => $user_id,
				'context'   => 'focus',
			),
			true
		);

		/**
		 * Fires after the footer in the focus template.
		 *
		 * @param int $course_id Course ID.
		 * @param int $user_id   User ID.
		 */
		do_action( 'learndash-focus-content-footer-after', $course_id, $user_id );

	endwhile;
else :

	learndash_get_template_part(
		'modules/alert.php',
		array(
			'type'    => 'warning',
			'icon'    => 'alert',
			'message' => esc_html__( 'No content found at this address', 'learndash' ),
		),
		true
	);

endif; ?>
