<?php
/**
 * Assumes the following variables will have been set up before inclusion.
 * Others may be added as required.
 *
 * $bg_url: URL of background image
 * $meta_values: array of values returned by get_post_meta()
 *
 */

// Check meta-values are available (in case someone's modified things).
if (!isset($meta_values)) {
    echo "<p>Meta values not set in " . __FILE__ . "</p>\n";
}

// Retrieve meta values for use in CSS
$popup_fg_colour          = $meta_values['obson_popup_fg_colour'][0];
$popup_bg_colour          = $meta_values['obson_popup_bg_colour'][0];
$button_text_colour       = $meta_values['obson_btn_fg_colour'][0];
$button_text_colour_hover = $meta_values['obson_btn_fg_colour_hover'][0];
$button_text_colour_click = $meta_values['obson_btn_fg_colour_click'][0];
$button_bg_colour         = $meta_values['obson_btn_bg_colour'][0];
$button_bg_colour_hover   = $meta_values['obson_btn_bg_colour_hover'][0];
$button_bg_colour_click   = $meta_values['obson_btn_bg_colour_click'][0];

?>
<style>
body, html {
    height:100%;
}
body#landing-page {
    background-image: url('<?php echo $bg_url; ?>');
    background-size: 100%;
    background-position: left top;
    background-repeat: no-repeat;
    background-color: #222;
    font-size: 13pt;
    font-family: "Verdana";
    color: <?php echo $popup_fg_colour; ?>
}

form {
    text-align: left;
    margin: 0;
    margin-bottom: 15px;
    font-size: 13pt;
    font-family: "Verdana";
    color: <?php echo $popup_fg_colour; ?>
}

p {
    font-size: 13pt;
    font-family: "Verdana";
    color: <?php echo $popup_fg_colour; ?>
}

/**
 * #outerbox will occupy the whole screen area so it can serve to locate
 * contained objects in defined positions.
 */
#outerbox {
    width: 100%;
    position: absolute;             /* to place it somewhere on the screen */
    top: 0px;                       /* free space at top */
    bottom: 0px;                    /* makes it lock to the bottom */
    background-position: left top;
    text-align:center;
}

#innerbox-1 {
    width: 400px;
    height: auto;
    padding: 20px 0;
    position: absolute;
    top: calc(30% - 100px);
    right: 15%;
    background-color: <?php echo $popup_bg_colour; ?>;
    background-position: left top;
    border-radius: 12px;
    text-align:center;
    color: <?php echo $popup_fg_colour; ?>;
    z-index: 9;
    opacity: 1;
    box-shadow: 0px 0px 10px 10px #444;
}

#innerbox-2 {
    width: 540px;
    padding: 20px;
    position: absolute;
    top: 10%;
    left: calc(50% - 290px);
    background-color: <?php echo $popup_bg_colour; ?>;
    background-position: left top;
    border-radius: 12px;
    text-align:center;
    color: <?php echo $popup_fg_colour; ?>;
    z-index: 9;
    opacity: 1;
    box-shadow: 0px 0px 10px 10px #444;
}

p.of-text-1 {
    font-size: 25pt;
    margin: 0;
    font-family: "Verdana";
    text-align: center;
}

h3.of-header {
    text-transform: uppercase;
    font-family: Verdana;
    font-weight: 700;
    font-size: 21pt;
    color: <?php echo $popup_fg_colour; ?>;;
    line-height: 25pt;
    margin-bottom: 15pt;
}

p.of-text-2 {
    font-size: 14pt;
    font-family: "Verdana";
}

p.of-spacer {
    line-height: 5pt;
    padding: 0;
    margin: 0;
}

p.of-error {
    width: 100%;
    text-align: center;
    font-weight: 700;
    font-size: 100%;
    font-style: normal;
}

a.of-text-link, a.of-text-link:hover {
    font-size: 15pt;
    font-family: "Verdana";
    color: <?php echo $popup_fg_colour; ?>;
    text-decoration: underline;
    font-size: 100%;
    font-weight: 700;
}

a.of-text-link:hover {
    text-decoration: none;
}

#of_submit {
    color: <?php echo $button_text_colour; ?>;
    text-transform: none;
    font-family: "Verdana";
    padding: 0.6em;
    line-height: 1;
    font-size: 23pt;
    background-color: <?php echo $button_bg_colour; ?>;
    border-radius: 8pt;
    border: 2px solid <?php echo $button_bg_colour; ?>;
    /* padding: 0.84375em 0.875em 0.78125em; */
    margin: 20px;
    margin-top:40px;
    font-weight: 700;
    z-index: 999;
}

#of_submit:hover {
    background-color: <?php echo $button_bg_colour_hover; ?>;
    color: <?php echo $button_text_colour_hover; ?>;
    box-shadow: 0px 0px 10px 10px <?php echo $button_text_colour_hover; ?>;
}

#of_submit:active {
    background-color: <?php echo $button_bg_colour_click; ?>;
    color: <?php echo $button_text_colour_click; ?>;
    box-shadow: none;
}

.of-input {
    margin-top:2px;
    margin-bottom: 2px;
    width: 96%;
    font-size: 1rem;
    padding: 7px;
    color: black;
    font-family: Verdana, "sans-serif";
    font-weight: normal;
}

.short{
    width: 3em;
}

.of_bordered {
    border: 1px solid #666;
    padding 5px;
    background-color: white;
}

</style>