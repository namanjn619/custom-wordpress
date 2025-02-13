<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$classes = array(
	'container' => 'ld-lesson-item' . ( get_the_ID() === absint( $quiz['post']->ID ) ? ' ld-is-current-lesson ' : '' ) . ( 'completed' === $quiz['status'] ? ' learndash-complete' : ' learndash-incomplete' ),
	'wrapper'   => 'ld-lesson-item-preview' . ( get_the_ID() === absint( $quiz['post']->ID ) ? ' ld-is-current-item ' : '' ),
	'anchor'    => 'ld-lesson-item-preview-heading ld-primary-color-hover',
	'title'     => 'ld-lesson-title',
);

if ( isset( $context ) && 'lesson' === $context ) {
	$classes['container'] = 'ld-table-list-item' . ( 'completed' === $quiz['status'] ? ' learndash-complete' : ' learndash-incomplete' );
	$classes['wrapper']   = 'ld-table-list-item-wrapper';
	$classes['anchor']    = 'ld-table-list-item-preview ld-primary-color-hover' . ( get_the_ID() === absint( $quiz['post']->ID ) ? ' ld-is-current-item ' : '' );
	$classes['title']     = 'ld-topic-title';
} ?>

<div class="<?php echo esc_attr( $classes['container'] ); ?> <?php echo esc_attr( $classes['anchor'] ); ?>">
	<div class="<?php echo esc_attr( $classes['wrapper'] ); ?>">
		<a class="<?php echo esc_attr( $classes['anchor'] ); ?>" href="<?php echo esc_url( learndash_get_step_permalink( $quiz['post']->ID, $course_id ) ); ?>">

			<?php learndash_status_icon( $quiz['status'], 'sfwd-quiz', null, true ); ?>

			<span class="elumine-icon modern-icon-Quizes"></span>
			<div class="<?php echo esc_attr( $classes['title'] ); ?>"><?php echo wp_kses_post( $quiz['post']->post_title ); ?></div> <!--/.ld-lesson-title-->
			<?php
			$quiz_time = '';
			if ('on' === learndash_get_setting($quiz['post'], 'quiz_time_limit_enabled')) {
                $quiz_time = learndash_get_setting( $quiz['post'], 'timeLimit' );
            }
            if ( $quiz_time > 0 && 'completed' !== $quiz['status'] ) { ?>
            	<span class="time-estimate right-aligned"><?php echo elumine_convert_seconds_to_time($quiz['post'], $quiz_time); ?></span>
            <?php
	        }
			?>

		</a> <!--/.ld-lesson-item-preview-heading-->
	</div> <!--/.ld-lesson-item-preview-->
</div>
