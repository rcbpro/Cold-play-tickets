<?php
if ('itg-em-user-contact.php' == basename($_SERVER['SCRIPT_FILENAME']))
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
    <h2>Edit your Contact Information <a href="profile.php" class="button-secondary">Edit Profile</a></h2>
    <?php
    /** Whether the contact info is updated
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
    */
    /** If a post method has been initiated */
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        /** Strip slash */
        if ( get_magic_quotes_gpc() ) {
            $_POST = array_map( 'stripslashes_deep', $_POST );
        }
        
        /** strip all tags from the post request */
        $_POST = array_map('strip_tags', $_POST);
        
        /** Initiate the insert array */
        $insert = array(
            'address' => $_POST['address'],
            'state' => $_POST['state'],
            'city' => $_POST['city'],
            'country' => $_POST['country'],
	    'univ' => $_POST['univ'],
	    'year' => $_POST['year'],
	    'roll_no' => $_POST['roll_no'],
	    'dept' => $_POST['dept']
        );
        
        /** Initiate the datatype array */
        $insert_dt = array(
            '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s'
        );
        
        /** Validate the phone number */
        $e_flag = false;
        $error = '';
        if(preg_match('/^\d{8,15}$/', $_POST['ph_no'])) {
            $insert['ph_no'] = $_POST['ph_no'];
            $insert_dt[] = '%s';
            $e_flag = true;
        }
        else {
            $error = <<<EOD
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> You phone number is either too short, too long or invalid. Use a valid one please. You Contact information is not complete yet.</p>
    </div>
</div>
EOD;
        }
	
	/** Validate the university name and roll number */
	if($_POST['univ'] == '' || $_POST['roll_no'] == '') {
	    $e_flag = false;
	    
	    $error .= '<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> Please give us a valid University name and a valid Roll Number. So that we can contact you :)</p>
    </div>
</div>';
	}
        
	/** Append the complete parameter */
	if($e_flag) {
	    $insert['complete'] = 1;
            $insert_dt[] = '%d';
	}
	/** First update the WPDB */
	$wpdb->update($wpdb->usermeta, array('meta_value' => $_POST['first_name']), array('user_id' => $_SESSION['itg_em_wpuid'], 'meta_key' => 'first_name'), array('%s'), array('%d', '%s'));
	$wpdb->update($wpdb->usermeta, array('meta_value' => $_POST['last_name']), array('user_id' => $_SESSION['itg_em_wpuid'], 'meta_key' => 'last_name'), array('%s'), array('%d', '%s'));
	
        /** Now update the db */
        if(FALSE !== $wpdb->update($itg_em_db_table_name['user_table'], $insert, array('id'=>$_SESSION['itg_em_uid']), $insert_dt, array('%d'))) {
            ?>
		<div class="ui-widget">
			<div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding: 0 .7em;"> 
				<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				<strong>Done!</strong> Your Contact information has been updated.</p>
			</div>
		</div>
            <?php
        }
        else {
            ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 5px;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> Something bad really happened. Related to DB! Please contact the system admin ASAP.</p>
    </div>
</div>
            <?php
        }
        if('' != $error) {
            echo $error;
        }
    }
    
    /** Get current data */
    $itg_em_current_user = itg_em_user_current_user_info();
    
    /**
     * A little hack to fix the cache thing
     * I really dont want to plug the get_userinfo again :|
     */
    $itg_em_current_user->first_name = (isset($_POST['first_name']))? $_POST['first_name'] : $itg_em_current_user->first_name;
    $itg_em_current_user->last_name = (isset($_POST['last_name']))? $_POST['last_name'] : $itg_em_current_user->last_name;
    
    /** Regenerate the complete session */
    $_SESSION['itg_em_complete'] = $itg_em_current_user->complete;
    
    /** A little hack to hide the first time wrong error */
    if($_SESSION['itg_em_complete'] == 1) {
        ?>
        <style type="text/css">
            #itg_em_user_notice {display: none;}
        </style>
        <?php
    }
    ?>
    <p>
        Hi there <?php echo $itg_em_current_user->first_name . ' ' . $itg_em_current_user->last_name; ?>. Please Update your contact information so that we can get in touch with you.
        <br />
        To change your other Profile information, password, aim etc... please Go to the Profile menu. <a href="profile.php" class="button-secondary">Go Now.</a>
    </p>
    <p><span class="description">Items marked with * are required</span></p>
    <form class="validate_form" method="post" action="">
        <ul class="ui-icon-ena">
	    <li>
		<label for="first_name">First Name:*</label>
		<input type="text" name="first_name" id="first_name" value="<?php echo $itg_em_current_user->first_name; ?>" class="validate[required,length[3,100]]" />
	    </li>
	    <li>
		<label for="last_name">Last Name*:</label>
		<input type="text" name="last_name" id="last_name" value="<?php echo $itg_em_current_user->last_name; ?>" class="validate[required,length[2,100]]" />
	    </li>
            <li>
                <label for="ph_no">Phone Number*: </label>
                <input type="text" class="validate[required,custom[onlyNumber],length[8,15]]" value="<?php echo $itg_em_current_user->ph_no; ?>" name="ph_no" id="ph_no" />
            </li>
	    <li>
		<label for="univ">University/College/School Name*:</label>
		<input type="text" class="validate[required,length[4,100]]" value="<?php echo $itg_em_current_user->univ; ?>" name="univ" id="univ" />
		<br />
		<span class="description">Please give in your University information. Write in full name please.</span>
	    </li>
	    <li>
		<label for="year">Session Year:</label>
		<input type="text" name="year" id="year" class="validate[optional,custom[onlyNumber],length[4,4]]" value="<?php echo (($itg_em_current_user->year == 0)? '' : $itg_em_current_user->year); ?>" />
		<br />
		<span class="description">Please enter your Joining year. Like 2008, 2009 etc.</span>
	    </li>
	    <li>
		<label for="dept">Department:</label>
		<input type="text" name="dept" id="dept" class="validate[optional,length[2,100]]" value="<?php echo $itg_em_current_user->dept; ?>" />
	    </li>
	    <li>
		<label for="roll_no">Roll Number*:</label>
		<input type="text" name="roll_no" id="roll_no" class="validate[required,length[2,100]]" value="<?php echo $itg_em_current_user->roll_no; ?>" />
		<br />
		<span class="description">Your Unique Roll number given by your institution</span>
	    </li>
            <li>
                <label for="address">Address: </label>
                <input type="text" class="validate[optional,length[0,255]]" value="<?php echo $itg_em_current_user->address; ?>" name="address" id="address" />
            </li>
            <li>
                <label for="state">State: </label>
                <input type="text" class="validate[optional,length[0,100]]" name="state" id="state" value="<?php echo $itg_em_current_user->state; ?>" />
            </li>
            <li>
                <label for="city">City: </label>
                <input type="text" name="city" id="city" class="validate[optional,length[0,100]]" value="<?php echo $itg_em_current_user->city; ?>" />
            </li>
            <li>
                <label for="country">Country: </label>
                <input type="text" name="country" id="country" class="validate[optional,length[0,100]]" value="<?php echo $itg_em_current_user->country; ?>" />
            </li>
            <li>
                <p>
                    <span class="description">No HTML or other things allowed for any of the contact field. It will be stripped off.</span>
                </p>
            </li>
            <li>
                <input type="submit" value="Update" class="button-primary" />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" value="Reset" class="button-secondary" />
            </li>
        </ul>
    </form>
</div>