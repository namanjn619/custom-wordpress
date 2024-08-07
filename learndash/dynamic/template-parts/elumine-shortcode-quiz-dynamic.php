<div class="single-sfwd-quiz shortcode direct_view">
<main class="course-enrolled">
    <div class="row">
        <section class="enrolled-right wdm-sidebar-right col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="enrolled-content">
                        <div class="topic">
                            <div class="title">
                                <?php
                                //the_title();
                                if (!empty($quiz_post) && 'sfwd-quiz' === $quiz_post->post_type) {
                                    $quiz_id = $quiz_post->ID;
                                } else {
                                    $quiz_id = $atts['quiz_id'];
                                }
                                $el_pro_quiz_id = get_post_meta($quiz_id, 'quiz_pro_id', true);
                                $quizMapper = new \WpProQuiz_Model_QuizMapper();
                                $quizModel = $quizMapper->fetch($el_pro_quiz_id);
                                echo $quizModel->getName();
                                ?>
                            </div>
                            <div class="content">
                            <?php
                            if (!version_compare(LEARNDASH_VERSION, '2.6.0', '>=')) {
                                if (! empty($lesson_progression_enabled) && ! learndash_is_quiz_accessable(null, $quiz_post)) {
                                    if (empty($quiz_settings['lesson'])) {
                                        echo sprintf(
                                            wp_kses_post(
                                                /* translators: %s: Lesson Label */
                                                _x('Please go back and complete the previous %s.', 'placeholder lesson', 'elumine')
                                            ),
                                            LearnDash_Custom_Label::label_to_lower('lesson')
                                        );
                                    } else {
                                        echo sprintf(
                                            wp_kses_post(
                                                /* translators: %s: Topic Label */
                                                _x('Please go back and complete the previous %s.', 'placeholder topic', 'elumine')
                                            ),
                                            LearnDash_Custom_Label::label_to_lower('topic')
                                        );
                                    }
                                }
                            } else {
                                if (!empty($lesson_progression_enabled)) {
                                    $last_incomplete_step = learndash_is_quiz_accessable(null, $quiz_post, true);
                                    if (1 !== $last_incomplete_step) {
                                        if (is_a($last_incomplete_step, 'WP_Post')) {
                                            if ($last_incomplete_step->post_type === learndash_get_post_type_slug('topic')) {
                                                echo sprintf(
                                                    // translators: placeholders: topic URL.
                                                    esc_html_x('Please go back and complete the previous %s.', 'placeholders: topic URL', 'elumine'),
                                                    '<a class="learndash-link-previous-incomplete" href="' . learndash_get_step_permalink($last_incomplete_step->ID, $course_id) . '">' . LearnDash_Custom_Label::label_to_lower('topic') . '</a>'
                                                );
                                            } elseif ($last_incomplete_step->post_type === learndash_get_post_type_slug('lesson')) {
                                                echo sprintf(
                                                    // translators: placeholders: lesson URL.
                                                    esc_html_x('Please go back and complete the previous %s.', 'placeholders: lesson URL', 'elumine'),
                                                    '<a class="learndash-link-previous-incomplete" href="' . learndash_get_step_permalink($last_incomplete_step->ID, $course_id) . '">' . LearnDash_Custom_Label::label_to_lower('lesson') . '</a>'
                                                );
                                            } elseif ($last_incomplete_step->post_type === learndash_get_post_type_slug('quiz')) {
                                                echo sprintf(
                                                    /* translators: placeholders: quiz URL.*/
                                                    esc_html_x('Please go back and complete the previous %s.', 'placeholders: quiz URL', 'elumine'),
                                                    '<a class="learndash-link-previous-incomplete" href="' . learndash_get_step_permalink($last_incomplete_step->ID, $course_id) . '">' . LearnDash_Custom_Label::label_to_lower('quiz') . '</a>'
                                                );
                                            }
                                        }
                                    }
                                }
                            }

                            if ($show_content) {
                                ?>
                                <div class="learndash_content elumine_content"><?php echo $content; ?></div>
                                <?php
                                if ((isset($materials)) && (!empty($materials))) :
                                    ?>
                                        <div id="learndash_topic_materials learndash_quiz_materials" class="learndash_topic_materials learndash_quiz_materials">
                                            <div class="sub-heading">
                                                <h3>
                                                    <span class="ml-0">
                                                        <?php printf(
                                                            /* translators: %s: Quiz Label placeholders: Quiz Materials Label.*/
                                                            _x('%s Materials', 'placeholders: Quiz Materials Label', 'elumine'),
                                                            LearnDash_Custom_Label::get_label('quiz')
                                                        ); ?>
                                                    </span>
                                                </h3>
                                            </div>
                                            <div class="wdm-material-content"><?php echo $materials; ?></div>
                                        </div>
                                        <?php
                                endif;
                                ?>
                                <?php
                                if ($attempts_left) {
                                    echo $quiz_content;
                                } else {
                                    ?>
                                        <p id="learndash_already_taken">
                                            <?php echo sprintf(
                                                _x(
                                                    /* translators: 1: Quiz Label 2: Attempts Count */
                                                    'You have already taken this %1$s %2$d time(s) and may not take it again.',
                                                    'placeholders: quiz, attempts count',
                                                    'elumine'
                                                ),
                                                LearnDash_Custom_Label::label_to_lower('quiz'),
                                                $attempts_count
                                            ); ?>
                                        </p>
                                        <?php
                                }
                            }
                            ?>
                            </div>
                        </div>
                </section>
    </div>
</main>
</div>
