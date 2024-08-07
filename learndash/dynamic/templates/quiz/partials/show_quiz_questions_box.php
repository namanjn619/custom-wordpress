<?php
/**
 * Show Quiz Questions Box
 *
 * Available Variables:
 *
 * @var object $quiz_view      WpProQuiz_View_FrontQuiz instance.
 * @var object $quiz           WpProQuiz_Model_Quiz instance.
 * @var array  $shortcode_atts Array of shortcode attributes to create the Quiz.
 * @var int    $question_count Number of Question to display.
 *
 * @since 3.2
 *
 * @package LearnDash\Quiz
 */
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- we are inside of a template
$global_points = 0;
$json          = array();
$cat_points    = array();
$index		   = 0;
?>
<div style="display: none;" class="wpProQuiz_quiz">
	<ol class="wpProQuiz_list">
	<?php
		$question_mapper = new WpProQuiz_Model_QuestionMapper();
		$quiz_post_id = learndash_get_quiz_id_by_pro_quiz_id( $quiz->getID() );
		$is_rc_enabled = get_post_meta( $quiz_post_id, 'tts_rc_enabled', true );
		$all_question_ids = learndash_get_quiz_questions( $quiz_post_id );
		$all_questions_keys  = array_keys( $all_question_ids );
		$formated_quiz = array();
		$executed_ids = array();
		$quiz_extra_data = array();
		// Check if practice test is enabled or not.
		$is_ps_enabled = get_post_meta( $quiz_post_id, 'tts_practice_set_enabled', true );
		if ( 'true' == $is_rc_enabled ) {
			$rc_set_info        = get_post_meta( $quiz_post_id, 'rc_sets', true );
			$rc_mapped_question = get_post_meta( $quiz_post_id, 'rc_mapped_question', true ); // No Need May be
			if ( ! empty( $rc_set_info ) ) {
				foreach ( $rc_set_info as $set ) {
					$set_data = get_post_meta( $quiz_post_id, $set, true );
					if ( 'enable' == $set_data['status'] && ! empty( $set_data['question'] ) ) {
						$render_questions   = array();
						foreach ( $set_data['question'] as $question_id ) {
							$question_pro_id = get_post_meta( $question_id, 'question_pro_id', true );

							$question        = $question_mapper->fetchById( intval( $question_pro_id ), null );
							$formated_quiz[$question_id]['first_question'] = 0;
							$formated_quiz[$question_id]['last_question'] = 0;
							$formated_quiz[$question_id]['set'] = $set_data;
							$formated_quiz[$question_id]['question'] = $question;
							
							if ( array_key_exists((int)$question_id, $all_question_ids ) ) {
								$executed_ids[] = (int)$question_id;
							}
						}
						$reversed_set_questions = array_reverse($set_data['question']);
						$first_que = array_pop($reversed_set_questions);
						$last_que  = end($set_data['question']);
						$formated_quiz[$first_que]['first_question'] = 1;
						$formated_quiz[$last_que]['last_question'] = 1;
					}
				}
			}
		} elseif( 'true' == $is_ps_enabled ) {
			$mapped_question = get_post_meta( $quiz_post_id, 'ps_mapped_question', true );
			if ( empty( $mapped_question ) ) {
				$mapped_question = array();
			}
			$practice_sets = get_post_meta( $quiz_post_id, 'tts_practice_set_data', true );
			if ( ! empty( $practice_sets ) ) {
				$indexed_questions = get_practice_test_que_number($quiz_post_id,$practice_sets);
				foreach ( $practice_sets as $test_key ) {
					$test_data = get_post_meta( $quiz_post_id, $test_key, true );
					if ( 'break' == $test_data['type'] ) {
						$json = render_break( $test_data, $json, $test_key );
						$question_count++;
					} elseif( 'section' == $test_data['type'] ){
						$quiz_extra_data['index'] = $index;
						$quiz_extra_data['quiz_view'] = $quiz_view;
						$quiz_extra_data['quiz'] = $quiz;
						$quiz_extra_data['global_points'] = $global_points;
						$quiz_extra_data['json'] = $json;
						$quiz_extra_data['cat_points'] = $cat_points;
						$quiz_extra_data['shortcode_atts'] = $shortcode_atts;
						$quiz_extra_data['question_count'] = $question_count;
						$quiz_extra_data['executed_ids'] = $executed_ids;

						$r_data = render_section( $quiz_extra_data, $test_data, $test_key, $indexed_questions );

						$index = $r_data['index'];
						$global_points = $r_data['global_points'];
						$json = $r_data['json'];
						$cat_points = $r_data['cat_points'];
						$question_count = $r_data['question_count'];
						$executed_ids = $r_data['executed_ids'];
					}
				}
			}
			$remaining_questions = array_merge(array_diff($all_questions_keys, $executed_ids), array_diff($executed_ids, $all_questions_keys));
			
			$break_q_post_id = get_option('tts_break_question_post_id');
			$break_q_pro_id  = get_post_meta( $break_q_post_id, 'question_pro_id', true );
			if ( ( $index = array_search( $break_q_post_id, $remaining_questions ) ) !== false) {
			    unset( $remaining_questions[$index] );
			}

			if ( ! empty( $remaining_questions ) ) {
				foreach ( $remaining_questions as $que ) {
					$question_pro_id = get_post_meta( $que, 'question_pro_id', true );
					$question = $question_mapper->fetchById( intval( $question_pro_id ), null );
					$question->setQuestionPostId( $que );
					$question_data['question'] = $question;
					$r_data = render_question_with_format( $index, $question_data, $quiz_view, $quiz, $global_points, $json, $cat_points, $shortcode_atts, $question_count );	
					$index = $r_data['index'];
					$global_points = $r_data['global_points'];
					$json = $r_data['json'];
					$cat_points = $r_data['cat_points'];
					$question_count = $r_data['question_count'];
				}
			}
			 
			$points_array = array(
				'points' => intval( 0 ),
				'correct' => intval( 0 ),
				'possiblePoints' => intval( 0 )
			);
			$points_str       = maybe_serialize( $points_array );
			$user_id          = get_current_user_id();
			$quiz_pro_id      = $quiz->getID();
			$$quiz_post_id    = $quiz_post_id;
			$question_pro_id  = get_post_meta( 34547,'question_pro_id', true );
			$response_str     = '';
			$points_nonce     = 'ld_quiz_pnonce'. $user_id .'_'. $quiz_pro_id .'_'. $quiz_post_id .'_'. $question_pro_id .'_'. $points_str;
			

			$repsonse_nonce = 'ld_quiz_anonce'. $user_id .'_'. $quiz_pro_id .'_'. $quiz_post_id .'_'. $question_pro_id .'_'. $response_str;
		}

		if( 'true' != $is_ps_enabled ) {
			$remaining_questions = array_merge(array_diff($all_questions_keys, $executed_ids), array_diff($executed_ids, $all_questions_keys));
			if ( ! empty( $remaining_questions ) ) {
				foreach ( $remaining_questions as $que ) {
					$question_pro_id = get_post_meta( $que, 'question_pro_id', true );
					$question = $question_mapper->fetchById( intval( $question_pro_id ), null );
					$formated_quiz[$que]['question'] = $question;
				}
			}
			if ( ! empty( $formated_quiz ) ) {
				$data = custom_render_quiz_qestions( $index, $formated_quiz, $quiz_view, $quiz, $global_points, $json, $cat_points, $shortcode_atts, $question_count );
				$index = $data['index'];
				$json = $data['json'];
				$global_points = $data['global_points'];
				$cat_points = $data['cat_points'];
				$question_count = $data['question_count'];
			}
		}
	?>
	</ol>
	<?php if ( $quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE ) { ?>
		<div>
			<input type="button" name="wpProQuiz_pageLeft"
					data-text="<?php
						// translators: placeholder: page number
						echo esc_html__( 'Page %d', 'learndash' );
					?>"
					style="float: left; display: none;" class="wpProQuiz_button wpProQuiz_QuestionButton">
			<input type="button" name="wpProQuiz_pageRight"
					data-text="<?php
						// translators: placeholder: page number
						echo esc_html__( 'Page %d', 'learndash' );
					?>"
					style="float: right; display: none;" class="wpProQuiz_button wpProQuiz_QuestionButton">

			<?php if ( $quiz->isShowReviewQuestion() && ! $quiz->isQuizSummaryHide() ) { ?>
				<input type="button" name="checkSingle" value="<?php echo wp_kses_post(
					SFWD_LMS::get_template(
						'learndash_quiz_messages',
						array(
							'quiz_post_id' => $quiz->getID(),
							'context'      => 'quiz_quiz_summary_button_label',
							'message'      => sprintf(
								// translators: placeholder: Quiz.
								esc_html_x( '%s Summary', 'Quiz Summary', 'learndash' ),
								LearnDash_Custom_Label::get_label( 'quiz' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
							),
						)
					)
				); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right;">
			<?php } else { ?>
				<input type="button" name="checkSingle" value="<?php echo wp_kses_post(
					SFWD_LMS::get_template(
						'learndash_quiz_messages',
						array(
							'quiz_post_id' => $quiz->getID(),
							'context'      => 'quiz_finish_button_label',
							'message'      => sprintf(
								// translators: placeholder: Quiz.
								esc_html_x( 'Finish %s', 'placeholder: Quiz', 'learndash' ),
								LearnDash_Custom_Label::get_label( 'quiz' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
							),
						)
					)
				); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right;">
			<?php } ?>

			<div style="clear: both;"></div>
		</div>
	<?php } ?>
</div>

<?php

$template_part_relative = 'learndash/neo/template-parts/';

include elumine_locate_custom_templates($template_part_relative . 'elumine-quiz-review-box.php');
if ( empty( $global_points ) ) {
	$global_points = 1;
}
return array(
	'globalPoints' => $global_points,
	'json'         => $json,
	'catPoints'    => $cat_points,
);
