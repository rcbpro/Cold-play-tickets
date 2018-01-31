<?php
if ('itg-em-admin-general.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_admin')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}
/**
 * Edit Options like
 * Inst. Address
 * Nickname
 * Contact email etc
 */
/**
 * @ref
 * $itg_em_options = array(
        'institute_name' => 'RCC Institute of Information Technology',
        'institute_short_name' => 'RCCIIT',
        'fest_name' => 'Techtrix\'X',
        'address1' => 'Canal South Road',
        'address2' => 'Beliaghata',
        'country' => 'India',
        'state' => 'West Bengal',
        'city' => 'Kolkata',
        'pincode' => '700015',
        'contact_email' => get_option('admin_email'),
        'phone_num1' => '033-2323-2463',
        'phone_num2' => '033-2323-3356',
        'notes' => ''
    );
 */
global $itg_em_options;
?>
<div class="wrap">
    <?php
    wp_tiny_mce(false, array(
        "editor_selector" => "my_tiny_mce"
    ));
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        /** Strip slash */
        if ( get_magic_quotes_gpc() ) {
            $_POST = array_map( 'stripslashes_deep', $_POST );
        }
        
        /** Now save it */
        $new_itg_em_options = array(
            'institute_name' => $_POST['institute_name'],
            'institute_short_name' => $_POST['institute_short_name'],
            'fest_name' => $_POST['fest_name'],
            'address1' => $_POST['address1'],
            'address2' => $_POST['address2'],
            'country' => $_POST['country'],
            'state' => $_POST['state'],
            'city' => $_POST['city'],
            'pincode' => $_POST['pincode'],
            'contact_email' => $_POST['contact_email'],
            'phone_num1' => $_POST['phone_num1'],
            'phone_num2' => $_POST['phone_num2'],
            'notes' => $_POST['notes'],
            'currency' => $_POST['currency']
        );
        
        update_option('itg_em_options', $new_itg_em_options);
        
        $itg_em_options = get_option('itg_em_options');
        ?>
        <div class="updated fade">Information Updated</div>
        <?php
    }
    ?>
    <h2>Edit Institute Information</h2>
    <form method="post" action="">
        <ul>
            <li>
                <label for="institute_name">Institute Name:</label>
                <input type="text" name="institute_name" id="institute_name" value="<?php echo $itg_em_options['institute_name']; ?>" />
            </li>
            <li>
                <label for="institute_short_name">Institute Short Name:</label>
                <input type="text" name="institute_short_name" id="institute_short_name" value="<?php echo $itg_em_options['institute_short_name']; ?>" />
            </li>
            <li>
                <label for="fest_name">Fest Name:</label>
                <input type="text" name="fest_name" id="fest_name" value="<?php echo $itg_em_options['fest_name']; ?>" />
            </li>
            <li>
                <label for="address1">Address1:</label>
                <input type="text" name="address1" id="address1" value="<?php echo $itg_em_options['address1']; ?>" />
            </li>
            <li>
                <label for="address2">Address2:</label>
                <input type="text" name="address2" id="address2" value="<?php echo $itg_em_options['address2']; ?>" />
            </li>
            <li>
                <label for="country">Country:</label>
                <input type="text" name="country" id="country" value="<?php echo $itg_em_options['country']; ?>" />
            </li>
            <li>
                <label for="state">State:</label>
                <input type="text" name="state" id="state" value="<?php echo $itg_em_options['state']; ?>" />
            </li>
            <li>
                <label for="city">City:</label>
                <input type="text" name="city" id="city" value="<?php echo $itg_em_options['city']; ?>" />
            </li>
            <li>
                <label for="pincode">Pincode:</label>
                <input type="text" name="pincode" id="pincode" value="<?php echo $itg_em_options['pincode']; ?>" />
            </li>
            <li>
                <label for="contact_email">Contact Email:</label>
                <input type="text" name="contact_email" id="contact_email" value="<?php echo $itg_em_options['contact_email']; ?>" />
            </li>
            <li>
                <label for="phone_num1">Phone Number 1:</label>
                <input type="text" name="phone_num1" id="phone_num1" value="<?php echo $itg_em_options['phone_num1']; ?>" />
            </li>
            <li>
                <label for="phone_num2">Phone Number 2:</label>
                <input type="text" name="phone_num2" id="phone_num2" value="<?php echo $itg_em_options['phone_num2']; ?>" />
            </li>
            <li>
                <label for="currency">Currency Sign:</label>
                <input type="text" name="currency" id="currency" value="<?php echo $itg_em_options['currency']; ?>" />
            </li>
            <li>
                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes" class="my_tiny_mce">
                    <?php echo esc_html($itg_em_options['notes']); ?>
                </textarea>
            </li>
            <li>
                <input type="submit" class="button-primary" value="Save Information" />
            </li>
        </ul>
    </form>
</div>