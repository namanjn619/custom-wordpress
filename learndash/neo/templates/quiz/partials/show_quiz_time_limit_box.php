<?php
/**
 * Displays Quiz Time Limit Box
 *
 * Available Variables:
 *
 * @var object  $quiz_view      WpProQuiz_View_FrontQuiz instance.
 * @var object $quiz           WpProQuiz_Model_Quiz instance.
 * @var array  $shortcode_atts Array of shortcode attributes to create the Quiz.
 *
 * @since 3.2
 *
 * @package LearnDash\Quiz
 */
?>
<div class="el-timer-wrap">
<div style="display: none;" class="wpProQuiz_time_limit">
	<div class="tl-label"><?php echo esc_html__( 'Time Left:', 'learndash' ) ?></div>
	<div class="time">
		<?php
		echo wp_kses_post(
			SFWD_LMS::get_template(
				'learndash_quiz_messages',
				array(
					'quiz_post_id' => $quiz->getID(),
					'context'      => 'quiz_quiz_time_limit_message',
					'message'      => esc_html__( '', 'learndash' ) . '<span>0</span>',
				)
			)
		);
		?>
		<div class="el-time-wrap">
			<div class="wpProQuiz_progress"></div>
		</div>
	</div>
</div>
<div style="display: none;" class="custom_wpProQuiz_time_limit">
	<div class="tl-label"><?php echo esc_html__( 'Time Left:', 'learndash' ) ?></div>
	<div class="time">
		<?php
		echo wp_kses_post(
			SFWD_LMS::get_template(
				'learndash_quiz_messages',
				array(
					'quiz_post_id' => $quiz->getID(),
					'context'      => 'quiz_quiz_time_limit_message',
					'message'      => esc_html__( '', 'learndash' ) . '<span>0</span>',
				)
			)
		);
		?>
	</div>
</div>
</div>