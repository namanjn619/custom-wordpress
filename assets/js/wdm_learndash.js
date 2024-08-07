all_sections_time = '';
jQuery(document).ready(function(){
	
jQuery('.wdm-timer-nav').hide();
	var delay = 1000; 
	setTimeout(function() { 
	jQuery('.elementor-accordion .elementor-tab-title').removeClass('elementor-active');
	jQuery('.elementor-accordion .elementor-tab-content').css('display', 'none'); }, delay);
	// switchCSS(jQuery(this).width());
	loadMathJaxConfig();
	// jQuery(window).resize(function() {
	// 	switchCSS(jQuery(this).width());
	// });
	
	/**
	 * Feature Name: Crossout Questions
	 * This will add cross to each choice
	 * Trello Link: https://trello.com/c/Q4dVIyk1
	 */
	if ( ! jQuery('.wpProQuiz_quiz').hasClass('view-mode') ) {
		jQuery('.wpProQuiz_questionListItem').each(function(i, el) {
				var input = jQuery(el).find('input');
				var label = jQuery(el).find('label');
				if ( input.is(':checkbox') || input.is(':radio' ) ) {
					jQuery(this).prepend("<div class='wdm-cross'><i class='fa fa-times' aria-hidden='strikethrough' title='Strikethrough'></i></div>");
					label.append( '<div class="wdm-cross-border"></div>' );
				}
		});
	}
	
	jQuery(document).on( 'click' , '.tts-collapse-header', function(){
		var parent = jQuery(this).parents('.tts-collapse');
		 if ( parent.hasClass('tts-collapse-active')) {
			parent.removeClass('tts-collapse-active');
			parent.find('.tts-collapse-header').html('<span>More Information</span>');

		 } else {
			parent.addClass('tts-collapse-active');
			parent.find('.tts-collapse-header').html('<span>Less Information</span>');
		}
	});
	jQuery(document).on('click','.tts-next-section', function(e){
		var parent       = jQuery(this).parents('li.wpProQuiz_listItem');
		var section      = parent.data('section');
		var super_parent = jQuery(this).parents('.wpProQuiz_content');
		var quiz_meta    = super_parent.data('quiz-meta');
		var n_que        = parent.next().data('q');
		var interval     = jQuery.cookie('tts-current-counter-' + tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id );
		
		window.clearInterval( interval );
		jQuery.cookie( section+'-'+tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, 0 );
		jQuery('.wpProQuiz_reviewQuestion.wdm-global').find('[data-q="'+n_que+'"]').click();

		var next_section   = parent.next('li');
		var n_section      = next_section.data('section');
		var n_section_time = next_section.data('section-time');
		var next_que = next_section.data('q');
		
		jQuery.cookie('tts-current-section-' + tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, n_section );
		jQuery.cookie( n_section+'-'+tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, n_section_time );
		jQuery('.tts-custom-section-nav li.tts-custom-que-nav').each(function(){
			if (jQuery(this).hasClass( "custom_wpProQuiz_reviewQuestionTarget" ) ) {
			  jQuery(this).removeClass("custom_wpProQuiz_reviewQuestionTarget");
			}
		});
		jQuery('.tts-custom-section-nav#'+n_section+' li.tts-custom-que-nav').each(function(){
			var q = jQuery(this).data('q');
			if (q == next_que ){
				jQuery(this).addClass('custom_wpProQuiz_reviewQuestionTarget');
			}
		});
		if( ! next_section.hasClass('wdm-break') ) {
			jQuery('.tts-custom-section-nav#'+section).hide();
			jQuery('.tts-custom-section-nav#'+n_section).show();
		}
		jQuery('.tts-view-list .'+n_section ).addClass('tts-ps-active-sec');
		jQuery('.tts-view-list .'+section ).removeClass('tts-ps-active-sec');
		jQuery('.tts-view-list .'+section ).addClass('tts-disabled-section');
		start_timer(quiz_meta,n_section,n_section_time);
	});
	 
	jQuery('input.tts-practice-test-quiz').bind('click', function(e){ 
		jQuery('.wdm-timer-nav').css('display','none');
		if(jQuery('.wpProQuiz_time_limit').hasClass('tts-ps-timer-enabled')){
			jQuery('.wpProQuiz_time_limit.tts-ps-timer-enabled').hide();
			jQuery('.tts-custom-timer').show();
		}
		var super_parent = jQuery(this).parents('.wpProQuiz_content');
		var quiz_meta    = super_parent.data('quiz-meta');
		// jQuery.cookie('ldadv-time-limit-' + tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, 0 );
		// jQuery(".wpProQuiz_reviewQuestion").hide();
		jQuery('.tts-custom-section-nav ol li.tts-custom-que-nav').unbind();
		jQuery('.tts-custom-section-nav li.tts-custom-que-nav').bind('click', function(){
			jQuery('.tts-custom-section-nav li.tts-custom-que-nav').each(function(){
				if (jQuery(this).hasClass( "custom_wpProQuiz_reviewQuestionTarget" ) ) {
				  jQuery(this).removeClass("custom_wpProQuiz_reviewQuestionTarget");
				}
			});
			jQuery(this).addClass('custom_wpProQuiz_reviewQuestionTarget');
			var section = jQuery(this).data('section');
			var q = jQuery(this).data('q');
			jQuery('.wpProQuiz_reviewQuestion.wdm-global').find('[data-section="' + section + '"]').each(function() {
		  		if( jQuery( this ).data('q') == q ){
		  			jQuery(this).click();
		  		}
			});
		});
		var parent = jQuery(this).parents('li.wpProQuiz_listItem');
		var current_que = parent.data('q');
		var current_section = parent.data('section');
		var next_question = parent.next('li');
		var next_que = next_question.data('q'); 
		var next_que_section = next_question.data('section');
		if( jQuery('.wpProQuiz_reviewQuestion.wdm-global').find('[data-q="' + current_que + '"]').hasClass('wpProQuiz_reviewQuestionSolved') ){
			jQuery('.tts-custom-section-nav#'+current_section+' li.tts-custom-que-nav').each(function(){
				if( current_que == jQuery(this).data('q') ) {
					jQuery(this).addClass('wpProQuiz_reviewQuestionSolved');
				}
			});
		}
		jQuery('.tts-custom-section-nav li.tts-custom-que-nav').each(function(){
			if (jQuery(this).hasClass( "custom_wpProQuiz_reviewQuestionTarget" ) ) {
			  jQuery(this).removeClass("custom_wpProQuiz_reviewQuestionTarget");
			}
		});
		
		var all_sections = '';
		jQuery.ajax({
			type: 'post',
			dataType: 'json',
			async: false,
			url: tts_ld_data.ajax_url,
			data: {
				action:'tts_fetch_quiz_sections',
				nonce: tts_ld_data.nonce,
				quiz_post_id:quiz_meta.quiz_post_id,
				quiz_pro_id:quiz_meta.quiz_pro_id,
			},
			success : function( response ){
				if ( 'success' == response.status ) {
					all_sections_time = response.data;
				}
			 }
		});
		
		var first_ele =  jQuery( "li.wpProQuiz_listItem").first();
    	var section   = first_ele.data('section');
    	var time      = first_ele.data('section-time');
		if( ! first_ele.hasClass('wdm-break') ) {
			jQuery('.tts-custom-section-nav#'+section).show();
		}

		jQuery.cookie('tts-current-section-' + tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, section );
		jQuery.cookie( section+'-'+tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, time );
		
		jQuery('.tts-view-list .'+section ).addClass('tts-ps-active-sec');
		// jQuery('.tts-view-list .'+section ).removeClass('tts-ps-active-sec');
		// jQuery('.tts-view-list .'+section ).addClass('tts-disabled-section');
		
		start_timer( quiz_meta ,section, time );
	});
	// jQuery('.wpProQuiz_questionListItem').each(function(i, el) {
	// 	var input = jQuery(el).find('input');
	// 	var label = jQuery(el).find('label');
	// 	if ( input.is(':checkbox') || input.is(':radio' ) ) {
	// 		jQuery(this).prepend("<div class='wdm-cross'><i class='fa fa-times' aria-hidden='strikethrough' title='Strikethrough'></i></div>");
	// 	}
	// });
	
	/**
	 * Feature Name: Crossout Questions
	 * This will cross the choice. When user click on the cross icon then that choice will be crossed out.
	 * Trello Link: https://trello.com/c/Q4dVIyk1
	 */
	jQuery(document).on('click','.wdm-cross', function(){
		if ( jQuery(this).hasClass( "wdm-crossed" ) ) {
			//jQuery(this).parent('li').find('input').prop("disabled", false );
			jQuery(this).removeClass("wdm-crossed");
			jQuery(this).parent('li').removeClass("wdm-crossed-element");
			jQuery(this).html("<i class='fa fa-times' aria-hidden='strikethrough' title='Strikethrough'>");
		}else {
			//jQuery(this).parent('li').find('input').prop("disabled", true );
			jQuery(this).parent('li').addClass("wdm-crossed-element");
			jQuery(this).addClass("wdm-crossed");
			jQuery(this).html("<i class='fa fa-undo' aria-hidden='enable' title='Reset'></i></i>");
		}
	});

	// This will remove the cross icon and cross effect of selected checkbox choice.
	jQuery(document).on('change','.wpProQuiz_questionListItem input[type="checkbox"]', function(){
		if(this.checked) {
			var label = jQuery(this).closest('li').children('.wdm-cross');
			label.replaceWith( "<div class='wdm-empty'></div>" );
			jQuery(this).closest('li').removeClass("wdm-crossed-element");

		} else {
			var label = jQuery(this).closest('li').children('.wdm-empty');
			label.replaceWith("<div class='wdm-cross'><i class='fa fa-times' aria-hidden='strikethrough' title='Strikethrough'></i></div>");
		}
	});

	// This will remove the cross icon and cross effect of selected radio choice.
	jQuery(document).on('change','.wpProQuiz_questionListItem input[type="radio"]', function(){
		jQuery(this).closest('.wpProQuiz_questionList').find('input[type="radio"]').each(function(i, el){
			if(this.checked) {
				var label = jQuery(this).closest('li').children('.wdm-cross');
				label.replaceWith( "<div class='wdm-empty'></div>" );
				jQuery(this).closest('li').removeClass("wdm-crossed-element");
			} else {
				var label = jQuery(this).closest('li').children('.wdm-empty');
				label.replaceWith("<div class='wdm-cross'><i class='fa fa-times' aria-hidden='strikethrough' title='Strikethrough'></i></div>");
			}
		});
	});

	/**
	 * Feature Name: Hide Quiz summary
	 * This will add a collapsable to the course material tab
	 * Trello Link: https://trello.com/c/gU8kb0eR
	 */
	jQuery(document).on('click', '.learndash_quiz_materials .sub-heading',function(){
		if ( jQuery(this).hasClass( "wdm-collapsed" ) ) {
			jQuery(this).removeClass("wdm-collapsed");
			jQuery(this).siblings('.wdm-material-content').removeClass("wdm-collapsed-contnet");
			jQuery(this).siblings('.wdm-material-content').slideDown("slow");
		}else {
			jQuery(this).addClass("wdm-collapsed");
			jQuery(this).siblings('.wdm-material-content').slideUp("slow");
			jQuery(this).siblings('.wdm-material-content').addClass("wdm-collapsed-contnet");
		}
	} );

	/* Collapse Tab when user click on the start quiz button */
	jQuery(document).on('mouseup', '.wpProQuiz_text .wdm-quiz-start-btn',function(){
		var parent = jQuery(this).parents('.wdm_quiz_content');
		if ( ! parent.find('.learndash_quiz_materials .sub-heading').hasClass( "wdm-collapsed" ) ) {
			parent.find('.learndash_quiz_materials .sub-heading').addClass("wdm-collapsed");
			parent.find('.wdm-material-content').slideUp("slow");;
			parent.find('.wdm-material-content').addClass("wdm-collapsed-contnet");
		}
	} );

	/* Reading Comp User Result*/
	jQuery('.learndash-wrapper').on( 'click' , '.wpProQuiz_button_reShowQuestion', function(){
		jQuery( ".wpProQuiz_quiz.view-mode li" ).each(function( index ) {
		  if ( jQuery(this).hasClass('rc_set_start')) {
		  		jQuery(this).find('.rc-content').removeClass('col-lg-6').addClass('col-lg-12');
				jQuery(this).find('.rc-content').removeClass('col-md-6').addClass('col-md-12');
				jQuery(this).find('.rc-question').removeClass('col-lg-6').addClass('col-lg-12');
				jQuery(this).find('.rc-question').removeClass('col-md-6').addClass('col-md-12');
		  } else {
		  		jQuery(this).find('.rc-content').remove();
		  		jQuery(this).find('.rc-question').removeClass('col-lg-6').addClass('col-lg-12');
				jQuery(this).find('.rc-question').removeClass('col-md-6').addClass('col-md-12');
		  }
		});
	});

	/* LearnDash Reporting */
	jQuery(document).on('click', '.wdm_quiz_attempt_title_section .wdm_quiz_attempt_icon', function () {
		var parent = jQuery(this).parents(".wdm_quiz_attempt_section");
		if ( !parent.find(".wdm_quiz_attempts").hasClass("wdm-active-section") ) {
			parent.find(".wdm_quiz_attempts").addClass("wdm-active-section");
			parent.find(".wdm_quiz_attempt_title_section .wdm_quiz_attempt_icon i").removeClass('fa-angle-down').addClass('fa-angle-up');
			parent.find(".wdm_quiz_attempts").show(500);
		}
		else {
			parent.find(".wdm_quiz_attempts").removeClass("wdm-active-section");
			parent.find(".wdm_quiz_attempt_title_section .wdm_quiz_attempt_icon i").removeClass('fa-angle-up').addClass('fa-angle-down');
			parent.find(".wdm_quiz_attempts").hide(500);

		}
	});

	jQuery(document).on('load', '.tts-collapse-wrap', function () {
		if ( ! jQuery('div.tts-collapse-wrap').first().hasClass('.tts-active')) {
	    	jQuery('div.tts-collapse-wrap').first().addClass('tts-active');
		}
	});

	jQuery(document).on("click", "a.statistic_set_content", function () {
		jQuery(this).parents("tr").next().next().toggle("fast");
	});

	jQuery(document).on('click', '.tts-collapse', function () {
		var parent = jQuery(this).parents('.tts-collapse-wrap');

		if (parent.hasClass('tts-active')) {
			parent.removeClass('tts-active');
			parent.find('.ps-icon').html('<i class="fa fa-angle-down"></i>');
		} else {
			parent.addClass('tts-active');
			parent.find('.ps-icon').html('<i class="fa fa-angle-up"></i>');
		}
	});
	jQuery(document).on('click', '.tts-collapse-inner', function () {
		var parent = jQuery(this).parents('.tts-collapse-wrap-inner');

		if (parent.hasClass('tts-active')) {
			parent.removeClass('tts-active');
			parent.find('.ps-icon').html('<i class="fa fa-angle-down"></i>');
		} else {
			parent.addClass('tts-active');
			parent.find('.ps-icon').html('<i class="fa fa-angle-up"></i>');
		}
	});

	jQuery(document).on('click','.custom-next-que',function(){
		var parent = jQuery(this).parents('li.wpProQuiz_listItem');
		var current_que = parent.data('q');
		var current_section = parent.data('section');
		var next_question = parent.next('li');
		var next_que = next_question.data('q'); 
		var next_que_section = next_question.data('section');
		if( jQuery('.wpProQuiz_reviewQuestion.wdm-global').find('[data-q="' + current_que + '"]').hasClass('wpProQuiz_reviewQuestionSolved') ){
			jQuery('.tts-custom-section-nav#'+current_section+' li.tts-custom-que-nav').each(function(){
				if( current_que == jQuery(this).data('q') ) {
					jQuery(this).addClass('wpProQuiz_reviewQuestionSolved');
				}
			});
		}
		jQuery('.tts-custom-section-nav li.tts-custom-que-nav').each(function(){
			if (jQuery(this).hasClass( "custom_wpProQuiz_reviewQuestionTarget" ) ) {
			  jQuery(this).removeClass("custom_wpProQuiz_reviewQuestionTarget");
			}
		});
		jQuery('.tts-custom-section-nav#'+next_que_section+' li.tts-custom-que-nav').each(function(){
			var q = jQuery(this).data('q');
			if (q == next_que ){
				jQuery(this).addClass('custom_wpProQuiz_reviewQuestionTarget');
			}
		});
	});
	jQuery(document).on('click','.tts-mrk-review',function(){
		var parent = jQuery(this).parents('li.wpProQuiz_listItem');
		var current_que = parent.data('q');
		var current_section = parent.data('section');
		var next_question = parent.next('li');
		var next_que = next_question.data('q'); 
		var next_que_section = next_question.data('section');
		if( jQuery('.wpProQuiz_reviewQuestion.wdm-global').find('[data-q="' + current_que + '"]').hasClass('wpProQuiz_reviewQuestionReview') ){
			jQuery('.tts-custom-section-nav#'+current_section+' li.tts-custom-que-nav').each(function(){
				if( current_que == jQuery(this).data('q') ) {
					jQuery(this).addClass('wpProQuiz_reviewQuestionReview');
				}
			});
		}
	});
	jQuery('.custom-submit-section').on('click', function(){
		var parent = jQuery(this).parents('li.wpProQuiz_listItem');
		var current_que = parent.data('q');
		var current_section = parent.data('section');
		if( jQuery('.wpProQuiz_reviewQuestion.wdm-global').find('[data-q="' + current_que + '"]').hasClass('wpProQuiz_reviewQuestionSolved') ){
			jQuery('.tts-custom-section-nav#'+current_section+' li.tts-custom-que-nav').each(function(){
				if( current_que == jQuery(this).data('q') ) {
					jQuery(this).addClass('wpProQuiz_reviewQuestionSolved');
				}
			});
		}
		var super_parent = jQuery(this).parents('.wpProQuiz_content');
		var quiz_meta    = super_parent.data('quiz-meta');
		var next_section = parent.next('li');
		var n_section = next_section.data('section');
		var n_section_time = next_section.data('section-time');
		var interval     = jQuery.cookie('tts-current-counter-' + tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id );
		window.clearInterval( interval );
		jQuery.cookie(current_section+'-'+tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, 0 );
		jQuery.cookie('tts-current-section-' + tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, n_section );
		jQuery.cookie(n_section+'-'+tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, n_section_time );
		
		jQuery('.tts-custom-section-nav#'+current_section).hide();
		if( ! next_section.hasClass('wdm-break') ) {
			jQuery('.tts-custom-section-nav#'+n_section).show();
		}

		jQuery('.tts-view-list .'+n_section ).addClass('tts-ps-active-sec');
		jQuery('.tts-view-list .'+current_section ).removeClass('tts-ps-active-sec');
		jQuery('.tts-view-list .'+current_section ).addClass('tts-disabled-section');
		
		start_timer( quiz_meta , n_section , n_section_time );

		var next_section_data =  get_next_section_time(current_section);
		if( undefined == next_section_data['next_section'] || '' == next_section_data['next_section'] ) {
			jQuery('.tts-custom-timer').hide();
		}

	});
	jQuery(document).on('click','.wpProQuiz_questionListItem label', function(){
		var super_parent = jQuery(this).parents('.wpProQuiz_listItem');
		var q = super_parent.data('q');
		var section = super_parent.data('section');
		jQuery('.tts-custom-section-nav#'+section+' li.tts-custom-que-nav').each(function(){
			if( q == jQuery(this).data('q') && ! jQuery(this).hasClass('wpProQuiz_reviewQuestionSolved') ) {
				jQuery(this).addClass('wpProQuiz_reviewQuestionSolved');
				jQuery(this).removeClass('wpProQuiz_reviewQuestionReview');
			}
		});
	});
	jQuery(document).on('click', '.tts-mrk-review', function(){
	    var parent = jQuery(this).parents('li.wpProQuiz_listItem');
	    var section = parent.data('section');
	    var question = parent.data('q');
	    if ( undefined != section && undefined != question ){
	        if( jQuery('div.tts-custom-section-nav#'+section).find(`[data-q='${question}']`).hasClass('custom_wpProQuiz_reviewQuestionReview')){
        		jQuery('div.tts-custom-section-nav#'+section).find(`[data-q='${question}']`).removeClass('wpProQuiz_reviewQuestionReview');
	            jQuery('div.tts-custom-section-nav#'+section).find(`[data-q='${question}']`).removeClass('custom_wpProQuiz_reviewQuestionReview');
	            jQuery(this).val('Mark Question for Review');
	        }else {
        		jQuery('div.tts-custom-section-nav#'+section).find(`[data-q='${question}']`).removeClass('wpProQuiz_reviewQuestionReview');
	            jQuery('div.tts-custom-section-nav#'+section).find(`[data-q='${question}']`).addClass('custom_wpProQuiz_reviewQuestionReview');
	            jQuery(this).val('Unmark for Review');
	        }
	    } else {
	        var que_by_index = jQuery(this).closest('li').index()+1;
	    	var que = jQuery('.wpProQuiz_reviewQuestion ol').find('li').eq(que_by_index-1);
	    	if( que.hasClass('custom_wpProQuiz_reviewQuestionReview')) {
	    		que.removeClass('custom_wpProQuiz_reviewQuestionReview')
	            jQuery(this).val('Mark Question for Review');
	    	}else {
	    		que.addClass('custom_wpProQuiz_reviewQuestionReview')
	            jQuery(this).val('Unmark for Review');
	    	}
	    }
	});
	// jQuery('.tts-custom-section-nav ol li.tts-custom-que-nav').unbind();

	// jQuery(document).on('change','.wdm-sng-var', function(){
	// 	if( jQuery(this).is(":checked") ){ // check if the radio is checked
 //            var parent = jQuery(this).parents('.wdm-sgl-variation');
 //        	var val = jQuery(this).val(); // retrieve the value
 //        	parent.find('.wdm-var-option-ps').each(function(i,e){
 //            	var att = jQuery(e).data("var-key");
 //            	var att_val = jQuery(e).data("var-value");
 //            	jQuery('#'+att+ ' option[value="'+ att_val +'"]').prop("selected", true);
	// 			jQuery('input.variation_id').val(val);
	// 			jQuery('.single_add_to_cart_button').removeClass('disabled');
	// 			jQuery('.single_add_to_cart_button').removeClass('.wc-variation-selection-needed');
 //            });
 //            // data-var
 //        }
	// });
});

function switchCSS(windowsize) {
	//console.log( jQuery(".wpProQuiz_reviewDiv").innerHeight() );
	if ( windowsize > 1198 ) {
		if (  ! jQuery('.ld-focus-sidebar').hasClass('focused') ) {
			jQuery('.ld-focus-main .modern-icon-Expand').click();
		}
  	} else {
  		if ( jQuery('.ld-focus-sidebar').hasClass('focused') ) {
  			jQuery('.ld-focus-main .modern-icon-Expand').click();
  		}
  	}
}

function loadMathJaxConfig() {
	MathJax.Hub.Config({
	  "HTML-CSS": {
	    imageFont : null,
        scale: 92,
        matchFontHeight: true,
	  }
	});
}

function parseTime(sec){
	var seconds = parseInt(sec % 60);
	var minutes = parseInt((sec / 60) % 60);
	var hours = parseInt((sec / 3600) % 24);

	seconds = (seconds > 9 ? '' : '0') + seconds;
	minutes = (minutes > 9 ? '' : '0') + minutes;
	hours = (hours > 9 ? '' : '0') + hours;

	return hours + ':' +  minutes + ':' + seconds;
}

function start_timer( quiz_meta ,section, time ) {
	
	var next_section_data =  get_next_section_time(section);
	
	var _counter     = time;
	var _intervalId  = 0;
	var instance     = {};
	var timer_cookie = section+'-'+tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id;
	jQuery.cookie( timer_cookie, time );
	if(!_counter)
		return;
	jQuery.cookie.raw = true;
	var full = _counter * 1000;
	var tick = jQuery.cookie(timer_cookie);
	var limit = tick ? tick : _counter;
	var x = limit * 1000;

	var timelimit_text =  jQuery('.tts-custom-timer');
	var $timeText = timelimit_text.find('span').text(parseTime(limit));


	var beforeTime = +new Date();

	_intervalId = window.setInterval(function() {
	
	jQuery.cookie('tts-current-counter-' + tts_ld_data.user_id + '-' + quiz_meta.quiz_pro_id, _intervalId );

		var diff = (+new Date() - beforeTime);
		var remainingTime = x - diff;

		if(diff >= 500) {
			tick = remainingTime / 1000;
			$timeText.text(parseTime(Math.ceil(tick)));
			jQuery.cookie(timer_cookie, tick);
		}

		// $timeDiv.css('width', (remainingTime / full * 100) + '%');

		if(remainingTime <= 0) {
			// execute_next_section( var);
			// $.removeCookie(timer_cookie);
			window.clearInterval(_intervalId);
			jQuery.cookie( timer_cookie, 0 );
			jQuery('.tts-custom-section-nav#'+section).hide();
			jQuery('.tts-custom-section-nav#'+next_section_data['next_section']).show();
			console.log('----------------');
			console.log(next_section_data['next_section']);
			if( undefined == next_section_data['next_section'] || '' == next_section_data['next_section'] ) {
				console.log('You are undefined');
				console.log(jQuery( "li.wpProQuiz_listItem").last());
				jQuery('.tts-custom-timer').hide();
				var last_ele = jQuery( "li.wpProQuiz_listItem").last();
				console.log(last_ele.find('.custom-submit-section'));
				last_ele.find('.custom-submit-section').click();
				// jQuery( "li.wpProQuiz_listItem").last().find('.custom-submit-section').click();
			} else {
				start_timer( quiz_meta , next_section_data['next_section'], next_section_data['next_section_time'] );
				jQuery('.tts-view-list .'+next_section_data['next_section'] ).addClass('tts-ps-active-sec');
				jQuery('.tts-view-list .'+section ).removeClass('tts-ps-active-sec');
				jQuery('.tts-view-list .'+section ).addClass('tts-disabled-section');
				jQuery('.tts-custom-section-nav#'+next_section_data['next_section']+' .tts-custom-que-nav').first().click();
			}
		}

	}, 16);
}

function get_next_section_time( section ) {
	all_sections = Object.keys(all_sections_time); 
	current_i = all_sections.indexOf(section) 
	next_section = all_sections[current_i+1];
	// console.log(next_section);
	var list = new Array();
	var return_data = new Array();
	if( '' != next_section ) {
		// console.log('not empty');
		return_data['next_section'] = next_section;
		return_data['next_section_time'] = all_sections_time[next_section];
	}
	// console.log('Return Data: '+ return_data);
	return return_data;
}
