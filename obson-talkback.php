<?php
/**
 * Plugin Name: Obson Talkback
 * Plugin URI:  http://obson.net/the-obson-talkback-plugin/
 * Description: Quick and easy landing-page and survey plugin. English-language only.
 * Version:     0.1
 * Author:      David Brown
 * Author URI:  http://obson.net
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Determine urls for use by template
$css_file_url = plugins_url( '/css/style.css.php', __FILE__ );

// ---------------------------
// Activation and deactivation
// ---------------------------

function obson_talkback_install() {
    // Register custom post types
    obson_talkback_setup_post_types();

    // Clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'obson_talkback_install' );

function obson_talkback_deactivation() {
    // Custom post types will be automatically removed, so no need to
    // unregister them, but we must clear the permalinks to remove custom
    // post type's rules
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'obson_talkback_deactivation' );

// --------------
// Initialisation
// --------------

function obson_talkback_setup_post_types() {
    register_post_type
    (
        'offers',
        array
        (
            'public'       => true,
            'has_archive'  => true,
            'labels'       => array
            (
                'name'          => 'Offers',
                'singular_name' => 'Offer',
                'add_new'       => 'Add New Offer',
                'add_new_item'  => 'Add New Offer',
                'edit_item'     => 'Edit Offer',
                'new_item'      => 'New Offer',
                'all_items'     => 'All Offers',
                'view_item'     => 'View Offer',
                'menu_name'     => 'Offers',
                'not_found'     => 'No Offers have been defined',
            ),
            'rewrite'      => array
            (
                'slug' => 'offer'
            ),
            'hierarchical' => true,
            'supports'     => array
            (
                'title',
                'editor',
            ),
            'menu_icon'    => 'dashicons-carrot',
        )
    );

    register_post_type
    (
        'questions',
        array
        (
            'public'       => true,
            'has_archive'  => true,
            'labels'       => array
            (
                'name'          => 'Questions',
                'singular_name' => 'Question',
                'add_new'       => 'Add New Question',
                'add_new_item'  => 'Add New Question',
                'edit_item'     => 'Edit Question',
                'new_item'      => 'New Question',
                'all_items'     => 'All Questions',
                'view_item'     => 'View Question',
                'menu_name'     => 'Questions',
                'not_found'     => 'No Questions have been defined',
            ),
            'rewrite'      => array
            (
                'slug' => 'question'
            ),
            'hierarchical' => true,
            'supports'     => array
            (
                'title',
            ),
            'menu_icon'    => 'dashicons-editor-help',
        )
    );
}

add_action( 'init', 'obson_talkback_setup_post_types' );

/**
 * Hide the View Post button (etc.) for Questions
 */
function obson_admin_css() {
    global $post_type;
    if ( $post_type == 'questions' ) {
        echo '
<style type="text/css">

#edit-slug-box, #view-post-btn, #post-preview,.updated p a {
    display: none;
}

.view {
    display:none;
}

</style>';

    }
}

add_action( 'admin_head', 'obson_admin_css' );


// ----------
// META BOXES
// ----------
// For more (possibly out-of-date) information, see:
// http://code.tutsplus.com/tutorials/how-to-create-custom-wordpress-writemeta-boxes--wp-20336,

function obson_add_meta_boxes() {
    // Offers
    add_meta_box(
        'obson-offers-meta-box-id',         // Arbitrary id
        'Offer Settings',                   // Menu caption
        'obson_meta_box_offers',            // Function defining box content
        'offers',                           // Associated post type
        'normal',                           // Main admin column
        'high'                              // Near the top
    );

    // Questions
    add_meta_box(
        'obson-questions-meta-box-id',      // Arbitrary id
        'Q&A Settings',                     // Menu caption
        'obson_meta_box_questions',         // Function defining box content
        'questions',                        // Associated post type
        'normal',                           // Main admin column
        'high'                              // Near the top
    );
}

add_action( 'add_meta_boxes', 'obson_add_meta_boxes' );

function obson_meta_box_offers() {
    global $post;                           // The current WP post

    // To show the post id in the meta box uncomment the next line
    // echo "post id: " . $post->ID . "<br>";

    $values = get_post_custom( $post->ID );   // Recover current values

    // The actual value we want is always in the zeroth element of the array.
    // I'm not sure what the other elements (which are usually empty) are for.

    // Caption
    $caption = isset( $values['obson_offers_intro_text'][0] )
        ? $values['obson_offers_intro_text'][0]
        : 'An easier way to sell stuff online...';

    // Popup colours
    $popup_fg_colour = isset( $values['obson_popup_fg_colour'][0] )
        ? $values['obson_popup_fg_colour'][0]
        : '#444444';

    $popup_bg_colour = isset( $values['obson_popup_bg_colour'][0] )
        ? $values['obson_popup_bg_colour'][0]
        : '#FFAA00';

    // Button text
    $button_text = isset( $values['obson_offers_button_text'][0] )
        ? $values['obson_offers_button_text'][0]
        : 'Tell me about it!';

    // Button text colours
    $btn_fg_colour_normal = isset( $values['obson_btn_fg_colour'][0] )
        ? $values['obson_btn_fg_colour'][0]
        : '#FFFFFF';

    $btn_fg_colour_hover = isset( $values['obson_btn_fg_colour_hover'][0] )
        ? $values['obson_btn_fg_colour_hover'][0]
        : '#FFFFFF';

    $btn_fg_colour_click = isset( $values['obson_btn_fg_colour_click'][0] )
        ? $values['obson_btn_fg_colour_click'][0]
        : '#FFFFFF';

    // Button background colours
    $btn_bg_colour_normal = isset( $values['obson_btn_bg_colour'][0] )
        ? $values['obson_btn_bg_colour'][0]
        : '#800000';

    $btn_bg_colour_hover = isset( $values['obson_btn_bg_colour_hover'][0] )
        ? $values['obson_btn_bg_colour_hover'][0]
        : '#FF0000';

    $btn_bg_colour_click = isset( $values['obson_btn_fg_colour_click'][0] )
        ? $values['obson_btn_bg_colour_click'][0]
        : '#800000';

    // Mode
    $selected = isset( $values['obson_offers_mode'][0] )
        ? esc_attr( $values['obson_offers_mode'][0] )
        : 'Test';

    // Nonce field for security
    wp_nonce_field(
        'obson_offers_meta_box_nonce',      // id to recover nonce value
        'meta_box_nonce'                    // name of nonce field
    );

    // Display the meta box form
    ?>
    <p>
        <label for="obson_offers_intro_text">Caption</label><br>
        <input type="text" name="obson_offers_intro_text"
               id="obson_offers_intro_text"
               value="<?php echo $caption; ?>"
               class="obson-full-width"/>
    </p>

    <p>
        Colour<br>
        <input type="color" name="obson_popup_fg_colour"
               id="obson_popup_fg_colour"
               value="<?php echo $popup_fg_colour; ?>" class="color-picker"/>
        <label for="obson_popup_fg_colour">Text</label><br>
        <input type="color" name="obson_popup_bg_colour"
               id="obson_popup_bg_colour"
               value="<?php echo $popup_bg_colour; ?>" class="color-picker"/>
        <label for="obson_popup_bg_colour">Background</label>
    </p>

    <p>
        <label for="obson_offers_button_text">Button Text</label><br>
        <input type="text" name="obson_offers_button_text"
               id="obson_offers_button_text"
               value="<?php echo $button_text; ?>"/>
    </p>

    <p>
        Button Text Colour<br>
        <input type="color" name="obson_btn_fg_colour"
               id="obson_btn_fg_colour"
               value="<?php echo $btn_fg_colour_normal; ?>"
               class="color-picker"/>
        <label for="obson_btn_fg_colour">Normal</label><br>
        <input type="color" name="obson_btn_fg_colour_hover"
               id="obson_btn_fg_colour_hover"
               value="<?php echo $btn_fg_colour_hover; ?>"
               class="color-picker"/>
        <label for="obson_btn_fg_colour_hover">Hover</label><br>
        <input type="color" name="obson_btn_fg_colour_click"
               id="obson_btn_fg_colour_click"
               value="<?php echo $btn_fg_colour_click; ?>"
               class="color-picker"/>
        <label for="obson_btn_fg_colour_click">Click</label>
    </p>

    <p>
        Button Background Colour<br>
        <input type="color" name="obson_btn_bg_colour"
               id="obson_btn_bg_colour"
               value="<?php echo $btn_bg_colour_normal; ?>"
               class="color-picker"/>
        <label for="obson_btn_bg_colour">Normal</label><br>
        <input type="color" name="obson_btn_bg_colour_hover"
               id="obson_btn_bg_colour_hover"
               value="<?php echo $btn_bg_colour_hover; ?>"
               class="color-picker"/>
        <label for="obson_btn_bg_colour_hover">Hover</label><br>
        <input type="color" name="obson_btn_bg_colour_click"
               id="obson_btn_bg_colour_click"
               value="<?php echo $btn_bg_colour_click; ?>"
               class="color-picker"/>
        <label for="obson_btn_bg_colour_click">Click</label>
    </p>

    <p>
        <label for="obson_offers_mode">Mode</label><br>
        <select name="obson_offers_mode" id="obson_offers_mode">
            <option value="active" <?php selected( $selected, 'active' ); ?>>
                Active
            </option>
            <option
                value="inactive" <?php selected( $selected, 'inactive' ); ?>>
                Inactive
            </option>
            <option value="test" <?php selected( $selected, 'test' ); ?>>
                Test
            </option>
        </select>
    </p>

    <?php
}

/**
 * This function makes it possible to group elements by post type and save
 * them with a single function call. Note that this could be more efficient
 * as we don't really need to save the whole gamut of possible elements every
 * time, but it's simple and consistent. And since the user can change the
 * question type  there possibly wouldn't be much gained by only saving the
 * essential fields.
 *
 * @param $post_id
 * @param $elements
 */
function obson_save_posts( $post_id, $elements ) {
    // Don't auto-save
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check nonce
    if ( ! isset( $_POST['meta_box_nonce'] )
         || ! wp_verify_nonce(
            $_POST['meta_box_nonce'],           // posted nonce
            'obson_offers_meta_box_nonce'       // stored nonce
        )
    ) {
        return;
    }

    // Abort if user has insufficient permission
    if ( ! current_user_can( 'edit_post' ) ) {
        return;
    }

    foreach ( $elements as $element ) {
        update_post_meta(
            $post_id,
            $element,
            isset( $_POST[ $element ] )
                ? esc_attr( $_POST[ $element ] )
                : ''
        );
    }

}

/**
 * @param $post_id
 */
function obson_meta_box_offers_save( $post_id ) {
    global $post;

    $post_type = get_post_type( $post );

    switch ( $post_type ) {

        case 'offers':
            $args = array(
                'obson_offers_intro_text',
                'obson_popup_fg_colour',
                'obson_popup_bg_colour',
                'obson_offers_button_text',
                'obson_btn_fg_colour',
                'obson_btn_fg_colour_hover',
                'obson_btn_fg_colour_click',
                'obson_btn_bg_colour',
                'obson_btn_bg_colour_hover',
                'obson_btn_bg_colour_click',
                'obson_offers_mode'
            );
            break;

        case 'questions':
            $args = array(
                'obson_questions_intro_text',
                'obson_questions_offer',
                'obson_seq',
                'obson_question_state',
                'obson_question_button_text',
                'obson_question_accordion',
                'obson_question_first_name_caption',
                'obson_question_last_name_caption',
                'obson_question_email_caption',
                'obson_mc_type',
                'obson_question_edit_type',
                'obson_allow_download',
                'obson_lead_magnet',
                'obson_lead_magnet_link_text',
                'obson_site_email_link_text',
                'obson_email_signature',
                'obson_question_webmaster_address',
                'obson_question_respondent_email_text',
                'obson_question_salutation',
                'obson_question_name_style',
                'obson_question_exit_to',
            );
            for ( $i = 1; $i <= 10; $i ++ ) {
                $args[] = 'obson_multiple_choice_text_' . $i;
            }
            break;


        default:
            return;
    }

    obson_save_posts( $post_id, $args );
}

add_action( 'save_post', 'obson_meta_box_offers_save' );

/**
 *
 */
function obson_meta_box_questions() {
    global $post;                           // The current WP post

    //echo "post id: " . $post->ID . "<br>";

    $values = get_post_custom( $post->ID );   // Recover current values

    // Uncomment next line for a diagnostic list of all available values
    //echo "values = <pre>" . print_r($values, 1) . "</pre><br>";

    // Intro text (i.e. the question)
    $intro_text = isset( $values['obson_questions_intro_text'][0] )
        ? esc_attr( $values['obson_questions_intro_text'][0] )
        : '';

    // Offer this question belongs to
    $selected_offer = isset( $values['obson_questions_offer'][0] )
        ? esc_attr( $values['obson_questions_offer'][0] )
        : '';

    // Sequence number
    $seq = ( isset( $values['obson_seq'][0] ) && ctype_digit( $values['obson_seq'][0] ) )
        ? $values['obson_seq'][0]
        : '0';

    // Whether this question is active or inactive
    $selected_state = isset( $values['obson_question_state'][0] )
        ? esc_attr( $values['obson_questions_state'][0] )
        : '';

    // Button text
    $button_text = isset( $values['obson_question_button_text'][0] )
        ? esc_attr( $values['obson_question_button_text'][0] )
        : 'Next question';

    // Question style - no default: must be set manually
    $qstyle = isset( $values['obson_question_accordion'][0] )
        ? esc_attr( $values['obson_question_accordion'][0] )
        : '';

    // 'Sign up' selections
    $first_name_caption = isset( $values['obson_question_first_name_caption'][0] )
        ? esc_attr( $values['obson_question_first_name_caption'][0] )
        : 'First name';

    $last_name_caption = isset( $values['obson_question_last_name_caption'][0] )
        ? esc_attr( $values['obson_question_last_name_caption'][0] )
        : 'Last name';

    $email_caption = isset( $values['obson_question_email_caption'][0] )
        ? esc_attr( $values['obson_question_email_caption'][0] )
        : 'Email address';

    $multiple_choice_type = isset( $values['obson_mc_type'][0] )
        ? esc_attr( $values['obson_mc_type'][0] )
        : 'choose_one';

    $edit_type = isset( $values['obson_question_edit_type'][0] )
        ? esc_attr( $values['obson_question_edit_type'][0] )
        : 'simple';

    $lead_magnet = isset( $values['obson_lead_magnet'][0] )
        ? esc_attr( $values['obson_lead_magnet'][0] )
        : '';

    $allow_download = isset( $values['obson_allow_download'][0] )
        ? esc_attr( $values['obson_allow_download'][0] )
        : 'no';
    if ( $allow_download != 'yes' ) {
        $allow_download = 'no';
    }

    $email_link_text_site = isset( $values['obson_site_email_link_text'][0] )
        ? esc_attr( $values['obson_site_email_link_text'][0] )
        : '';

    $email_signature = isset( $values['obson_email_signature'][0] )
        ? esc_attr( $values['obson_email_signature'][0] )
        : '';

    $link_text = isset( $values['obson_lead_magnet_link_text'][0] )
        ? esc_attr( $values['obson_lead_magnet_link_text'][0] )
        : 'Download your reward';

    $webmaster_email = isset( $values['obson_question_webmaster_address'][0] )
        ? esc_attr( $values['obson_question_webmaster_address'][0] )
        : '';

    $respondent_email_text = isset( $values['obson_question_respondent_email_text'][0] )
        ? esc_attr( $values['obson_question_respondent_email_text'][0] )
        : '';

    $salutation_style = isset( $values['obson_question_salutation'][0] )
        ? esc_attr( $values['obson_question_salutation'][0] )
        : 'name_only';

    $name_style = isset( $values['obson_question_name_style'][0] )
        ? esc_attr( $values['obson_question_name_style'][0] )
        : 'both';

    $exit_to = isset( $values['obson_question_exit_to'][0] )
        ? esc_attr( $values['obson_question_exit_to'][0] )
        : '';

    // 'Choose One' selections
    for ( $i = 1; $i <= 10; $i ++ ) {
        $id                         = 'obson_multiple_choice_text_' . $i;
        $multiple_choice_text[ $i ] = isset( $values[ $id ][0] )
            ? esc_attr( $values[ $id ][0] )
            : '';
    }

    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'obson_offers_meta_box_nonce', 'meta_box_nonce' );

    // Get the titles of all existing Offers for use in Offer selector
    $args   = array(
        'post_type' => 'offers',
    );
    $offers = get_posts( $args );

    // Display the meta box form...
    ?>
    <table cols="2" cellpadding="0" cellspacing="5" style="width:96%">
        <tr>
            <td><label for="obson_questions_intro_text">The question</label>
            </td>
            <td width="45%"><textarea
                    name="obson_questions_intro_text"
                    id="obson_questions_text"
                    style="width:98%;"><?php
                    echo $intro_text;
                    ?></textarea>
            </td>
        </tr>
        <tr>
            <td><label for="obson_questions_offer">Associate with
                    offer:</label></td>
            <td width="45%"><select name="obson_questions_offer"
                                    id="obson_questions_offer"
                                    style="width:98%;">
                    <?php
                    foreach ( $offers as $offer ) {
                        echo '<option value="' .
                             $offer->ID . '" ' .
                             selected( $selected_offer, $offer->ID ) .
                             '>' .
                             $offer->post_title .
                             '</option>' .
                             "\n";
                    }
                    ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="obson_question_button_text">'Next' Button
                    Text</label>
            </td>
            <td>
                <input type="text" name="obson_question_button_text"
                       id="obson_question_button_text"
                       style="width:98%;"
                       value="<?php echo $button_text; ?>">
            </td>
        </tr>
        <tr>
            <td><label for="obson_seq">Sequence*</td>
            <td><input type="number"
                       name="obson_seq"
                       id="obson_seq"
                       min="0" max="100"
                       style="width:100px;"
                       value="<?php echo $seq; ?>"></td>
        </tr>
        <tr>
            <td><label for="obson_question_state">This question is*</label>
            </td>
            <td>
                <select name="obson_question_state"
                        id="obson_question_state"
                        style="width:100px;">
                    <option value="active"
                        <?php selected( $selected_state, 'active' ); ?>>Active
                    </option>
                    <option value="inactive"
                        <?php selected( $selected_state, 'inactive' ); ?>>
                        Inactive
                    </option>
                </select>
            </td>
        </tr>
    </table>

    <p style="padding:15px;">* Active questions will be presented in the given
        sequence; inactive questions will be omitted. Sequence numbers need
        not be consecutive but may not be negative or greater then 100. If two
        (or more) questions have the same sequence number, either of them may
        be selected.</p>

    <h3>Choose one of the answer styles below:</h3>

    <!-- Accordion starts here -->

    <div class="accordion">

        <!-- 'Sign up' option -->

        <input id="sign_up" name="obson_question_accordion" type="radio"
            <?php checked( $qstyle, 'sign_up' ); ?> value="sign_up">

        <label for="sign_up">Sign up</label>

        <div style="width:100%; border: 1px transparent">
            <p>This option simply presents the respondent with fields for
                entering name and email address. It should normally be the
                first 'question' displayed. The email address will be checked
                for validity (it must have a verifiable domain) and an email
                will be sent to that address before proceeding to further
                questions in the sequence.
            </p>
            <table width="98%">
                <tr>
                    <td width="50%">
                        <label for="obson_question_name_caption">Heading for
                            first name</label>
                    </td>
                    <td>
                        <input type="text"
                               id="obson_question_first_name_caption"
                               name="obson_question_first_name_caption"
                               value="<?php echo $first_name_caption; ?>">
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <label for="obson_question_last_name_caption">Heading
                            for last name</label>
                    </td>
                    <td>
                        <input type="text"
                               id="obson_question_last_name_caption"
                               name="obson_question_last_name_caption"
                               value="<?php echo $last_name_caption; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_question_email_caption">Heading for
                            email address</label>
                    </td>
                    <td>
                        <input type="text" id="obson_question_email_caption"
                               name="obson_question_email_caption"
                               value="<?php echo $email_caption; ?>">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Multiple-choice' option -->

        <input id="multiple_choice" name="obson_question_accordion"
               type="radio"
            <?php checked( $qstyle, 'multiple_choice' ); ?>
               value="multiple_choice">

        <label for="multiple_choice">Multiple choice</label>

        <!-- LH column -->
        <div style="width:100%; border: 1px transparent">
            <div style="float: left; width:45%;">
                <ol>
                    <?php
                    for ( $i = 1; $i <= 10; $i ++ ) {
                        echo "<li><input type='text' name='obson_multiple_choice_text_$i' " .
                             "value='{$multiple_choice_text[$i]}'></li>\n";
                    }
                    ?>
                </ol>
            </div>

            <!-- RH column -->
            <div style="width:45%; float: right;">
                <p>You can enter up to ten possible answers. Blank
                    answers will be ignored.</p>

                <p>The respondent should:</p>
                <select name="obson_mc_type" id="obson_mc_type">
                    <option value="choose_one"
                        <?php selected( $multiple_choice_type, 'choose_one' ); ?>>
                        Choose one answer
                    </option>
                    <option value="choose_some"
                        <?php selected( $multiple_choice_type, 'choose_some' ); ?>>
                        Choose all answers that apply
                    </option>
                    <option value="rank"
                        <?php selected( $multiple_choice_type, 'rank' ); ?>>
                        Rank answers in order of importance
                    </option>
                </select>
            </div>
        </div>

        <!-- 'Own words' option -->

        <input id="acrd1-item5" name="obson_question_accordion" type="radio"
            <?php checked( $qstyle, 'own_words' ); ?> value="own_words">

        <label for="acrd1-item5">In their own words</label>

        <div style="width:100%; border: 1px transparent">
            <label for="obson_question_edit_type">Choose one of the
                following</label><br>
            <select name="obson_question_edit_type"
                    id="obson_question_edit_type">
                <option
                    value="simple" <?php selected( $edit_type, 'simple' ); ?>>
                    Simple one-line response
                </option>
                <option
                    value="extended" <?php selected( $edit_type, 'extended' ); ?>>
                    Extended response
                </option>
            </select>

            <p>If you choose 'simple one-line response' the user will be
                offered a simple text box in which to enter a response, the
                length of which will be limited to a maximum of 255
                characters.
            </p>

            <p>If you choose 'extended response' the user will be offered a
                larger area in which to enter a response</p>
        </div>

        <!-- 'Thank you' option -->

        <input id="thank_you" name="obson_question_accordion" type="radio"
            <?php checked( $qstyle, 'thank_you' ); ?> value="thank_you">

        <label for="thank_you">Thank you!</label>

        <div style="width:100%; border: 1px transparent">
            <p>When respondents exit from this page they are deemed to have
                completed the survey. An email will be sent to you, at
                the 'webmaster' address you supply below, listing their
                responses. A thank-you email will also be sent to the
                respondent; you can change the wording below. Choose a
                'lead-magnet' (from your WordPress Media Library) to reward
                the respondent for completing the survey. This will be sent
                as an attachment to the thank-you email. <b>Caution: you must
                    be able to send emails from your server for this to work.</b></p>

            <table width="98%">
                <tr>
                    <td width="50%">
                        <label for="obson_question_webmaster_address">Your
                            email address (as 'webmaster')</label>
                    </td>
                    <td>
                        <input type="email"
                               id="obson_question_webmaster_address"
                               name="obson_question_webmaster_address"
                               style="width:100%"
                               value="<?php echo $webmaster_email; ?>">
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <label for="obson_question_respondent_email_text">Text
                            for email to the respondent</label>
                    </td>
                    <td>
                        <textarea id="obson_question_respondent_email_text"
                                  name="obson_question_respondent_email_text"
                                  style="width:100%"><?php
                            echo $respondent_email_text;
                            ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_question_salutation">Salutation
                            style</label>
                    </td>
                    <td>
                        <select name="obson_question_salutation"
                                id="obson_question_salutation">
                            <option
                                value="hi" <?php selected( $salutation_style, 'hi' );
                            ?>>Hi [name]
                            </option>
                            <option
                                value="dear" <?php selected( $salutation_style, 'dear' );
                            ?>>Dear [name]
                            </option>
                            <option
                                value="name_only" <?php selected( $salutation_style, 'name_only' );
                            ?>>[name only]
                            </option>
                            <option
                                value="none" <?php selected( $salutation_style, 'none' );
                            ?>>[none]
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_question_name_style">Name
                            style</label>
                    </td>
                    <td>
                        <select name="obson_question_name_style"
                                id="obson_question_name_style">
                            <option
                                value="first" <?php selected( $name_style, 'first' );
                            ?>>First name only
                            </option>
                            <option
                                value="both" <?php selected( $name_style, 'both' );
                            ?>>First and last names
                            </option>
                            <option
                                value="french" <?php selected( $name_style, 'french' );
                            ?>>LAST-NAME, first-name
                            </option>
                            <option
                                value="formal" <?php selected( $name_style, 'formal' );
                            ?>>Formal ('Sir or Madam')
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_lead_magnet">Lead-magnet</label>
                    </td>
                    <td>
                        <select name="obson_lead_magnet"
                                id="obson_lead_magnet">
                            <?php
                            $media_query = new WP_Query(
                                array(
                                    'post_type'      => 'attachment',
                                    'post_status'    => 'inherit',
                                    'posts_per_page' => - 1,
                                )
                            );
                            foreach ( $media_query->posts as $media ) {
                                // To download this file we will use its url, which will be
                                // accessible from wp_get_attachment_url($media->ID). All we need
                                // here is the media id.
                                echo "                            ";    // indent for html readability
                                echo "<option " .
                                     selected( $lead_magnet, $media->ID ) .
                                     " value='{$media->ID}'>{$media->post_title} " .
                                     "({$media->post_mime_type})</option>\n";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_lead_magnet_access">Access to
                            lead-magnet</label>
                    </td>
                    <td>
                        <select id="obson_allow_download"
                                name="obson_allow_download"
                                style="width:100%">
                            <?php
                            if ( ! isset( $allow_download ) || $allow_download != 'yes' ) {
                                $allow_download = 'no';
                            }
                            ?>
                            <option
                                value="no" <?php selected( $allow_download,
                                'no' ) ?>>Email attachment only
                            </option>
                            <option
                                value="yes" <?php selected( $allow_download,
                                'yes' ) ?>>Email attachment and download link
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_lead_magnet_link_text">Text for
                            lead-magnet link</label>
                    </td>
                    <td>
                        <input type="text" id="obson_lead_magnet_link_text"
                               name="obson_lead_magnet_link_text"
                               style="width:100%"
                               value="<?php echo $link_text; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_question_exit_to">Lead-magnet
                            information page:</label>
                    </td>
                    <td>
                        <select name="obson_question_exit_to"
                                id="obson_question_exit_to"
                                style="width:100%;">
                            <?php
                            $args  = array(
                                'sort_order'   => 'asc',
                                'sort_column'  => 'post_title',
                                'hierarchical' => 1,
                                'exclude'      => '',
                                'include'      => '',
                                'meta_key'     => '',
                                'meta_value'   => '',
                                'authors'      => '',
                                'child_of'     => 0,
                                'parent'       => - 1,
                                'exclude_tree' => '',
                                'number'       => '',
                                'offset'       => 0,
                                'post_type'    => 'page',
                                'post_status'  => 'publish'
                            );
                            $pages = get_pages( $args );
                            foreach ( $pages as $page ) {
                                echo "                            ";    // indent for readability
                                echo "<option value='{$page->ID}' " .
                                     selected( $exit_to, $page->ID ) .
                                     ">{$page->post_title}</option>\n";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_site_email_link_text">Text for info
                            page link in email</label>
                    </td>
                    <td>
                        <input type="text" id="obson_site_email_link_text"
                               name="obson_site_email_link_text"
                               style="width:100%"
                               value="<?php echo $email_link_text_site; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="obson_email_signature">Email
                            signature</label>
                    </td>
                    <td>
                        <input type="text" id="obson_email_signature"
                               name="obson_email_signature"
                               style="width:100%"
                               value="<?php echo $email_signature; ?>">
                    </td>
                </tr>
            </table>

        </div>

    </div>

    <?php
}

// -------
// Helpers
// -------

// Change the columns for the edit CPT screen
function obson_change_columns( $cols ) {
    $cols = array(
        'cb'     => '<input type="checkbox" />',
        'title'  => __( 'Title', 'trans' ),
        'date'   => __( 'Date', 'trans' ),
        'author' => __( 'Author', 'trans' ),
        'tags'   => __( 'Tags', 'trans' ),
    );

    return $cols;
}

add_filter( "manage_offers_posts_columns", "obson_change_columns" );

/**
 * List the variables that will be used in query strings and HTTP posts.
 * Anything not included in this list will be disallowed by WP.
 *
 * @param $vars
 *
 * @return array
 */
function obson_query_vars_filter( $vars ) {
    $vars[] = 'show';
    $vars[] = 'of_email';
    $vars[] = 'signup';
    $vars[] = 'resp_first_name';
    $vars[] = 'resp_last_name';
    $vars[] = 'resp_email';
    $vars[] = 'multi_choice';
    $vars[] = 'own_words';
    $vars[] = 'thank_you';
    $vars[] = 'rb_answer';
    $vars[] = 'cb_answer_1';
    $vars[] = 'cb_answer_2';
    $vars[] = 'cb_answer_3';
    $vars[] = 'cb_answer_4';
    $vars[] = 'cb_answer_5';
    $vars[] = 'cb_answer_6';
    $vars[] = 'cb_answer_7';
    $vars[] = 'cb_answer_8';
    $vars[] = 'cb_answer_9';
    $vars[] = 'cb_answer_10';
    $vars[] = 'rank_1';
    $vars[] = 'rank_2';
    $vars[] = 'rank_3';
    $vars[] = 'rank_4';
    $vars[] = 'rank_5';
    $vars[] = 'rank_6';
    $vars[] = 'rank_7';
    $vars[] = 'rank_8';
    $vars[] = 'rank_9';
    $vars[] = 'rank_10';
    $vars[] = 'text_response';
    $vars[] = 'obs';    // Neither 'error' nor 'obson_error' seem to work.
                        // See obson.net/topics/surprises/ for more info
    return $vars;
}

add_filter( 'query_vars', 'obson_query_vars_filter' );

/**
 * Landing pages have their own template.
 */
add_filter( 'template_include', 'obson_landing_page_template' );

function obson_landing_page_template( $template ) {

    if ( get_query_var( 'post_type' ) == 'offers' ) {
        // We get this as an absolute path but assume the template is in the
        // same directory as the current file (i.e. the plugins directory).
        $new_template = plugin_dir_path( __FILE__ ) . 'templates/offer-template.php';

        if ( '' != $new_template ) {
            return $new_template;
        }
    }

    return $template;
}

/**
 * Styling for additional admin entries
 */
function obson_admin_theme_style() {
    wp_enqueue_style( 'my-admin-theme', plugins_url( 'css/wp-admin.css', __FILE__ ) );
}

add_action( 'admin_enqueue_scripts', 'obson_admin_theme_style' );

/**
 * Session handling
 */
add_action( 'init', 'obson_session_start', 1 );
add_action( 'wp_logout', 'obson_end_session' );
add_action( 'wp_login', 'obson_end_session' );

/**
 *
 */
function obson_session_start() {
    if ( empty( session_id() ) ) {
        session_start();
    }
}

function obson_end_session() {
    session_destroy();
}

/**
 * Change the email-from name from WordPress to Obson.
 * Note: This should really be user-defined (later).
 */
function obson_mail_name( $name ) {
    return 'obson'; // new email name from sender.
}

add_filter( 'wp_mail_from_name', 'obson_mail_name' );

/**
 * Emails to be sent as HTML
 *
 * @param $content_type
 *
 * @return string
 */
function obson_set_content_type( $content_type ) {
    return 'text/html';
}

add_filter( 'wp_mail_content_type', 'obson_set_content_type' );

