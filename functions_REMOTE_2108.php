<?php

// Currently plugin version.
if ( ! defined( 'ELUMINE_CHILD_VERSION' ) ) {
	define( 'ELUMINE_CHILD_VERSION', '1.0.0' );
}

if ( ! function_exists( 'elumine_child_enqueue_styles' ) ) {
	/*
	*   Function to load parent and child theme CSS
	*
	*/
	function elumine_child_enqueue_styles() {
		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array(), time(), false );
	}
}

if ( ! function_exists( 'elumine_child_theme_slug_setup' ) ) {
	/*
	*   Function to load child theme textdomain
	*
	*/
	function elumine_child_theme_slug_setup() {
		load_child_theme_textdomain( 'elumine-child', get_stylesheet_directory() . '/languages' );
	}
}

/*
* Load Child Theme and parent theme CSS
*
*/
add_action( 'wp_enqueue_scripts', 'elumine_child_enqueue_styles' );

/*
* Load Child theme text-domain
*
*/
add_action( 'after_setup_theme', 'elumine_child_theme_slug_setup' );


function wdm_ld_script(){ 

	wp_enqueue_script( 'wdm_elumine_child_js', get_stylesheet_directory_uri() . '/assets/js/elumine-child.js', array('jquery'), false, false );

	/* 28/03/2022 */
	if ( current_user_can( 'manage_options' ) ) {
		$wdm_user_info = array(
			'user_is'  => "administrator"
		);
	} else {
	   $wdm_user_info = array(
			'user_is'  => ""
		);
	}
	wp_localize_script( 'wdm_elumine_child_js', 'wdm_user_role', $wdm_user_info );
	/* 28/03/2022 */

	wp_enqueue_style( 'wdm_learndash_css', get_stylesheet_directory_uri() . "/assets/css/wdm-learndash.css", array(), false  );
	wp_enqueue_script( 'wdm_mathjax_js', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML', array('jquery'), false, false );
	wp_enqueue_script( 'wdm_learndash_js', get_stylesheet_directory_uri() . '/assets/js/wdm_learndash.js', array('jquery'), false, false );
	wp_enqueue_script( 'wdm_tts_break_js', get_stylesheet_directory_uri() . '/assets/js/wdm_break_timer.js', array('jquery'), false, true );
	$js_arr = array(
			'ajax_url'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('tts#300'),
			'user_id' => get_current_user_id(),
		);
	wp_localize_script( 'wdm_learndash_js', 'tts_ld_data', $js_arr );
	wp_localize_script( 'wdm_tts_break_js', 'tts_ld_data', $js_arr );
	// if ( is_account_page() ) {
	// 	wp_enqueue_script( 'wdm_wc_account', get_stylesheet_directory_uri() . '/assets/js/wdm_wc_account.js', array('jquery'), false, true );
	// }
	// if ( !is_user_logged_in() && is_checkout() ) {
	// 	wp_enqueue_script( 'wdm_userflow', get_stylesheet_directory_uri() . '/assets/js/wdm_checkout_userflow.js', array('jquery'), false, true );
	// 	$cjs_arr = array(
	// 		'ajax_url'  => admin_url( 'admin-ajax.php' ),
	// 		'nonce' => wp_create_nonce('ttscjs#300'),
	// 	);
	// 	wp_localize_script( 'wdm_userflow', 'tts_cjs_data', $cjs_arr );
	// }
}
add_action( 'wp_footer', 'wdm_ld_script' );

/**
 * Render Quiz questions for Reading Comprehension question set 
 */
function custom_render_quiz_qestions( $index, $questions, $quiz_view, $quiz, $global_points, $json, $cat_points, $shortcode_atts, $question_count ) {
	foreach ( $questions as $question_data ) {
			// error_log('Question Data');
			// error_log(print_r($question_data,1));
			$question = $question_data['question'];
			$css_class = ''; 
			if (  isset($question_data['first_question'] ) && ! empty( $question_data['first_question'] ) ) {
				$css_class .= ' rc_set_start'; 
			} 
			if (  isset($question_data['last_question'] ) && ! empty( $question_data['last_question'] ) ) {
				$css_class .= ' rc_set_end'; 
			}

			$index ++;
			$answer_array = $question->getAnswerData();

			$global_points += $question->getPoints();


			$json[ $question->getId() ]['type']             = $question->getAnswerType();
			$json[ $question->getId() ]['id']               = (int) $question->getId();
			$json[ $question->getId() ]['question_post_id'] = (int) $question->getQuestionPostId();
			$json[ $question->getId() ]['catId']            = (int) $question->getCategoryId();

			if ( $question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated() && $question->isDisableCorrect() ) {
				$json[ $question->getId() ]['disCorrect'] = (int) $question->isDisableCorrect();
			}

			if ( ! isset( $cat_points[ $question->getCategoryId() ] ) ) {
				$cat_points[ $question->getCategoryId() ] = 0;
			}

			$cat_points[ $question->getCategoryId() ] += $question->getPoints();

			if ( ! $question->isAnswerPointsActivated() ) {
				$json[ $question->getId() ]['points'] = $question->getPoints();
			}

			if ( $question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated() ) {
				$json[ $question->getId() ]['diffMode'] = 1;
			}

			$question_meta = array(
				'type'             => $question->getAnswerType(),
				'question_pro_id'  => $question->getId(),
				'question_post_id' => $question->getQuestionPostId(),
			);

			?>
			<li class="wpProQuiz_listItem <?php echo $css_class; ?>" style="display: none;" data-type="<?php echo esc_attr( $question->getAnswerType() ); ?>" data-question-meta="<?php echo htmlspecialchars( wp_json_encode( $question_meta ) ); ?>">
				<div class="elumine-top-info">
					<div class="wpProQuiz_question_page" <?php $quiz_view->isDisplayNone( $quiz->getQuizModus() != WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE && ! $quiz->isHideQuestionPositionOverview() ); ?> >
						<label><?php echo esc_html( 'Question' ); ?></label>
					<?php
						echo wp_kses_post(
							SFWD_LMS::get_template(
								'learndash_quiz_messages',
								array(
									'quiz_post_id' => $quiz->getID(),
									'context'      => 'quiz_question_list_2_message',
									'message'      => sprintf(
										// translators: placeholder: question number, questions total
										esc_html_x( '%1$s of %2$s', 'placeholder: question number, questions total', 'learndash' ),
										'<div><span>' . $index . '</span>',
										'<span>' . $question_count . '</span></div>'
									),
									'placeholders' => array( $index, $question_count ),
								)
							)
						);
					?>
					</div>
					<h5 style="<?php echo $quiz->isHideQuestionNumbering() ? 'display: none;' : 'display: inline-flex;'; ?>" class="wpProQuiz_header">
						<div class="el-q-no">
							<?php
								echo wp_kses_post(
									SFWD_LMS::get_template(
										'learndash_quiz_messages',
										array(
											'quiz_post_id' => $quiz->getID(),
											'context'      => 'quiz_question_list_1_message',
											'message'      => esc_html__( 'Question', 'learndash' ) .'<span>' . $index . '</span>' ,
											'placeholders' => array( $index ),
										)
									)
								);
							?>
						</div>

						<?php if ( $quiz->isShowPoints() ) { ?>
						<div
							style="font-weight: bold; float: right;">
							<?php
							echo wp_kses_post(
								SFWD_LMS::get_template(
									'learndash_quiz_messages',
									array(
										'quiz_post_id' => $quiz->getID(),
										'context'      => 'quiz_question_points_message',
										// translators: placeholder: total quiz points.
										'message'      => sprintf( esc_html_x( '%s point(s)', 'placeholder: total quiz points', 'learndash' ), '<span>' . $question->getPoints() . '</span>' ),
										'placeholders' => array( $question->getPoints() ),
									)
								)
							);

							?>
							</div>
					<?php } ?>

					</h5>
				</div>
				<div class="tts-que-container">
					<div class="row">
						<?php
							if ( isset( $question_data['set'] ) && ! empty( $question_data['set'] ) ) {
								?>
								<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 rc-content">
									<?php  echo  $question_data['set']['content']; ?>
								</div>
								<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 rc-question">
								<?php
							} else {
								?>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tts-question">
								<?php
							}
						?>
				<?php if ( $question->getCategoryId() && $quiz->isShowCategory() ) { ?>
					<div style="font-weight: bold; padding-top: 5px;">
						<?php
							echo wp_kses_post(
								SFWD_LMS::get_template(
									'learndash_quiz_messages',
									array(
										'quiz_post_id' => $quiz->getID(),
										'context'      => 'quiz_question_category_message',
										// translators: placeholder: Quiz Category.
										'message'      => sprintf( esc_html_x( 'Category: %s', 'placeholder: Quiz Category', 'learndash' ), '<span>' . esc_html( $question->getCategoryName() ) . '</span>' ),
										'placeholders' => array( esc_html( $question->getCategoryName() ) ),
									)
								)
							);
						?>
					</div>
				<?php } ?>
				<div class="wpProQuiz_question" style="margin: 20px 0px 0px 0px;">
					<div class="wpProQuiz_question_text">
						<?php
							$wpproquiz_question_text = $question->getQuestion();
							$wpproquiz_question_text = sanitize_post_field( 'post_content', $wpproquiz_question_text, 0, 'display' );
							$wpproquiz_question_text = wpautop( $wpproquiz_question_text );
							global $wp_embed;
							$wpproquiz_question_text = $wp_embed->run_shortcode( $wpproquiz_question_text );
							$wpproquiz_question_text = do_shortcode( $wpproquiz_question_text );
							echo $wpproquiz_question_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to allow HTML / shortcode output
						?>
					</div>
					<p class="wpProQuiz_clear" style="clear:both;"></p>

					<?php
					/**
					 * Matrix Sort Answer
					 */
					?>
					<?php if ( $question->getAnswerType() === 'matrix_sort_answer' ) { ?>
						<div class="wpProQuiz_matrixSortString">
							<h5 class="wpProQuiz_header">
							<?php
							echo wp_kses_post(
								SFWD_LMS::get_template(
									'learndash_quiz_messages',
									array(
										'quiz_post_id' => $quiz->getID(),
										'context'      => 'quiz_question_sort_elements_header',
										'message'      => esc_html__( 'Sort elements', 'learndash' ),
									)
								)
							);
							?>
							</h5>
							<ul class="wpProQuiz_sortStringList">
							<?php
							$answer_array_new_matrix = array();
							foreach ( $answer_array as $q_idx => $q ) {
								$datapos                             = LD_QuizPro::datapos( $question->getId(), $q_idx );
								$answer_array_new_matrix[ $datapos ] = $q;
							}

							$matrix = array();
							foreach ( $answer_array as $k => $v ) {
								$matrix[ $k ][] = $k;

								foreach ( $answer_array as $k2 => $v2 ) {
									if ( $k != $k2 ) {
										if ( $v->getAnswer() == $v2->getAnswer() ) {
											$matrix[ $k ][] = $k2;
										} else if ( $v->getSortString() == $v2->getSortString() ) {
											$matrix[ $k ][] = $k2;
										}
									}
								}
							}

								foreach ( $answer_array as $k => $v ) {
								?>
								<li class="wpProQuiz_sortStringItem" data-pos="<?php echo esc_attr( $k ); ?>">
								<?php echo $v->isSortStringHtml() ? do_shortcode( nl2br( $v->getSortString() ) ) : esc_html( $v->getSortString() ); ?>
								</li>
								<?php
								}

								$answer_array = $answer_array_new_matrix;
							?>
							</ul>
							<div style="clear: both;"></div>
						</div>
						<label class="el-instructions"><?php echo esc_html( 'Place your answers' ); ?></label>
					<?php } ?>

					<?php
					/**
					 * Print questions in a list for all other answer types
					 */
					?>
					<ul class="wpProQuiz_questionList" data-question_id="<?php echo esc_attr( $question->getId() ); ?>"
						data-type="<?php echo esc_attr( $question->getAnswerType() ); ?>">
						<?php
						if ( $question->getAnswerType() === 'sort_answer' ) {
							$answer_array_new = array();
							foreach ( $answer_array as $q_idx => $q ) {
								$datapos = LD_QuizPro::datapos( $question->getId(), $q_idx );
								$answer_array_new[ $datapos ] = $q;
							}
							$answer_array = $answer_array_new;

							if ( $question->getAnswerType() === 'sort_answer' ) {
								$answer_array_org_keys = array_keys( $answer_array );

								/**
								 * Do this while the answer keys match. I just don't trust shuffle to always
								 * return something other than the original.
								 */
								$random_tries = 0;
								while( true ) {
									// Backup so we don't get stuck because some plugin rewrote a function we are using.
									++$random_tries;

									$answer_array_randon_keys = $answer_array_org_keys;
									shuffle( $answer_array_randon_keys );
									$answer_array_keys_diff = array_diff_assoc( $answer_array_org_keys, $answer_array_randon_keys );

									// If the diff array is not empty or we have reaches enough tries, abort.
									if ( ( ! empty( $answer_array_keys_diff ) ) || ( $random_tries > 10 ) ) {
										break;
									}
								}

								$answer_array_new = array();
								foreach ( $answer_array_randon_keys as $q_idx ) {
									if ( isset( $answer_array[ $q_idx ] ) ) {
										$answer_array_new[ $q_idx ] = $answer_array[ $q_idx ];
									}
								}
								$answer_array = $answer_array_new;
							}
						}

						$answer_index = 0;
						foreach ( $answer_array as $v_idx => $v ) {
							$answer_text = $v->isHtml() ? do_shortcode( nl2br( $v->getAnswer() ) ) : esc_html( $v->getAnswer() );

							if ( '' == $answer_text && ! $v->isGraded() ) {
								continue;
							}

							if ( $question->isAnswerPointsActivated() ) {
								$json[ $question->getId() ]['points'][] = $v->getPoints();
							}

							$datapos = $answer_index;
							if ( $question->getAnswerType() === 'sort_answer' || $question->getAnswerType() === 'matrix_sort_answer' ) {
								$datapos = $v_idx; //LD_QuizPro::datapos( $question->getId(), $answer_index );
							}
							?>

							<li class="wpProQuiz_questionListItem" data-pos="<?php echo esc_attr( $datapos ); ?>">
								<?php
								/**
								 *  Single/Multiple
								 */
								if ( $question->getAnswerType() === 'single' || $question->getAnswerType() === 'multiple' ) {
									$json[ $question->getId() ]['correct'][] = (int) $v->isCorrect();
									?>
									<span <?php echo $quiz->isNumberedAnswer() ? '' : 'style="display:none;"'; ?>></span>
									<label>
										<span class="el-effect"></span>
										<input class="wpProQuiz_questionInput"
												type="<?php echo $question->getAnswerType() === 'single' ? 'radio' : 'checkbox'; ?>"
												name="question_<?php echo esc_attr( $quiz->getId() ); ?>_<?php echo esc_attr( $question->getId() ); ?>"
												value="<?php echo esc_attr( ( $answer_index + 1 ) ); ?>"> <?php echo $answer_text; ?>
									</label>

									<?php
									/**
									 *  Sort Answer
									 */
								} elseif ( $question->getAnswerType() === 'sort_answer' ) {
									$json[ $question->getId() ]['correct'][] = (int) $answer_index;
									?>
									<div class="wpProQuiz_sortable">
										<?php echo $answer_text; ?>
									</div>

									<?php
									/**
									 *  Free Answer
									 */
								} elseif ( $question->getAnswerType() === 'free_answer' ) {
									$json[ $question->getId() ]['correct'] = $quiz_view->getFreeCorrect( $v );
									?>
									<label>
										<input class="wpProQuiz_questionInput" type="text"
												name="question_<?php echo esc_attr( $quiz->getId() ); ?>_<?php echo esc_attr( $question->getId() ); ?>"
												style="width: 300px;">
									</label>

									<?php
									/**
									 *  Matrix Sort Answer
									 */
								} elseif ( $question->getAnswerType() === 'matrix_sort_answer' ) {
									$json[ $question->getId() ]['correct'][] = (int) $answer_index;
									$msacw_value                             = $question->getMatrixSortAnswerCriteriaWidth() > 0 ? $question->getMatrixSortAnswerCriteriaWidth() : 20;
									?>
									<table>
										<tbody>
										<tr class="wpProQuiz_mextrixTr">
											<td width="<?php echo esc_attr( $msacw_value ); ?>%">
												<div
													class="wpProQuiz_maxtrixSortText"><?php echo $answer_text; ?></div>
											</td>
											<td width="<?php echo esc_attr( 100 - $msacw_value ); ?>%">
												<ul class="wpProQuiz_maxtrixSortCriterion">
													<span><?php echo esc_html( 'Drag your answer here' ); ?></span>
												</ul>
											</td>
										</tr>
										</tbody>
									</table>

									<?php
									/**
									 *  Cloze Answer
									 */
								} elseif ( $question->getAnswerType() === 'cloze_answer' ) {
									$cloze_data = fetchQuestionCloze( $v->getAnswer() );

									$quiz_view->_clozeTemp = isset( $cloze_data['data'] ) ? $cloze_data['data'] : [];

									$json[ $question->getId() ]['correct'] = isset( $cloze_data['correct'] ) ? $cloze_data['correct'] : [];

									if ( $question->isAnswerPointsActivated() ) {
										$json[ $question->getId() ]['points'] = $cloze_data['points'];
									}

									// Added the wpautop in LD 2.2.1 to retain line-break formatting.
									$cloze_data['replace'] = wpautop( $cloze_data['replace'] );
									$cloze_data['replace'] = sanitize_post_field( 'post_content', $cloze_data['replace'], 0, 'display' );
									$cloze_data['replace'] = do_shortcode( $cloze_data['replace'] );

									$cloze = $cloze_data['replace'];

									echo preg_replace_callback(
										'#@@wpProQuizCloze@@#im',
										array(
											$quiz_view,
											'clozeCallback',
										),
										$cloze
									);

									/**
									 *  Assessment answer
									 */
								} elseif ( $question->getAnswerType() === 'assessment_answer' ) {
									$assessment_data = $quiz_view->fetchAssessment( $v->getAnswer(), $quiz->getId(), $question->getId() );
									$assessment      = sanitize_post_field( 'post_content', $assessment_data['replace'], 0, 'display' );
									$assessment      = wpautop( $assessment );
									$assessment      = do_shortcode( $assessment );
									$assessment      = preg_replace_callback(
										'#@@wpProQuizAssessment@@#im',
										array(
											$quiz_view,
											'assessmentCallback',
										),
										$assessment
									);

									/** This filter is documented in includes/lib/wp-pro-quiz/wp-pro-quiz.php */
									$assessment = apply_filters( 'learndash_quiz_question_answer_postprocess', $assessment, 'assessment' );
									$assessment = do_shortcode( $assessment );
									echo $assessment; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML / Shortcodes

									/**
									 * Essay answer
									 */
								} elseif ( $question->getAnswerType() === 'essay' ) {
									if ( $v->getGradedType() === 'text' ) :
										
										$placeholder = wp_kses_post(
											SFWD_LMS::get_template(
												'learndash_quiz_messages',
												array(
													'quiz_post_id' => $quiz->getID(),
													'context'      => 'quiz_essay_question_textarea_placeholder_message',
													'message'      => esc_html__( 'Type your response here', 'learndash' ),
												)
											)
										);
										?>
										<textarea class="wpProQuiz_questionEssay" rows="3" cols="40"
											name="question_<?php echo esc_attr( $quiz->getId() ); ?>_<?php echo esc_attr( $question->getId() ); ?>"
											id="wpProQuiz_questionEssay_question_<?php echo esc_attr( $quiz->getId() ); ?>_<?php echo esc_attr( $question->getId() ); ?>"
											cols="30" autocomplete="off"
											rows="10" placeholder="<?php echo $placeholder; ?>"></textarea>
										<?php elseif ( $v->getGradedType() === 'upload' ) : ?>
											<?php
												echo wp_kses_post(
													SFWD_LMS::get_template(
														'learndash_quiz_messages',
														array(
															'quiz_post_id' => $quiz->getID(),
															'context'      => 'quiz_essay_question_upload_answer_message',
															'message'      => '<p>' . esc_html__( 'Upload your answer to this question.', 'learndash' ) . '</p>',
														)
													)
												);
											?>
											<form enctype="multipart/form-data" method="post" name="uploadEssay">
												<input type='file' name='uploadEssay[]' id='uploadEssay_<?php echo esc_attr( $question->getId() ); ?>' size='35' class='wpProQuiz_upload_essay' />
												<input type="submit" id='uploadEssaySubmit_<?php echo esc_attr( $question->getId() ); ?>' value="<?php esc_html_e( 'Upload', 'learndash' ); ?>" />
												<input type="hidden" id="_uploadEssay_nonce_<?php echo esc_attr( $question->getId() ); ?>" name="_uploadEssay_nonce" value="<?php echo esc_attr( wp_create_nonce( 'learndash-upload-essay-' . $question->getId() ) ); ?>" />
												<input type="hidden" class="uploadEssayFile" id='uploadEssayFile_<?php echo esc_attr( $question->getId() ); ?>' value="" />
											</form>
											<div id="uploadEssayMessage_<?php echo esc_attr( $question->getId() ); ?>" class="uploadEssayMessage"></div>
										<?php else : ?>
											<?php esc_html_e( 'Essay type not found', 'learndash' ); ?>
										<?php endif; ?>

										<p class="graded-disclaimer">
											<?php if ( 'graded-full' == $v->getGradingProgression() ) : ?>
												<?php
												echo wp_kses_post(
													SFWD_LMS::get_template(
														'learndash_quiz_messages',
														array(
															'quiz_post_id' => $quiz->getID(),
															'context'      => 'quiz_essay_question_graded_full_message',
															'message'      => esc_html__( 'This response will be awarded full points automatically, but it can be reviewed and adjusted after submission.', 'learndash' ),
														)
													)
												);
												?>
											<?php elseif ( 'not-graded-full' == $v->getGradingProgression() ) : ?>
												<?php
													echo wp_kses_post(
														SFWD_LMS::get_template(
															'learndash_quiz_messages',
															array(
																'quiz_post_id' => $quiz->getID(),
																'context'      => 'quiz_essay_question_not_graded_full_message',
																'message'      => esc_html__( 'This response will be awarded full points automatically, but it will be reviewed and possibly adjusted after submission.', 'learndash' ),
															)
														)
													);
												?>
											<?php elseif ( 'not-graded-none' == $v->getGradingProgression() ) : ?>
												<?php
													echo wp_kses_post(
														SFWD_LMS::get_template(
															'learndash_quiz_messages',
															array(
																'quiz_post_id' => $quiz->getID(),
																'context'      => 'quiz_essay_question_not_graded_none_message',
																'message'      => esc_html__( 'This response will be reviewed and graded after submission.', 'learndash' ),
															)
														)
													);
												?>
											<?php endif; ?>
										</p>
									<?php
								}

								?>
							</li>
							<?php
							$answer_index ++;
						}
						?>
					</ul>
				</div>
				<?php if ( ! $quiz->isHideAnswerMessageBox() ) { ?>
					<div class="wpProQuiz_response" style="display: none;">
						<div style="display: none;" class="wpProQuiz_correct">
							<?php if ( $question->isShowPointsInBox() && $question->isAnswerPointsActivated() ) { ?>
								<div>
									<span class="wpProQuiz_response_correct_label" style="float: left;">
									<?php
										echo wp_kses_post(
											SFWD_LMS::get_template(
												'learndash_quiz_messages',
												array(
													'quiz_post_id' => $quiz->getID(),
													'context'      => 'quiz_question_answer_correct_message',
													'message'      => esc_html__( 'Correct', 'learndash' ),
												)
											)
										);
									?>
									</span>
									<span class="wpProQuiz_response_correct_points_label" style="float: right;">
										<?php echo esc_html( $question->getPoints() ) . ' / ' . esc_html( $question->getPoints() ); ?>
										<?php
										echo wp_kses_post(
											SFWD_LMS::get_template(
												'learndash_quiz_messages',
												array(
													'quiz_post_id' => $quiz->getID(),
													'context'      => 'quiz_question_answer_points_message',
													'message'      => esc_html__( 'Points', 'learndash' ),
												)
											)
										);
										?>
									</span>
									<div style="clear: both;"></div>
								</div>
							<?php } elseif ( 'essay' == $question->getAnswerType() ) { ?>
								<?php
								echo wp_kses_post(
									SFWD_LMS::get_template(
										'learndash_quiz_messages',
										array(
											'quiz_post_id' => $quiz->getID(),
											'context'      => 'quiz_essay_question_graded_review_message',
											'message'      => esc_html__( 'Grading can be reviewed and adjusted.', 'learndash' ),
										)
									)
								);
								?>
							<?php } else { ?>
								<span>
								<?php
								echo wp_kses_post(
									SFWD_LMS::get_template(
										'learndash_quiz_messages',
										array(
											'quiz_post_id' => $quiz->getID(),
											'context'      => 'quiz_question_answer_correct_message',
											'message'      => esc_html__( 'Correct', 'learndash' ),
										)
									)
								);
								?>
								</span>
							<?php } ?>
							<p class="wpProQuiz_AnswerMessage">
							</p>
						</div>
						<div style="display: none;" class="wpProQuiz_incorrect">
							<?php if ( $question->isShowPointsInBox() && $question->isAnswerPointsActivated() ) { ?>
								<div>
									<span style="float: left;">
										<?php
											echo wp_kses_post(
												SFWD_LMS::get_template(
													'learndash_quiz_messages',
													array(
														'quiz_post_id' => $quiz->getID(),
														'context'      => 'quiz_question_answer_incorrect_message',
														'message'      => esc_html__( 'Incorrect', 'learndash' ),
													)
												)
											);
										?>
									</span>
									<span style="float: right;"><span class="wpProQuiz_responsePoints"></span> / <?php echo esc_html( $question->getPoints() ); ?>
									<?php
										echo wp_kses_post(
											SFWD_LMS::get_template(
												'learndash_quiz_messages',
												array(
													'quiz_post_id' => $quiz->getID(),
													'context'      => 'quiz_question_answer_points_message',
													'message'      => esc_html__( 'Points', 'learndash' ),
												)
											)
										);
									?>
									</span>

									<div style="clear: both;"></div>
								</div>
							<?php } elseif ( 'essay' == $question->getAnswerType() ) { ?>
								<?php
								echo wp_kses_post(
									SFWD_LMS::get_template(
										'learndash_quiz_messages',
										array(
											'quiz_post_id' => $quiz->getID(),
											'context'      => 'quiz_essay_question_graded_review_message',
											'message'      => esc_html__( 'Grading can be reviewed and adjusted.', 'learndash' ),
										)
									)
								);
								?>
							<?php } else { ?>
								<span>
								<?php
								echo wp_kses_post(
									SFWD_LMS::get_template(
										'learndash_quiz_messages',
										array(
											'quiz_post_id' => $quiz->getID(),
											'context'      => 'quiz_question_answer_incorrect_message',
											'message'      => esc_html__( 'Incorrect', 'learndash' ),
										)
									)
								);
								?>
							</span>
							<?php } ?>
							<p class="wpProQuiz_AnswerMessage"></p>
						</div>
					</div>
				<?php } ?>

				

				<div class="el-actions-wrap">
					<div class="el-left-actions">
						<input type="button" name="back" value="<?php echo wp_kses_post(
							SFWD_LMS::get_template(
								'learndash_quiz_messages',
								array(
									'quiz_post_id' => $quiz->getID(),
									'context'      => 'quiz_back_button_label',
									'message'      => esc_html__( 'Back', 'learndash' ),
								)
							)
						) ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: left ; margin-right: 10px ; display: none;">
						
						<?php if ( $question->isTipEnabled() ) { ?>
							<div class="el-hint">
								<div class="wpProQuiz_tipp" style="display: none; position: relative;">
									<div>
										<i class="modern-icon-Close"></i>
										<h5 style="margin: 0px 0px 10px;" class="wpProQuiz_header">
											<i class="modern-icon-Hint"></i>
										<?php
											echo wp_kses_post(
												SFWD_LMS::get_template(
													'learndash_quiz_messages',
													array(
														'quiz_post_id' => $quiz->getID(),
														'context'      => 'quiz_hint_header',
														'message'      => esc_html__( 'Hint', 'learndash' ),
													)
												)
											);
										?>
										</h5>
										<?php
										echo do_shortcode( apply_filters( 'comment_text', $question->getTipMsg(), null, null ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook
										?>
									</div>
								</div>
								<input type="button" name="tip" value="<?php echo wp_kses_post(
									SFWD_LMS::get_template(
										'learndash_quiz_messages',
										array(
											'quiz_post_id' => $quiz->getID(),
											'context'      => 'quiz_hint_button_label',
											'message'      => esc_html__( 'Hint', 'learndash' ),
										)
									)
								) ?>" class="wpProQuiz_button wpProQuiz_QuestionButton wpProQuiz_TipButton" style="float: left ; display: inline-block; margin-right: 10px ;">
								<i class="modern-icon-Hint"></i>
							</div>
						<?php } ?>
					</div>
					<div class="el-right-actions">
						<?php if ( $quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK && ! $quiz->isSkipQuestionDisabled() && $quiz->isShowReviewQuestion() ) { ?>
							<input type="button" name="skip" value="<?php echo wp_kses_post(
								SFWD_LMS::get_template(
									'learndash_quiz_messages',
									array(
										'quiz_post_id' => $quiz->getID(),
										'context'      => 'quiz_skip_button_label',
										'message'      => esc_html__( 'Skip this question', 'learndash' ),
									)
								)
							) ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: left;">
						<?php } ?>
						<?php if (learndash_get_setting( $quiz->getPostId(), 'showReviewQuestion' )) : ?>
							<div class="wpProQuiz_reviewDiv">
								<?php
									echo SFWD_LMS::get_template( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.
										'quiz/partials/show_quiz_review_buttons.php',
										array(
											'quiz_view'      => $quiz_view,
											'quiz'           => $quiz,
											'shortcode_atts' => $shortcode_atts,
										)
									);
								?>
							</div>
						<?php endif; ?>

						<input type="button" name="check" value="<?php echo wp_kses_post(
							SFWD_LMS::get_template(
								'learndash_quiz_messages',
								array(
									'quiz_post_id' => $quiz->getID(),
									'context'      => 'quiz_check_button_label',
									'message'      => esc_html__( 'Check', 'learndash' ),
								)
							)
						) ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right ; display: none;">
						<input type="button" name="next" value="<?php echo wp_kses_post(
							SFWD_LMS::get_template(
								'learndash_quiz_messages',
								array(
									'quiz_post_id' => $quiz->getID(),
									'context'      => 'quiz_next_button_label',
									'message'      => esc_html__( 'Next', 'learndash' ),
								)
							)
						) ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right; display: none;">

						<div style="clear: both;"></div>
					</div>
				</div>

				<?php if ( $quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE ) { ?>
					<div style="margin-bottom: 20px;"></div>
				<?php } ?>
				</div> <!-- WDM QUE DIV END -->
					</div> <!-- WDM ROW DIV END -->
				</div> <!-- WDM PARENT DIV END -->
			</li>

		<?php }
		return array(
			'index' => $index,
			'global_points' => $global_points,
			'json' => $json,
			'cat_points' => $cat_points,
			'question_count' => $question_count,

		);
}

// Include practice testes file
include get_stylesheet_directory() . '/includes/tts-practice-tests.php';


function tts_check_js_style() { ?>

	<style type="text/css">
	.tts_no_js_cookie_alert{ 
			padding: 20px;
	  	background-color: #f47036;
	  	color: white;
			padding: 0.5em;
			text-align: center;
			width: 80%;
			margin: auto;
	}
	#tts_no_js_warning{ 
		position:fixed;
		left:0;
		top:20%;
		font-size: 1.2em;
		z-index: 10000000 !important;
		padding:10px;
		width: 100%;
	}

	</style>
<?php
}

function tts_check_js_conditions() {
	$output = "";	
	$output .= '<noscript><div id="tts_no_js_warning"><p class="tts_no_js_cookie_alert">Please enable JavaScript to properly view and interact with this site.</p></div></noscript>';
	?>
	<!-- <script type="text/javascript">
	var cookieEnabled=(navigator.cookieEnabled)? true : false;   
	if (typeof navigator.cookieEnabled=="undefined" && !cookieEnabled){ 
		document.cookie="testcookie";
	    cookieEnabled=(document.cookie.indexOf("testcookie")!=-1)? true : false;
	}
	if(!cookieEnabled){
		document.write('<div id="tts_no_js_warning"><p class="tts_no_js_cookie_alert">Many features on this website require Cookie. You can enable Cookie via your browser\'s preference settings.</p></div>');
	}
	</script>     -->
	<?php
	echo $output;
}

add_action( 'wp_head', 'tts_check_js_style' );//add CSS to header
add_action( 'wp_footer', 'tts_check_js_conditions' );


add_filter('woocommerce_get_children', 'sort_vartaion_id', 10, 1);
function sort_vartaion_id( $ids ) {
	sort($ids);
	return $ids;
}

function clear_br($content){ 
     return str_replace("<br/>","<br clear='none'/>", $content);
}

add_filter('the_content','clear_br');


// remove_filter( 'the_excerpt', 'wpautop' );
// remove_filter( 'the_content', 'wpautop' );

/**
 * Get Practice test Question number
 * @param ld_quiz_post_id
 * @param section
 */
function get_practice_test_que_number( $quiz_post_id, $sections ) {
	$qd = 1;
	$question_array = array();
	foreach ( $sections as $test_key ) {
		$test_data = get_post_meta( $quiz_post_id, $test_key, true ); 
		if( 'section' == $test_data['type'] ){
				$qqd = 1;
				$question_array[$qd] = $qqd;
				foreach( $test_data['question_sets'] as $data ) {
					$question_array[$qd] = $qqd;
					if ( $data['type'] == 'que' ) {
						$qqd++;
						$qd++;
					} else {
						foreach ($data['question'] as $q) {
							$question_array[$qd] = $qqd;
							$qqd++;
							$qd++;
						}
					}
				}
		}
	}
	return $question_array;
}

add_action( 'learndash_course_completed', 'tts_redirect_to_myaccount_page', 20, 1 );

function tts_redirect_to_myaccount_page( $data ) {
	$page_url = get_permalink( get_option('woocommerce_myaccount_page_id') );
	if ( empty( $page_url ) ) {
		$page_url = get_site_url();
	}
	wp_redirect( $page_url );
	die();
}



