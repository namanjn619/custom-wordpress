<?php
function render_break( $break_data, $json, $key ) {
	if( empty( $break_data ) ) {
		$break_data = array();
	}
	$question_post_id = get_option('tts_break_question_post_id');
	$question_mapper  = new WpProQuiz_Model_QuestionMapper();
	$question_pro_id  = get_post_meta( $question_post_id, 'question_pro_id', true );
	$question         = $question_mapper->fetchById( intval( $question_pro_id ), null );
	$question->setQuestionPostId( $question_post_id );
	
	//$question->setAnswerType( '' );
	$question_meta = array(
		'type'             => $question->getAnswerType(),
		'question_pro_id'  => $question->getId(),
		'question_post_id' => $question_post_id,
	);
	$time_sec = ( 3600* $break_data['time']['hours'] ) + ( 60 * $break_data['time']['minutes'])+ ( $break_data['time']['seconds']); 
	//style="" data-type="single" data-question-meta="{&quot;type&quot;:&quot;single&quot;,&quot;question_pro_id&quot;:12136,&quot;question_post_id&quot;:32767}"
	// wdm-break-timer.js
	?>
		<li class="wpProQuiz_listItem wdm-break" data-section="<?php echo $key; ?>" data-section-time="<?php echo $time_sec; ?>" style="display: none;" data-type="<?php echo esc_attr( $question->getAnswerType() ); ?>" data-question-meta="<?php echo htmlspecialchars( wp_json_encode( $question_meta ) ); ?>">
			<div class="wdm-break" id="wdm-break-<?php echo $break_data['id']; ?>" data-id="<?php echo $break_data['id']; ?>">
				<ul class="wpProQuiz_questionList" data-question_id="<?php echo esc_attr( $question->getId() ); ?>" data-type="<?php echo esc_attr( $question->getAnswerType() ); ?>">
					<li class="wpProQuiz_questionListItem" data-pos="0">
						<div class="break-data-container">
							<div class="break-inst"><?php echo $break_data['content']; ?></div>
							<?php if (	'yes' == $break_data['allow_skip'] ): ?>
									<div class="break-skip"><a data-id="<?php echo $break_data['id']; ?>" class="btn tts-next-section">Next Section</a></div>
							<?php endif ?>
						</div>
					</li>
				</ul>
			</div>
		</li>
	<?php
	$json[ $question->getId() ]['type']             = $question->getAnswerType();
	$json[ $question->getId() ]['id']               = (int) $question->getId();
	$json[ $question->getId() ]['question_post_id'] = (int) $question->getQuestionPostId();
	$json[ $question->getId() ]['catId']            = (int) $question->getCategoryId();
	$json[ $question->getId() ]['points']           = 0;
	return $json;
}

function render_section( $quiz_extra_data, $section_data, $key, $indexed_questions ) {
	if ( empty( $section_data ) ) {
		$section_data = array();
	}
	$question_mapper = new WpProQuiz_Model_QuestionMapper();
	if( 'section' == $section_data['type'] ){
		$time_sec = ( 3600* $section_data['time']['hours'] ) + ( 60 * $section_data['time']['minutes'])+ ( $section_data['time']['seconds']);
		error_log('Section Data*************************************');
		// error_log(print_r($section_data,1));
		$last_que = $section_data['question_sets'];
		// end( $last_que );
		// error_log(print_r($last_que,1));
		$last_que_key = array_key_last( $last_que );
		error_log(print_r($last_que_key,1));
		foreach( $section_data['question_sets'] as $q_key => $data ) {
			if ( $data['type'] == 'que' ) {
				if( $q_key == $last_que_key ) {
					$question_data['section_end'] = true;
				}
				$question_pro_id = get_post_meta( $data['question'], 'question_pro_id', true );
				$question = $question_mapper->fetchById( intval( $question_pro_id ), null );
				$question->setQuestionPostId( $data['question'] );
				$question_data['question'] = $question;
				$r_data = render_question_with_format( $quiz_extra_data['index'], $question_data, $quiz_extra_data['quiz_view'], $quiz_extra_data['quiz'], $quiz_extra_data['global_points'], $quiz_extra_data['json'], $quiz_extra_data['cat_points'], $quiz_extra_data['shortcode_atts'], $quiz_extra_data['question_count'], $key, $time_sec, $indexed_questions );
					
					$quiz_extra_data['index'] = $r_data['index'];
					$quiz_extra_data['global_points'] = $r_data['global_points'];
					$quiz_extra_data['json'] = $r_data['json'];
					$quiz_extra_data['cat_points'] = $r_data['cat_points'];
					$quiz_extra_data['question_count'] = $r_data['question_count'];
					$quiz_extra_data['executed_ids'][] = $data['question'];
			} else {
				$first_que = $data['question'][0];
				$last_que  = end($data['question']);
				$set_data  = array(
					'id' => $data['id'],
					'type' => $data['type'],
					'title' => $data['title'],
					'content' => $data['content']
				); 
				foreach ($data['question'] as $q) {
					$question_pro_id = get_post_meta( $q, 'question_pro_id', true );
					$question = $question_mapper->fetchById( intval( $question_pro_id ), null );
					$question->setQuestionPostId( $q );
					$question_data['question'] = $q;
					$question_data['set'] = $set_data;
					$question_data['question'] = $question;
					if ( $q == $first_que ) {
						$question_data['first_question'] = 1;
					}
					if ( $q == $last_que ) {
						$question_data['last_question'] = 1;
					}
					if( $q_key == $last_que_key && $q == $last_que ) {
						$question_data['section_end'] = true;
					}
					$r_data = render_question_with_format( $quiz_extra_data['index'], $question_data, $quiz_extra_data['quiz_view'], $quiz_extra_data['quiz'], $quiz_extra_data['global_points'], $quiz_extra_data['json'], $quiz_extra_data['cat_points'], $quiz_extra_data['shortcode_atts'], $quiz_extra_data['question_count'], $key, $time_sec, $indexed_questions );
					
					$quiz_extra_data['executed_ids'][] = $q;
					$quiz_extra_data['index'] = $r_data['index'];
					$quiz_extra_data['global_points'] = $r_data['global_points'];
					$quiz_extra_data['json'] = $r_data['json'];
					$quiz_extra_data['cat_points'] = $r_data['cat_points'];
					$quiz_extra_data['question_count'] = $r_data['question_count'];
				}
			}
		}
		return $quiz_extra_data;
	}
	?>
		<!-- <li class="wpProQuiz_listItem" style="display: none;">
			<div class="wdm-break" id="wdm-break-"<?php echo $section_data['id']; ?> data-id="<?php echo $section_data['id']; ?>">
				Section <?php echo $section_data['id']; ?>
			</div>
		</li> -->
	<?php
}

function render_question_with_format( $index, $question_data, $quiz_view, $quiz, $global_points, $json, $cat_points, $shortcode_atts, $question_count, $key="no-section", $time_sec="0", $indexed_questions = array() ) {

			$question = $question_data['question'];
			$section_end = false;
			$css_class = ''; 
			if (  isset($question_data['first_question'] ) && ! empty( $question_data['first_question'] ) ) {
				$css_class .= ' rc_set_start'; 
			} 
			if (  isset($question_data['last_question'] ) && ! empty( $question_data['last_question'] ) ) {
				$css_class .= ' rc_set_end'; 
			}

			if( isset($question_data['section_end'] ) && ! empty( $question_data['section_end'] )) {
				$section_end = true; 
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
			<li class="wpProQuiz_listItem <?php echo $css_class; ?>" data-q="<?php echo $index; ?>" data-section="<?php echo $key; ?>" data-section-time="<?php echo $time_sec; ?>" style="display: none;" data-type="<?php echo esc_attr( $question->getAnswerType() ); ?>" data-question-meta="<?php echo htmlspecialchars( wp_json_encode( $question_meta ) ); ?>">
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
							if ( empty( $indexed_questions ) ) {
								$q_no = $index;

							} else {
								$q_no = $indexed_questions[$index];
							}
							echo wp_kses_post(
								SFWD_LMS::get_template(
									'learndash_quiz_messages',
									array(
										'quiz_post_id' => $quiz->getID(),
										'context'      => 'quiz_question_list_1_message',
										'message'      => esc_html__( 'Question', 'learndash' ) .'<span>' . $q_no . '</span>' ,
										'placeholders' => array( $q_no ),
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
						<!-- Write Submit Section Button code -->
						<!-- QQQ -->
						<?php if ( $section_end ): ?>
							<input type="button" name="next" value="<?php echo wp_kses_post(
								SFWD_LMS::get_template(
									'learndash_quiz_messages',
									array(
										'quiz_post_id' => $quiz->getID(),
										'context'      => 'quiz_submit_button_label',
										'message'      => esc_html__( 'Submit Section', 'learndash' ),
									)
								)
							) ?>" class="wpProQuiz_button wpProQuiz_QuestionButton custom-submit-section" style="float: right; display: none;">
						<?php else: ?>
							<input type="button" name="next" value="<?php echo wp_kses_post(
								SFWD_LMS::get_template(
									'learndash_quiz_messages',
									array(
										'quiz_post_id' => $quiz->getID(),
										'context'      => 'quiz_next_button_label',
										'message'      => esc_html__( 'Next', 'learndash' ),
									)
								)
							) ?>" class="wpProQuiz_button wpProQuiz_QuestionButton custom-next-que" style="float: right; display: none;">
						<?php endif; ?>

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

		<?php
		return array(
			'index' => $index,
			'global_points' => $global_points,
			'json' => $json,
			'cat_points' => $cat_points,
			'question_count' => $question_count,

		);
}
add_action( 'wp_ajax_tts_fetch_quiz_sections', 'tts_fetch_quiz_sections' );
function tts_fetch_quiz_sections() {
	// Verify wp nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tts#300' ) ) {
		echo json_encode(
			array(
				'status' => 'error',
				'message' => '<span class="error" style="color: #ff0000; font-weight: 600">' . __( 'Security checks failed!' ) . '</span>',
			)
		);
		exit;
	}

	// check if post id is empty.
	if ( empty( $_POST['quiz_post_id'] ) ) {
		echo json_encode(
			array(
				'status' => 'error',
				'message' => '<span class="error" style="color: #ff0000; font-weight: 600">' . __( 'Please select valid post!' ) . '</span>',
			)
		);
		exit;
	}
	$quiz_post_id = $_POST['quiz_post_id'];
	$is_ps_enabled = get_post_meta( $quiz_post_id, 'tts_practice_set_enabled', true );

	$practice_sets = get_post_meta( $quiz_post_id, 'tts_practice_set_data', true );
	$section_data = array();
	if (!empty($practice_sets)){
		foreach ( $practice_sets as $test_key ) {
			$test_data = get_post_meta( $quiz_post_id, $test_key, true );
			$time = ( 3600* $test_data['time']['hours'] ) + ( 60 * $test_data['time']['minutes'])+ ( $test_data['time']['seconds']);
			$section_data[$test_key] = $time;
		}
	}
	echo json_encode(
		array(
			'status' => 'success',
			'data' => $section_data,
		)
	);
	exit;

}
add_action( 'wp_ajax_tts_fetch_next_section', 'tts_fetch_next_section' );
function tts_fetch_next_section() {
	// Verify wp nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tts#300' ) ) {
		echo json_encode(
			array(
				'status' => 'error',
				'message' => '<span class="error" style="color: #ff0000; font-weight: 600">' . __( 'Security checks failed!' ) . '</span>',
			)
		);
		exit;
	}

	// check if post id is empty.
	if ( empty( $_POST['quiz_post_id'] ) ) {
		echo json_encode(
			array(
				'status' => 'error',
				'message' => '<span class="error" style="color: #ff0000; font-weight: 600">' . __( 'Please select valid post!' ) . '</span>',
			)
		);
		exit;
	}
	if ( empty( $_POST['section'] ) ) {
		echo json_encode(
			array(
				'status' => 'error',
				'message' => '<span class="error" style="color: #ff0000; font-weight: 600">' . __( 'Please select valid post!' ) . '</span>',
			)
		);
		exit;
	}
	$quiz_post_id = $_POST['quiz_post_id'];
	$is_ps_enabled = get_post_meta( $quiz_post_id, 'tts_practice_set_enabled', true );

	$practice_sets = get_post_meta( $quiz_post_id, 'tts_practice_set_data', true );
	$section_data = array();
	if (!empty($practice_sets)){
		foreach ( $practice_sets as $i => $test_key ) {
			if( $_POST['section'] == $test_key ) {
				$next_section = $practice_sets[$i+1];
				$test_data = get_post_meta( $quiz_post_id, $next_section, true );
				$next_time = ( 3600* $test_data['time']['hours'] ) + ( 60 * $test_data['time']['minutes'])+ ( $test_data['time']['seconds']);
						// 	$section_data[$test_key] = $time;
				echo json_encode(
					array(
						'status' => 'success',
						'next_section' => $next_section,
						'next_time' => $next_time,
					)
				);
				break;
			}

		// 	$test_data = get_post_meta( $quiz_post_id, $test_key, true );
		// 	$time = ( 3600* $test_data['time']['hours'] ) + ( 60 * $test_data['time']['minutes'])+ ( $test_data['time']['seconds']);
		// 	$section_data[$test_key] = $time;
		}
	}
	
	// $all_keys = array_keys($section_data);
	// $current_index = array_search( $_POST['section'] , $all_keys );

	// $next_key  = $all_keys[$current_index+1];
	// $next_time = $section_data[$next_key];

	// section
	// echo json_encode(
	// 	array(
	// 		'status' => 'success',
	// 		'next_section' => $next_section,
	// 		'next_time' => $next_time,
	// 	)
	// );
	exit;

}
