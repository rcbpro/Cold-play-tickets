<?php
if ('itg-em-user-team-edit.php' == basename($_SERVER['SCRIPT_FILENAME']))
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
    <h2>Edit Team Members <a href="admin.php?page=itg_em_user_add_team_page" class="button-secondary">Add New</a></h2>
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
    
    /** The pmode */
    $pmode = (isset($_GET['pmode']))? $_GET['pmode'] : 'view';
    switch($pmode) {
        default :
        case 'view' :
            /** The tabular data */
            
            /** First the pagination */
            include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/pagination.class.php');
            $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $itg_em_db_table_name[user_team] WHERE itgem_uid = %d", $_SESSION['itg_em_uid']));
            if($pagination_count > 0) {
                $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
                $total_page = ceil($pagination_count/10);
                $p = new pagination;
                $p->Items($pagination_count);
                $p->limit(10);
                $p->target("admin.php?page=itg_em_user_edit_team_page&pmode=view");
                $p->currentPage($this_page);
                $p->parameterName('p');
                
                /** Done the pagination. Output it */
                ?>
                <div class="tablenav">
                    <div class="tablenav-pages">
                        <?php $p->show(); ?>
                    </div>
                </div>
                <?php
                
                /** Now get the entries */
                $list_start = ($this_page-1)*10;
                if($list_start >= $pagination_count) $list_start = ($pagination_count - 10);
                if($list_start < 0) $list_start = 0;
                $team_members = itg_em_user_member_list($list_start);
                
                if($team_members) {
                    ?>
                    <p>
                        <span class="description">It is not possible to delete any user for security reason. However you can edit them if you want.</span>
                    </p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            foreach($team_members as $tm) {
                                ?>
                                <tr>
                                    <th scope="col"><?php echo $tm->id; ?></th>
                                    <td><?php echo $tm->first_name; ?></td>
                                    <td><?php echo $tm->last_name; ?></td>
                                    <td><?php echo $tm->email; ?></td>
                                    <td><?php echo $tm->ph_no; ?></td>
                                    <td>
                                        <a href="admin.php?page=itg_em_user_edit_team_page&pmode=edit&item_id=<?php echo $tm->id; ?>" class="button-secondary">Edit</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="tablenav">
                        <div class="tablenav-pages">
                            <?php $p->show(); ?>
                        </div>
                    </div>
                    <?php
                }
                else {
                    ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error:</strong> Something horrible happened. Contact the system administrator ASAP.</p>
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
        <strong>Howdy!</strong> You will see something when you have something. Start adding your team member and then come back to this page ;) <a href="admin.php?page=itg_em_user_add_team_page" class="button-primary">Add Now</a></p>
    </div>
</div>
                <?php
            }
            break;
        case 'edit' :
            if(!isset($_GET['item_id'])) {
                ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Errrrr!</strong> Sorry Cowboy/Cowgirl (really??) Its not possible to poke here without that id thing ;). Try your luck again!</p>
    </div>
</div>
                <?php
            }
            else {
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    
                    /** Is the user authorized */
                    if(itg_em_user_member_detail($_POST['id'])) {
                        /** Strip slash */
                        if ( get_magic_quotes_gpc() ) {
                            $_POST = array_map( 'stripslashes_deep', $_POST );
                        }
                        
                        /** strip all tags from the post request */
                        $_POST = array_map('strip_tags', $_POST);
                        
                        
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
                            $existing_mem = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$itg_em_db_table_name['user_team']} WHERE email=%s AND id != %d LIMIT 1", $_POST['email'], $_POST['id']));
                            
                            if(!$existing_mem) {
                                /** All done now insert it */
                                /** But first name verification */
                                if(strlen($_POST['first_name']) >= 3 && strlen($_POST['last_name']) >=2) {
                                    /** Everything valid. Insert */
                                    $insert = array(
                                        'first_name' => $_POST['first_name'],
                                        'last_name' => $_POST['last_name'],
                                        'email' => $_POST['email'],
                                        'ph_no' => $_POST['ph_no']
                                    );
                                    $insert_dt = array('%s', '%s', '%s', '%s');
                                    
                                    if($wpdb->update($itg_em_db_table_name['user_team'], $insert, array('id' => $_POST['id']), $insert_dt, '%d')) {
                                        ?>
                                <div class="ui-widget">
                                        <div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding: 0 .7em;"> 
                                                <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                                                <strong>Done!</strong> Your Team member has been Updated.</p>
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
                }
                $mem_details = itg_em_user_member_detail($_GET['item_id']);
                if($mem_details) {
                    ?>
                <form action="" method="post" class="validate_form">
                    <ul>
                        <input type="hidden" name="id" id="id" value="<?php echo $mem_details->id; ?>" />
                        <li>
                            <span class="description">Items marked with * are required</span>
                        </li>
                        <li>
                            <label for="first_name">Member First Name*: </label>
                            <input type="text" class="validate[required,length[3,255]]" name="first_name" id="first_name" value="<?php echo $mem_details->first_name; ?>" />
                        </li>
                        <li>
                            <label for="last_name">Member Last Name*: </label>
                            <input type="text" class="validate[required,length[2,255]]" name="last_name" id="last_name" value="<?php echo $mem_details->last_name; ?>" />
                        </li>
                        <li>
                            <label for="email">Member Email*: </label>
                            <input type="text" class="validate[required,custom[email]]" name="email" id="email" value="<?php echo $mem_details->email; ?>" />
                        </li>
                        <li>
                            <label for="ph_no">Member Phone Number: </label>
                            <input type="text" class="validate[optional,custom[onlyNumber],length[0,15]]" name="ph_no" id="ph_no" value="<?php echo $mem_details->ph_no; ?>" />
                        </li>
                        <li>
                            <input type="submit" class="button-primary" value="Update Member" />
                        </li>
                        <li>
                            <span class="description">Do not add any fancy HTML here! We will strip it off.</span>
                        </li>
                    </ul>
                </form> 
                    <?php
                }
                else {
                    ?>
                <div class="ui-widget">
                    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
                        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
                        <strong>Halt:</strong> You are not authorized to edit this entry.</p>
                    </div>
                </div>
                    <?php
                }
            }
            ?>
            <a href="admin.php?page=itg_em_user_edit_team_page" class="button-primary">Go Back</a>
            <?php
            break;
        /** For security purpose we are not allowing them to delete user as of now
        case 'delete' :
            if(!isset($_GET['item_id'])) {
                ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Errrrr!</strong> Sorry Cowboy/Cowgirl (really??) Its not possible to poke here without that id thing ;). Try your luck again!</p>
    </div>
</div>
                <?php
            }
            else {
                if(FALSE !== $wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['user_team']} WHERE id = %d AND itgem_uid = %d", $_GET['item_id'], $_SESSION['itg_em_uid']))) {
                    ?>
		<div class="ui-widget">
			<div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding: 0 .7em;"> 
				<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				<strong>Done!</strong> Your Team member has been deleted. <strong>Add new or Go Back</strong></p>
			</div>
		</div>
                    <?php
                }
                else {
                    ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error!</strong> You might not be authorized to do such thing!</p>
    </div>
</div>
                    <?php
                }
            }
            ?>
            <a href="admin.php?page=itg_em_user_edit_team_page" class="button-primary">Go Back</a>
            <?php
            break;
        */
    }
    ?>
</div>