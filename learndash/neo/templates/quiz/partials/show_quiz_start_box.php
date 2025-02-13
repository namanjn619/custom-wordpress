<?php
/**
 * Displays Quiz Start Box
 *
 * Available Variables:
 *
 * @var object $quiz_view WpProQuiz_View_FrontQuiz instance.
 * @var object $quiz      WpProQuiz_Model_Quiz instance.
 * @var array  $shortcode_atts Array of shortcode attributes to create the Quiz.
 *
 * @since 3.2
 *
 * @package LearnDash\Quiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpProQuiz_text">
	<?php
	// $quiz->getPostId();
	$is_ps_enabled = get_post_meta( $quiz->getPostId(), 'tts_practice_set_enabled', true );
	$executed_count = 0;
	$css_class = '';
	if( 'true' == $is_ps_enabled ) {
		$css_class = 'tts-practice-test-quiz';
	}
	if ( $quiz->isFormActivated() && $quiz->getFormShowPosition() == WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START ) {
		$quiz_view->showFormBox();
	}
	?>
	<div>
		<input class="wpProQuiz_button <?php echo $css_class; ?>" type="button" 
		value="<?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen,Squiz.PHP.EmbeddedPhp.ContentAfterOpen
		echo wp_kses_post(
			SFWD_LMS::get_template(
				'learndash_quiz_messages',
				array(
					'quiz_post_id' => $quiz->getID(),
					'context'      => 'quiz_start_button_label',
					// translators: placeholder Quiz.
					'message'      => sprintf( esc_html_x( 'Start %s', 'placeholder Quiz', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
				)
			)
		); // phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect
		?>" name="startQuiz" /><?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterEnd ?>
	</div>
</div>
