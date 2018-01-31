<?php
if ('itg-em-user-team-add.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_subs')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}
/**
 * Some globals
 */
global $wpdb, $itg_em_db_table_name;
?>
<div class="wrap">
    <h2>Add Team Members <a class="button-secondary" href="admin.php?page=itg_em_user_edit_team_page">Edit Members</a></h2>
    <?php
    /** Whether the contact info is updated */
    if($_SESSION['itg_em_complete'] == 0) {
        ?>
        <div class="ui-widget">
            <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
                <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
                <strong>Alert:</strong> You heard the notice cowboy! Update your contact information first! <a href="admin.php?page=itg_em_user_profile_page" class="button-primary">Update NOW</a></p>
            </div>
        </div>  
        <?php
        return;
    }
    
    /**
     * The default value
     */
    $first_name = 'First Name';
    $last_name = 'Last Name';
    $email = 'user@mail.com';
    $ph_no = '9831000000';
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        /** Strip slash */
        if ( get_magic_quotes_gpc() ) {
            $_POST = array_map( 'stripslashes_deep', $_POST );
        }
        
        /** strip all tags from the post request */
        $_POST = array_map('strip_tags', $_POST);
        
        /** Extract to override the default value */
        extract($_POST);
        
        $error = '';
        /** Validate the email first */
        if(preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $_POST['email'])) {
            /** Validate the phone number. Just for insertion. */
            if(!preg_match('/^\d{8,15}$/', $_POST['ph_no'])) {
                $error .= <<<EOD
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> You phone number is either too short, too long or invalid. Use a valid one please. Although it is not mandatory, please do not give us invalid information :).</p>
    </div>
</div>
EOD;
                $_POST['ph_no'] = 0;
            }
            
            /** See if the email is already registered as a team member */
            $existing_mem = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$itg_em_db_table_name['user_team']} WHERE email=%s LIMIT 1", $_POST['email']));
            
            if(!$existing_mem) {
                /** All done now insert it */
                /** But first name verification */
                if(strlen($_POST['first_name']) >= 3 && strlen($_POST['last_name']) >=2) {
                    /** Everything valid. Insert */
                    $insert = array(
                        'itgem_uid' => $_SESSION['itg_em_uid'],
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'],
                        'email' => $_POST['email'],
                        'ph_no' => $_POST['ph_no']
                    );
                    $insert_dt = array('%d', '%s', '%s', '%s', '%s');
                    
                    if($wpdb->insert($itg_em_db_table_name['user_team'], $insert, $insert_dt)) {
                        ?>
		<div class="ui-widget">
			<div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding: 0 .7em;"> 
				<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				<strong>Done!</strong> Your Team member has been added. <a class="button-secondary" href="admin.php?page=itg_em_user_edit_team_page&pmode=edit&item_id=<?php echo $wpdb->insert_id; ?>">Edit it</a></p>
			</div>
		</div>
                        <?php
                        if($error != '')
                            echo $error;
                    }
                }
                else {
                    ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> First name and/or last name is too short. Please rectify.</p>
    </div>
</div>
                    <?php
                }
            }
            else {
                ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> This email has already been registered by you or someone else. Please use some different email. You can select the same member for different events. No need to add them seperately</p>
    </div>
</div>
                <?php
            }
        }
        else {
            ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> Invalid email. Please use a valid one.</p>
    </div>
</div>
            <?php
        }
    }
    ?>
    <p>
        <span class="description">These team members will be shown when you apply for an event having multiple members.</span>
    </p>
    <form action="" method="post" class="validate_form">
        <ul>
            <li>
                <span class="description">Items marked with * are required</span>
            </li>
            <li>
                <label for="first_name">Member First Name*: </label>
                <input type="text" class="validate[required,length[3,255]]" name="first_name" id="first_name" value="<?php echo $first_name; ?>" />
            </li>
            <li>
                <label for="last_name">Member Last Name*: </label>
                <input type="text" class="validate[required,length[2,255]]" name="last_name" id="last_name" value="<?php echo $last_name; ?>" />
            </li>
            <li>
                <label for="email">Member Email*: </label>
                <input type="text" class="validate[required,custom[email]]" name="email" id="email" value="<?php echo $email; ?>" />
            </li>
            <li>
                <label for="ph_no">Member Phone Number: </label>
                <input type="text" class="validate[optional,custom[onlyNumber],length[0,15]]" name="ph_no" id="ph_no" value="<?php echo $ph_no; ?>" />
            </li>
            <li>
                <input type="submit" class="button-primary" value="Add Member" />
            </li>
            <li>
                <span class="description">Do not add any fancy HTML here! We will strip it off.</span>
            </li>
        </ul>
    </form>
</div>