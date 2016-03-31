<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 03/02/16
 * Time: 15:22
 * Template Name: Offers
 * Author David W. Brown, Obson.net
 * Version 0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * On failure, redirect to Home page and exit.
 */
function fail( $href = false ) {
	if ( ! $href ) {
		$href = site_url();
	}
	echo "href = $href\n";
	?>
	<script>
		location.href = "<?php echo $href; ?>";
	</script>
	<?php
	exit;       // in case no javascript available
}

/**
 * Check whether the offer is available and take appropriate action.
 *
 * @param string $mode
 */
function check_availability( $mode = 'inactive' ) {
	global $not_avail_msg;

	$not_avail_msg = '';

	switch ( $mode ) {
		case 'active':
			return;
			break;

		case 'inactive':
			fail();
			break;

		case 'test':
			// Set 'not available'
			$not_avail_msg = "<h1>Service not yet available</h1>\n";
			$not_avail_msg .= "<p>This service is currently under development " .
			                  "and is not available for use!</p>\n";
			break;
	}
}

function fix_permalink( $permalink ) {
	return empty( $permalink ) || $permalink[ strlen( $permalink ) - 1 ] == '/'
		? $permalink . '?x=0'
		: $permalink;
}

/**
 * Check that the name is safe and of required length
 *
 * @param $name
 * @param int $min_length
 *
 * @return bool: true if OK, false if not
 */
function validate_name( $name, $min_length = 3 ) {
	return ( ( esc_attr( $name ) == $name ) && ( strlen( $name ) >= $min_length ) )
		? true
		: false;
}

/**
 * This function uses zero-indexing -- i.e. the first element is element 0.
 * Note that at present if two questions have the same sequence number only
 * one will be used, but which one it will be is indeterminate (since it just
 * depends on how the DB engine processes the SQL), not random. See note
 * below.
 *
 * @param $offer_id
 * @param $n
 *
 * @return the id of the (n - 1)th question if available, or false otherwise.
 *
 * TODO (next version?): Will be changed to return an array of ids in case
 * there are several (A/B testing). If there is no (n - 1)th question the
 * array will be empty.
 */
function get_question( $offer_id, $n ) {
	// TODO: We use the WP get_posts function to retrieve all the posts
	// of the required type (i.e. questions), and this can consume quite a
	// lot of memory. It would be better if we recovered them in batches (as
	// intended by WP) with a non-zero offset argument, but for this version
	// we just assume (a) that there will never be more than 100 questions
	// altogether and (b) there is sufficient memory to hold all the
	// questions at the same time. This needs fixing some time...
	$args      = array(
		'post_type'   => 'questions',
		'numberposts' => 100,
		'offset'      => 0,
	);
	$questions = get_posts( $args );

	// Get all the relevant questions into an array indexed by each
	// question's sequence number
	$q = array();
	foreach ( $questions as $question ) {
		// Get the custom meta variables for this question
		$custom = get_post_custom( $question->ID );

		// Ignore if it's not active or it's for a different offer
		if ( $custom['obson_question_state'][0] != 'active' ||
		     $custom['obson_questions_offer'][0] != $offer_id
		) {
			continue;
		}

		// TODO Change this to $q[$custom['obson_seq'][0]][] so we get an
		// array of ids in each element of the array $q. We can then use
		// this to select one at random if there is more than one option.
		// On second thoughts, there's a bit more to this as we'll need to
		// to be able to detect which one was used when we're analysing the
		// results. Consequently deferring to later version.

		$q[ $custom['obson_seq'][0] ] = $question->ID;
	}

	if ( count( $q ) > $n ) {
		ksort( $q );                      // get the array into sequential order
		$values = array_values( $q );     // re-index without seq numbers
		return $values[ $n ];             // the nth question id
	}

	return false;
}

// Extract url of bg image from the post content
$the_rest   = stristr( $post->post_content, 'src="' );
$bg_url     = ltrim( stristr( $the_rest, '" alt', true ), 'src="' );
$main_title = $post->post_title;

// Get arguments for Not Available message and for parameterised stylesheet
$meta_values = get_post_meta( $post->ID, '', true );

// Send a tracking email for testing
if ( $meta_values[ 'obson_offers_mode' ][ 0 ] == 'test' ) {
	$timestamp = date( 'H:i:s', gmdate( 'U' ) );
	$msg = __FILE__ . ' has been accessed via http://obson.net at ' . $timestamp . ' GMT';
	wp_mail( 'david@cowlstreetpress.co.uk', 'Obson Talkback Access', $msg );
}

// Set 'Not Available' message if appropriate
check_availability( $meta_values['obson_offers_mode'][0] );

echo "<!DOCTYPE html>\n";
echo '<html ';
language_attributes();
echo ">\n";
echo "<head>\n";
echo '<meta charset=';
bloginfo( 'charset' );
echo ">\n";
echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";

// Standard styling.
require( 'css/style.php' );   // This requires $meta_values to have been set

wp_head();                  // poss. omit this?

echo "</head>\n";
echo '<body class="custom-background-image" id="landing-page">' . "\n";

echo $not_avail_msg;                // empty unless in test mode

// $show must default to string, otherwise ctype_digit() will fail.
$show = get_query_var( 'show', '0' );
if ( ! ctype_digit( $show ) ) {
	$show = 0;
}

echo "<div id='outerbox'>\n";

$signup_error = get_query_var( 'obs' );

if ( $show == 0 ) {

	// Intro dialog...

	// Display the initial call to action
	$caption     = get_post_meta( $post->ID, 'obson_offers_intro_text' )[0];
	$button_text = get_post_meta( $post->ID, 'obson_offers_button_text' )[0];
	echo "<div id='innerbox-1'>\n";
	echo "<p class='of-text-1'>$caption</p>\n";
	echo "<a href='" . fix_permalink( get_permalink() ) .
	     "&amp;show=1'><input type='button' name='of_submit' id='of_submit' " .
	     "value='$button_text' style='text-transform: none;'></a>\n";
	echo "</div>\n";

	$_SESSION['answers'] = null;    // Clear the session

} else {

	// All other cases...

	$qid = get_question( $post->ID, $show - 1 );
	if ( $qid === false ) {
		fail();                     // (safe exit to Home page)
	}
	$custom = get_post_custom( $qid );

	// Reset answer for each question
	$answer = array( 'qid' => $qid, 'seq' => $show );

	// Arrays for convenience
	$mc_types   = array( 'choose_one', 'choose_some', 'rank' );
	$edit_types = array( 'simple', 'extended' );

	//
	// Extract posted data
	//
	if ( isset( $_POST['signup'] ) && $_POST['signup'] == $show ) {
		$first_name = isset( $_POST['resp_first_name'] )
			? esc_attr( $_POST['resp_first_name'] )
			: '';
		$last_name  = isset( $_POST['resp_last_name'] )
			? esc_attr( $_POST['resp_last_name'] )
			: '';
		$email      = isset( $_POST['resp_email'] )
			? esc_attr( $_POST['resp_email'] )
			: '';

		// Validate sign-up data
		if ( validate_name( $first_name, 1 ) &&
		     validate_name( $last_name, 2 ) &&
		     is_email( $email )
		) {
			// Check for dodgy email addresses. Skip this when testing on
			// localhost, which may not be able to access the email checking
			// services.

			// TODO The is_email_unsafe() function is in wp-includes/ms-functions.php
			// which appears not to be included by default. Probably best to
			// include it explicitly
			if ( $_SERVER['SERVER_NAME'] != 'localhost' &&
			     function_exists( 'is_email_address_unsafe' ) &&
			     is_email_address_unsafe( $email )
			) {
				// Email address is unsafe - disallow
				fail();
			}

			// Store sign-up data
			$answer['resp_first_name'] = $first_name;
			$answer['resp_last_name']  = $last_name;
			$answer['resp_email']      = $email;
		} else {
			// Go back to signup page with error flag set
			$show -= 1;
			$permalink = get_permalink();
			$flag      = 'invalid';
			$args      = array(
				'show' => $show,
				'obs'  => $flag
			);
			$url       = add_query_arg( $args, $permalink );
			fail( $url );
		}
	} else if ( isset( $_POST['multi_choice'] ) &&
	            in_array( $_POST['multi_choice'], $mc_types )
	) {
		$mc_type                = $_POST['multi_choice'];
		$answer['multi_choice'] = $mc_type;

		switch ( $mc_type ) {
			case 'choose_one':
				// Valid answer if from 1 to 10. We set it to 0 to indicate
				// invalid or no answer
				$rb_answer = isset( $_POST['rb_answer'] )
					? $_POST['rb_answer'] : 0;
				if ( $rb_answer < 0 || $rb_answer > 10 ) {
					$rb_answer = 0;
				}
				$answer['rb_answer'] = esc_attr( $rb_answer );
				break;

			case 'choose_some':
				for ( $i = 1; $i <= 10; $i ++ ) {
					$key            = "cb_answer_$i";
					$answer[ $key ] = esc_attr( $_POST[ $key ] );
				}
				break;

			case 'rank':
				for ( $i = 1; $i <= 10; $i ++ ) {
					$key            = "rank_$i";
					$answer[ $key ] = esc_attr( $_POST[ $key ] );
				}
				break;
		}
	} else if ( isset( $_POST['own_words'] ) &&
	            in_array( $_POST['own_words'], $edit_types )
	) {
		$text_response           = isset( $_POST['text_response'] )
			? esc_attr( $_POST['text_response'] )
			: '';
		$answer['text_response'] = $text_response;
	} else if ( isset( $_POST['thank_you'] ) ) {
		// Nothing required here. After the respondent leaves the 'thank_you'
		// question, control is transferred directly to an exit page. I've
		// just included the check for completeness.
		fail();
	}

	// Store the answer(s) to the previous question in the session
	$_SESSION['answers'][ $show ] = $answer;

	$caption = $custom['obson_questions_intro_text'][0];

	echo "<div id='innerbox-2'>\n";

	$qpost = get_post( $qid );
	$title = $qpost->post_title;

	echo "<h3 class='of-header'>$title</h3>\n";

	// We split caption text into separate paragraphs wherever the '***'
	// sequence is found.
	$formatted_caption = str_replace(
		"***",
		"</p>\n<p class='of-text-2'>",
		$caption
	);

	echo "<p class='of-text-2'>$formatted_caption</p>\n";

	$qtype       = $custom['obson_question_accordion'][0];
	$button_text = $custom['obson_question_button_text'][0];

	//
	// Display the question and the form for submitting the answer(s)
	//
	switch ( $qtype ) {
		case 'sign_up':

			echo "<form method='post' action='" . get_permalink() . "'>\n";

			if ( $signup_error == 'invalid' ) {
				echo "<p class='of-error'>Please enter your first and " .
				     "last names<br>and a valid email adress!</p>\n";
			}

			echo "<input type='hidden' name='show' value='" .
			     ( $show + 1 ) . "'>" .
			     "<input type='hidden' name='signup' value='" .
			     ( $show + 1 ) . "'>" .
			     "<input type='hidden' name='qid' value='$qid'>";
			echo "<input type='text' class='of-input' name='resp_first_name'
                    placeholder='{$custom['obson_question_first_name_caption'][0]}'
                     tabindex='1'
                    ><br>\n";
			echo "<input type='text' class='of-input' name='resp_last_name'
                    placeholder='{$custom['obson_question_last_name_caption'][0]}'
                     tabindex='2'
                    ><br>\n";
			echo "<input type='email' class='of-input' name='resp_email'
                    placeholder='{$custom['obson_question_email_caption'][0]}'
                     tabindex='3'
                    ><br>\n";
			echo "<div style='text-align:center;'>";
			echo "<input id='of_submit' type='submit' name='btn_submit'
                    value='$button_text'  tabindex='4'></div>\n";
			echo "</form>\n";

			break;

		case 'multiple_choice':

			$multi_choice_type = $custom['obson_mc_type'][0];

			echo "<form method='post' action='" . get_permalink() . "'>\n";
			echo "<input type='hidden' name='show' value='" . ( $show + 1 ) .
			     "'><input type='hidden' name='multi_choice' " .
			     "value='$multi_choice_type'>\n";

			for ( $i = 1; $i <= 10; $i ++ ) {
				if ( isset( $custom[ 'obson_multiple_choice_text_' . $i ][0] ) ) {
					$text = $custom[ 'obson_multiple_choice_text_' . $i ][0];
					if ( strlen( $text ) > 0 ) {
						switch ( $multi_choice_type ) {
							case 'choose_one':
								echo "<input type='radio' name='rb_answer'
                                    value='$i'> ";
								break;
							case 'choose_some':
								echo "<input type='checkbox'
                                    name='cb_answer_$i' value='on'> ";
								break;
							case 'rank':
								echo "<input type='number'
                                    class='of-input short' name='rank_$i'
                                    min='1' max='10' step='1'
                                    value='$i'> &nbsp; ";
								break;
							default:
								fail();
						}
						echo "$text<br>\n";
					}
				}
			}

			echo "<div style='text-align:center;'><input id='of_submit'
                type='submit' name='btn_submit' value='$button_text'></div>\n";
			echo "</form>\n";

			break;

		case 'own_words':

			$edit_type = $custom['obson_question_edit_type'][0];

			echo "<form method='post' action='" . get_permalink() . "'>\n";
			echo "<input type='hidden' name='show' value='" . ( $show + 1 ) . "'>";
			echo "<input type='hidden' name='own_words' value='$edit_type'>";

			switch ( $edit_type ) {
				case 'simple':
					echo "<input class='of-input' type='text'
                        name='text_response' id='text_response' tabindex='1'>\n";
					break;
				case 'extended':
					echo "<textarea name='text_response' class='of-input'
                        id='text_response' rows='6' style='width:100%' tabindex='1'></textarea>\n";
					break;
				default:
					fail();
			}

			echo "<div style='text-align:center;'><input id='of_submit'
                type='submit' name='btn_submit' value='$button_text' tabindex='2'></div>\n";
			echo "</form>\n";

			break;

		case 'thank_you':

			// The thank_you 'question' doesn't require (or accept) an answer.
			// It simply displays the 'thank you' message with a link to
			// download the lead-magnet and a link to an exit page -- which
			// could well contain information about the lead-magnet and/or
			// an 'upsell'. It also sends an email to the 'webmaster'
			// detailing the questions and the answers given.

			$subject = "{$post->post_title} Survey completed";
			$to      = $custom['obson_question_webmaster_address'][0];
			$msg     = "<html>\n";
			$msg .= "<h1>Survey Response</h1>\n";

			$answers = $_SESSION['answers'];

			for ( $i = 2; $i <= count( $answers ); $i ++ ) {
				// Each answer always relates to the previous question, and
				// we find the previous question from the custom fields for
				// the post with the id given by the qid held in the
				// previous answer. Yes, I know it's confusing. Fortunately
				// we have all the answers in memory and all the questions
				// are posts, so it can be done fairly simply...

				$a   = $answers[ $i ];            // answer to the previous question
				$qid = $answers[ $i - 1 ]['qid']; // the id of the previous question
				$q   = get_post_custom( $qid );   // the previous question

				$qp       = get_post( $qid );     // post from previous question
				$qp_title = $qp->post_title;      // all we need from the post proper

				$msg .= "<h2>$qp_title</h2>\n";
				$msg .= "<p><em>" .
				        str_replace( "***",
					        "</em></p>\n<p><em>",
					        $q['obson_questions_intro_text'][0]
				        ) . "</em></p>\n";

				$qtype = $q['obson_question_accordion'][0];


				switch ( $qtype ) {
					case 'sign_up':

						// Store respondent info for easy access later
						$resp_first_name = $a['resp_first_name'];
						$resp_last_name  = $a['resp_last_name'];
						$resp_email      = $a['resp_email'];

						$msg .= "<p>{$q['obson_question_first_name_caption'][0]}: $resp_first_name<br>\n";
						$msg .= "{$q['obson_question_last_name_caption'][0]}: $resp_last_name<br>\n";
						$msg .= "{$q['obson_question_email_caption'][0]}: $resp_email</p>\n";

						break;

					case 'multiple_choice':

						switch ( $a['multi_choice'] ) {
							case 'choose_one':
								$key = 'obson_multiple_choice_text_' .
								       ( $a['rb_answer'] );
								$msg .= "<p>" . $q[ $key ][0] . "</p>\n";
								break;

							case 'choose_some':
								// We need to inspect each checkbox from
								// cb_answer_1 to cb_answer_10, and for each
								// one that is checked retrieve the answer as
								// the corresponding string from the question.
								$msg .= "<ul>\n";
								for ( $i = 1; $i <= 10; $i ++ ) {
									$key = 'cb_answer_' . $i;
									if ( $a[ $key ] == 'on' ) {
										$key2 = 'obson_multiple_choice_text_' . $i;
										$msg .= "<li>" . $q[ $key2 ][0] . "</li>\n";
									}
								}
								$msg .= "</ul>";
								break;

							case 'rank':
								// The process here is similar to that for
								// 'choose_some', but we record the rank, not
								// just whether the option was selected.
								$msg .= "<p>Rankings:</p>\n<p>";
								for ( $i = 1; $i <= 10; $i ++ ) {
									$key  = 'rank_' . $i;
									$rank = $a[ $key ];
									if ( $rank ) {
										$key2 = 'obson_multiple_choice_text_' . $i;
										$msg .= $rank . ': ' . $q[ $key2 ][0] . "<br>\n";
									}
								}
								$msg .= "</p>";
								break;
						}

						break;

					case 'own_words':

						$msg .= "<p>{$a['text_response']}</p>\n";

						break;
				}

			}
			$msg .= "</html>\n";

			$terminal_qid         = $answers[ $show ]['qid'];
			$terminal_question    = get_post_custom( $terminal_qid );
			$exit_to              = $terminal_question['obson_question_exit_to'][0];
			$exit_url             = get_post_permalink( $exit_to );
			$lead_magnet_id       = $terminal_question['obson_lead_magnet'][0];
			$lead_magnet_url      = wp_get_attachment_url( $lead_magnet_id );
			$lead_magnet_file     = get_attached_file( $lead_magnet_id );
			$allow_download       = $terminal_question['obson_allow_download'][0];
			$link_text            = $terminal_question['obson_lead_magnet_link_text'][0];
			$email_subject        = $terminal_question['post_title'][0];
			$email_main_text      = str_replace(
				"***",
				"</p>\n<p>",
				$terminal_question['obson_question_respondent_email_text'][0]
			);
			$sal_style            = $terminal_question['obson_question_salutation'][0];
			$name_style           = $terminal_question['obson_question_name_style'][0];
			$email_link_text_site = $terminal_question['obson_site_email_link_text'][0];
			$email_signature      = $terminal_question['obson_email_signature'][0];;

			if ( $allow_download == 'yes' ) {
				echo "<a class='of-text-link' href='$lead_magnet_url' download>$link_text</a><br>";
			}
			echo "<a href='$exit_url' target='_top'><input type='button' " .
			     "name='of_submit' id='of_submit' " .
			     "value='Finish' style='text-transform: none;'></a>\n";

			//echo "<pre>" . print_r($terminal_question, 1) . "</pre><br>";

			// This may not work if the server is configured incorrectly
			$headers = "From: obson@" . $_SERVER['SERVER_NAME'] . "\n\n";

			$attachment = array( $lead_magnet_file );

			// Email diagnostics: for testing purposes, you can uncomment the
			// following lines to see the report that will be emailed to the
			// webmaster
			// ---
			//echo "<div style='text-align:left; border: 1px dotted white; padding: 10px;'>\n";
			//echo "Info sent to webmaster ($to):<br>\n";
			//echo "subject = $subject<br>\n";
			//echo "msg = " . nl2br($msg, false) . "<br>\n";
			//echo "headers = $headers<br>\n";
			//echo "</div>\n";
			// ---

			// Send email to webmaster unless on localhost (testing) --
			// assumes wp_mail is able to send mail from the server
			if ( $_SERVER['SERVER_NAME'] != 'localhost' ) {
				wp_mail( $to, $subject, $msg, $headers );
			}

			//
			// Collect info to email to respondent
			//

			$to      = $resp_email;          // full RFC version didn't work
			$subject = $main_title;

			switch ( $sal_style ) {
				case 'hi':
					$greeting = 'Hi ';
					break;
				case 'dear':
					$greeting = 'Dear ';
					break;

				default:
					$greeting = '';
					break;
			}

			switch ( $name_style ) {
				case 'first':
					$greeting .= $resp_first_name;
					break;

				case 'both':
					$greeting .= $resp_first_name . ' ' . $resp_last_name;
					break;

				case 'french':
					$greeting .= strtoupper( $resp_last_name ) . ', ' . $resp_first_name;
					break;

				case 'formal':
					$greeting .= 'Sir or Madam';
					break;
			}

			$email_body = "<html>\n";
			$email_body .= "<p>$greeting</p>\n";
			$email_body .= "<p>$email_main_text</p>\n";
			$email_body .= "<p><a href='$exit_url'>$email_link_text_site</a></p>\n";
			$email_body .= "<p>$email_signature</p>";
			$email_body .= "</html>";

			// Uncomment this to display the email that will be sent to the
			// respondent (testing only)
			//echo "<div style='width:96%; padding: 10px; border: 1px solid white;'>";
			//echo "wp_mail($to, $subject, $email_body, $headers, $attachment)";
			//echo "attachment = <pre>" . print_r($attachment, 1) . "</pre></br>\n";
			//echo "</div>\n";

			// Send email to respondent unless on localhost (testing) --
			// assumes wp_mail is able to send mail from the server
			if ( $_SERVER['SERVER_NAME'] != 'localhost' ) {
				wp_mail( $to, $subject, $email_body, $headers, $attachment );
			}

			break;

		default:
			fail();
	}

	echo "</div>\n";
}

echo "</div>  <!-- id=outerbox -->\n";
