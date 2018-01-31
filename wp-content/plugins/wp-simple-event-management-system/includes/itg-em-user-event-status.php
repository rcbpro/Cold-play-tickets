<?php
if ('itg-em-user-event-status.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_subs')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}
/**
 * Browse through applied event application
 * List down every Application made by the user
 * User can only make that close or open.
 * Will show Reg ID if approved by the administrator
 * Two pmodes
 * case 'view' Browse through the event lists
 * case 'edit' Apply for a specific event
 */
/**
 * Some globals
 */
global $wpdb, $itg_em_db_table_name;
?>
<div class="wrap">
    <h2>View Application Status:</h2>
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
            $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $itg_em_db_table_name[reg_table] WHERE uid = %d", $_SESSION['itg_em_uid']));
            if($pagination_count > 0) {
                $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
                $total_page = ceil($pagination_count/10);
                $p = new pagination;
                $p->Items($pagination_count);
                $p->limit(10);
                $p->target("admin.php?page=itg_em_user_app_status_page&pmode=view");
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
                $events = itg_em_user_event_status($list_start);
                
                if($events) {
                    /** Shortcuts please */
                    $mem_details = array(
                        0 => '(Optional)',
                        1 => '(Required)'
                    );
                    ?>
                    <table class="widefat">
			<caption>Total Applications: <?php echo $pagination_count; ?> - Showing page <?php echo $this_page . ' / ' . $total_page; ?></caption>
                        <thead>
                            <tr>
                                <th scope="col">App. ID</th>
                                <th scope="col">Event Name</th>
                                <th scope="col">Date</th>
                                <th scope="col">Registration Number</th>
                                <th scope="col">Status</th>
                                <th scope="col">Members</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col">App. ID</th>
                                <th scope="col">Event Name</th>
                                <th scope="col">Date</th>
                                <th scope="col">Registration Number</th>
                                <th scope="col">Status</th>
                                <th scope="col">Members</th>
                                <th scope="col">Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            foreach($events as $event) {
                                ?>
                                <tr>
                                    <th scope="col"><?php echo $event->id; ?></th>
                                    <td><?php echo $event->event_name; ?></td>
                                    <td>
                                        From <em><?php echo date('l jS \of F Y', strtotime($event->start_date)); ?></em> <br /> To <em><?php echo date('l jS \of F Y', strtotime($event->end_date)); ?></em>
                                    </td>
                                    <td>
                                        <?php
                                        if($event->pay_status == 1) {
                                            echo $event->reg_id;
                                        }
                                        else echo 'Will be visible after approval';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if(1 == $event->pay_status)
                                            echo 'Paid';
                                        else
                                            echo 'Unpaid';
                                        ?>
                                        &nbsp;and&nbsp;
                                        <?php
                                        if(1 == $event->user_apply)
                                            echo 'Open';
                                        else
                                            echo 'Close';
                                        ?>
                                    </td>
                                    <td>
                                        <ol class="user_li">
                                        <?php
                                        if($event->tem_mem) {
                                            foreach($event->tem_mem as $tem_mem) {
                                                ?>
                                                <li><?php echo $tem_mem->first_name . ' ' . $tem_mem->last_name; ?></li>
                                                <?php
                                            }
                                        }
                                        else {
                                            ?>
                                            <li>No team member selected/necessary for this event</li>
                                            <?php
                                        }
                                        ?>
                                        </ol>
                                    </td>
                                    <td>
                                        <?php
                                        if($event->pay_status == 0) {
                                            if($event->user_apply == 1) {
                                                ?>
                                                <a class="button-secondary" href="admin.php?page=itg_em_user_app_status_page&pmode=edit&item_id=<?php echo $event->id; ?>">Close the application</a>
                                                <?php
                                            }
                                            else {
                                                ?>
                                                <a class="button-secondary" href="admin.php?page=itg_em_user_app_status_page&pmode=edit&item_id=<?php echo $event->id; ?>">Open the application</a>
                                                <?php
                                            }
                                        }
                                        else {
                                            ?>
                                            <strong>Payment Received and Enrolled</strong>
                                            <?php
                                        }
                                        ?>
                                        <br /><br />
                                        <a class="button-secondary thickbox" href="admin-ajax.php?action=itg_em_event_app_detail_ajax&item_id=<?php echo $event->id; ?>&height=600&width=800">View Application Detail</a>
                                        <br /><br />
                                        <a href="admin-ajax.php?action=itg_em_event_detail_ajax&item_id=<?php echo $event->event_id; ?>&height=400&width=800" class="thickbox button-secondary">View Event Details</a>
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
        <strong>Error:</strong> Something horrible happened. Contact Sys Admin ASAP</p>
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
        <strong>Sorry:</strong> You have not applied for any event as of now.</p>
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
                /** Verify */
                $ver_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE id=%d AND pay_status=0 AND uid=%d", $_GET['item_id'], $_SESSION['itg_em_uid']));
                if(is_object($ver_result)) {
                    /**
                     * Output the form
                     * only the application status
                     * Nothing else
                     */
                    if($_SERVER['REQUEST_METHOD'] == 'POST') {
                        
                        /**
                         * Verify
                         */
                        $verify = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$itg_em_db_table_name['reg_table']} WHERE id=%d AND uid=%d", $_POST['id'], $_SESSION['itg_em_uid']));
                        if($verify) {
                            /**
                             * Update the table
                             */
                            /** Strip slash */
                            if ( get_magic_quotes_gpc() ) {
                                $_POST = array_map( 'stripslashes_deep', $_POST );
                            }
                            
                            /**
                             * First check for any error
                             */
                            $error = '';
                            
                            if($_POST['user_apply'] != 0 && $_POST['user_apply'] != 1) {
                                $_POST['user_apply'] = 1;
                                $error .= 'Application data tampered. Restored to Open';
                            }
                            /** KSES (I prefer to say KISS of Death :P) the note */
                            $note = wp_kses_data($_POST['note']);
                            
                            /** Prepare the update Array */
                            $update_array = array(
                                'user_apply' => $_POST['user_apply'],
                                'note' => $note
                            );
                            $update_dt_array = array('%d', '%s');
                            
                            $app_status_short_cut = array(
                                0 => 'Your Application status has been Closed and not under enrollment.',
                                1 => 'Your Application status has been Opened and under enrollment.'
                            );
                            
                            /**
                             * Now update the database
                             */
                            if($wpdb->update($itg_em_db_table_name['reg_table'], $update_array, array('id' => $_POST['id']), $update_dt_array, array('%d'))) {
                                ?>
    <div class="ui-widget">
            <div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding: 0 .7em;"> 
                    <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                    <strong>Done!</strong> <?php echo $app_status_short_cut[$_POST['user_apply']]; ?> <a href="admin.php?page=itg_em_user_app_status_page" class="button-secondary">Go Back</a></p>
            </div>
    </div>
                                <?php
                            }
                            else {
                                ?>
    <div class="ui-widget">
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
            <strong>Errrrr!</strong> Something went real wrong with the database. Contact System ADMIN ASAP</p>
        </div>
    </div>
                                <?php
                            }
                            
                            if('' != $error) {
                                ?>
    <div class="ui-widget">
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
            <strong>Errrrr!</strong> <?php echo $error; ?></p>
        </div>
    </div>
                                <?php
                            }
                            /** Reset Query */
                            $ver_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE id=%d AND pay_status=0 AND uid=%d", $_GET['item_id'], $_SESSION['itg_em_uid']));
                        }
                        else {
                            ?>
    <div class="ui-widget">
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
            <strong>Errrrr!</strong> Nice Try! Better luck next time ;)</p>
        </div>
    </div>
                            <?php
                        }
                    }
                    
                    /**
                     * For security reason we will allow only to change
                     * 1. The application status
                     * 2. The Notice to admin
                     * Team members can not be changed once enrolled
                     */
                    /** Enable WP TinyMCE */
                    wp_tiny_mce(false, array(
                        "editor_selector" => "my_tiny_mce"
                    ));
                    
                    ?>
                    <h3>Edit your Application Status</h3>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="id" value="<?php echo $ver_result->id; ?>" />
                        <ul>
                            <li>
                                <label for="user_apply">Open/Close your application:</label>
                                <select name="user_apply" id="user_apply">
                                    <option value="0"<?php if(1 == $ver_result->user_apply) echo ' selected="selected"'; ?>>Close the application</option>
                                    <option value="1"<?php if(0 == $ver_result->user_apply) echo ' selected="selected"'; ?>>Open the application</option>
                                </select>
                            </li>
                            <li>
                                <strong>Current Application Status</strong>: <?php echo ((1 == $ver_result->user_apply)? 'Open for enrollment' : 'Close and will not be processed'); ?>
                            </li>
                            <li>
                                <span class="description">Currently it is not possible to delete your application. You can only open or close it. That also only if you payment status is not approved till now. Else you have to contact the administrator for any modification</span>
                            </li>
                            <li>
                                <label for="note">Note to Admin: (Optional)</label>
                                <textarea name="note" id="note" class="my_tiny_mce">
                                    <?php echo esc_html($ver_result->note); ?>
                                </textarea>
                            </li>
                            <li>
                                <input type="submit" class="button-primary" value="Update Application" />
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
        <strong>Errrrr!</strong> Sorry Cowboy/Cowgirl (really??) Its not possible to poke here with your silly ideas! Uh! We cought your name. Calling police now. (dont worry! Swashata here. You just cant hack me like this  :P)</p>
    </div>
</div>
                    <?php
                }
            }
            //$wpdb->show_errors(); $wpdb->print_error();
            break;
    }
    ?>
</div>