<?php
if ('itg-em-user-event-apply.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_subs')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}
/**
 * Handles the event application
 * User can apply for events whose end date is <= todays date
 * Checks team member
 * If required then verifies if that amount of members is available on users team
 * if not, then returns error.
 * Else verifies using both JS and PHP
 * For optional does nothing but generating a notice
 * Two pmodes
 * case 'view' Browse through the event lists
 * case 'apply' Apply for a specific event
 */
/**
 * Some globals
 */
global $wpdb, $itg_em_db_table_name, $itg_em_options;
?>
<div class="wrap">
    <h2>Apply For an upcoming event</h2>
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
        case 'view' :
            /** The tabular data */
            
            /** First the pagination */
            include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/pagination.class.php');
            $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $itg_em_db_table_name[admin_event] WHERE end_date >= %d", date('Y-m-d')));
            if($pagination_count > 0) {
                $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
                $total_page = ceil($pagination_count/10);
                $p = new pagination;
                $p->Items($pagination_count);
                $p->limit(10);
                $p->target("admin.php?page=itg_em_user_apply_event_page&pmode=view");
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
                $events = itg_em_user_event_list($list_start);
                
                if($events) {
                    /** Shortcuts please */
                    $mem_details = array(
                        0 => '(Optional)',
                        1 => '(Required)'
                    );
                    ?>
                    <table class="widefat">
                        <caption>Total Events: <?php echo $pagination_count; ?> - Showing page <?php echo $this_page . ' / ' . $total_page; ?></caption>
                        <thead>
                            <tr>
                                <th scope="col">Event ID</th>
                                <th scope="col">Event Name</th>
                                <th scope="col">Date</th>
                                <th scope="col" colspan="2">Members</th>
                                <th scope="col">Rounds</th>
                                <th scope="col">Price</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col">Event ID</th>
                                <th scope="col">Event Name</th>
                                <th scope="col">Date</th>
                                <th scope="col" colspan="2">Members</th>
                                <th scope="col">Rounds</th>
                                <th scope="col">Price</th>
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
                                    <td><?php echo $event->team_mem; ?> Total</td>
                                    <td><?php echo $mem_details[$event->mem_op]; ?></td>
                                    <td><?php echo $event->round; ?></td>
                                    <td>
                                        <?php 
                                        if($event->price == -1.00)
                                            echo '(To be announced later)';
                                        elseif($event->price == 0.00)
                                            echo 'Free Event';
                                        else
                                            echo $itg_em_options['currency'] . ' ' . $event->price;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($event->app_id) {
                                            ?>
                                            <a class="button-secondary thickbox" href="admin-ajax.php?action=itg_em_event_app_detail_ajax&item_id=<?php echo $event->app_id; ?>&height=600&width=800">View Application Status</a>
                                            <?php
                                        }
                                        else {
                                            ?>
                                            <a class="button-secondary" href="admin.php?page=itg_em_user_apply_event_page&pmode=apply&item_id=<?php echo $event->id; ?>">Apply for the event</a>
                                            <?php
                                        }
                                        ?>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="admin-ajax.php?action=itg_em_event_detail_ajax&item_id=<?php echo $event->id; ?>&height=400&width=800" class="thickbox button-secondary">View Details</a>
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
        <strong>Sorry:</strong> No Event available at this time.</p>
    </div>
</div>
                <?php
            }
            break;
        case 'apply' :
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
                $show_form = true;
                /** Get the event detail */
                $ev_detail = itg_em_admin_event_detail($_GET['item_id']);
                /** If user can really apply for the event */
                $ev_verification = itg_em_user_event_verification($_GET['item_id']);
                
                /** Get user members */
                $user_mem = itg_em_user_cur_mem();
                $user_mem_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d", $_SESSION['itg_em_uid']));
                //echo $user_mem_count . '<br />' . ($ev_detail->team_mem - 1);
                $user_mem_ids = itg_em_user_cur_mem_ids();
                $req_mem = $ev_detail->team_mem - 1;
                /** Just check for the members */
                if(($ev_detail->mem_op == 1) && ($user_mem_count < $req_mem)) {
                    ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error!</strong> This Event requires <?php printf(_n('%d additional member', '%d additional members', ($ev_detail->team_mem-1)), ($ev_detail->team_mem-1)); ?> which you dont have in your team. Please add some more team members then come back here. <a href="admin.php?page=itg_em_user_add_team_page" class="button-primary">Add Team Member</a></p>
    </div>
</div>
                    <?php
                }
                else {
                    if($ev_detail && $ev_verification) {
                        /** Save on post request */
                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $post_validation = true;
                            $error = '';
                            /** Check for team members authenticity */
                            if(is_array($_POST['user_team'])) {
                                foreach($_POST['user_team'] as $team) {
                                    if(!in_array($team, $user_mem_ids)) {
                                        $post_validation = false;
                                        $error .= 'Cheating? It is not possible to cheat Swashata so easily ;) Better try with your user id next time!<br />';
                                        break;
                                    }
                                }
                            }
                            /** Number of team member */
                            if($ev_detail->mem_op == 1 && count($_POST['user_team']) != $req_mem) {
                                $post_validation = false;
                                $error .= 'Please select the correct number of Team members.';
                            }
                            elseif(count($_POST['user_team']) > $ev_detail->team_mem) {
                                $post_validation = false;
                                $error .= 'You have selected More than the available team member slot. Please select less or equal';
                            }
                            
                            /** If error then print it */
                            if(!$post_validation) {
                                ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error!</strong> <?php echo $error; ?></p>
    </div>
</div>
                                <?php
                            }
                            else {
                                /**
                                 * Else insert the application
                                 * Also mail the system administrator
                                 */
                                if(is_array($_POST['user_team']))
                                    $team_ids = implode(',', $_POST['user_team']);
                                else
                                    $team_ids = '';
                                $reg_id = $_SESSION['itg_em_uid'] . '-' . wp_generate_password(8);
                                $note = wp_kses_data($_POST['note']);
                                $date = date('Y-m-d');
                                $insert = array(
                                    'team_ids' => $team_ids,
                                    'event_id' => $_GET['item_id'],
                                    'uid' => $_SESSION['itg_em_uid'],
                                    'reg_id' => $reg_id,
                                    'note' => $note,
                                    'date' => $date
                                );
                                $insert_dt = array('%s', '%d', '%d', '%s', '%s', '%s');
                                if($wpdb->insert($itg_em_db_table_name['reg_table'], $insert, $insert_dt)) {
                                    ?>
<style type="text/css">
    .validate_form {
        display: none;
    }
</style>
<div class="ui-widget">
        <div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding: 0 .7em;"> 
                <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                <strong>Done!</strong> Your Application has been enrolled. <a href="admin.php?page=itg_em_user_app_status_page&pmode=edit&item_id=<?php echo $wpdb->insert_id; ?>" class="button-secondary">View Status</a> or <a href="admin.php?page=itg_em_user_apply_event_page&pmode=view" class="button-secondary">Apply for other Events</a></p>
        </div>
</div>
                                    <?php
                                    /** Send an email to the Administrator */
                                    global $current_user;
                                    if(!$current_user) {
                                        get_currentuserinfo();
                                    }
                                    $headers = 'From: ' . $current_user->user_firstname . ' ' . $current_user->user_lastname . ' <' . $current_user->user_email . '>' . "\r\n\\";
                                    $subject = '[WP EM] A user has applied for an event - ' . get_bloginfo('name');
                                    $admin_url = admin_url();
                                    $message = <<<EOD
Hi Admin. We have an attendee for an Event. Please use the link below to view the details.
------------------------------------------------------------------------------------------
{$admin_url}admin.php?page=itg_event_admin_attendee&pmode=edit&item_id=$wpdb->insert_id
------------------------------------------------------------------------------------------
You can simply reply to this email to notify the user.
EOD;
                                    
                                    wp_mail($itg_em_options['contact_email'], $subject, $message, $headers);
                                    
                                }
                                else {
                                    ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Error!</strong> Something really bad happened. Please contact Administrator.</p>
    </div>
</div>
                                    <?php
                                }
                            }
                        }
                        /** Shortcuts please */
                        $mem_details = array(
                            0 => '(Optional)',
                            1 => '(Required)'
                        );  
                        /** Echo the event detail */
                        ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2">Details about the event: <?php echo $ev_detail->event_name; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Event Name:</th>
                                    <td><?php echo $ev_detail->event_name; ?></td>
                                </tr>
                                <tr>
                                    <th>Event Description:</th>
                                    <td>
                                        <div class="ui-widget">
                                                <div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding:.7em;"> 
                                                        <?php echo $ev_detail->event_desc; ?>
                                                </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Venue:</th>
                                    <td>
                                        <div class="ui-widget">
                                                <div class="ui-state-highlight ui-corner-all" style="margin: 20px 0; padding:.7em;"> 
                                                        <?php echo $ev_detail->venue; ?>
                                                </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Team Members:</td>
                                    <td>
                                        <?php echo $ev_detail->team_mem; ?> <?php echo $mem_details[$ev_detail->mem_op]; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Rounds:</td>
                                    <td><?php echo $ev_detail->round; ?></td>
                                </tr>
                                <tr>
                                    <td>Price</td>
                                    <td>
                                        <?php
                                        if(-1.00 == $ev_detail->price)
                                            echo 'Announced Later';
                                        else if(0.00 == $ev_detail->price)
                                            echo 'Free Event';
                                        else
                                            echo $ev_detail->price;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reference:</td>
                                    <td>
                                        <?php
                                        if($ev_detail->ref != '') {
                                            echo '<a href="' . $ev_detail->ref . '" class="button-secondary" target="_blank">Read More</a>';
                                        }
                                        else {
                                            echo 'Not available';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>    
                        <?php
                        wp_tiny_mce(false, array(
                            "editor_selector" => "my_tiny_mce"
                        ));
                        /** Now bring the form */
                        ?>
                        <h3>Apply for it</h3>
                        <form class="validate_form" method="post" action="">
                            <ul>
                                <li>Select Team members:</li>
                                <li>
                                    <?php
                                    /** Built in the Form verification rules */
                                    if(0 == $ev_detail->mem_op) {
                                        $min_check = 0;
                                    }
                                    else {
                                        $min_check = $ev_detail->team_mem-1;
                                    }
                                    $max_check = $ev_detail->team_mem - 1;
                                    /** List down the member list */
                                    foreach($user_mem as $user) {
                                        ?>
                                        <input type="checkbox" class="validate[minCheckbox[<?php echo $min_check; ?>],maxCheckbox[<?php echo $max_check; ?>]]" name="user_team[]" id="user_team_<?php echo $user->id; ?>" value="<?php echo $user->id; ?>" /> <label for="user_team_<?php echo $user->id; ?>"><?php echo $user->first_name . ' ' . $user->last_name; ?></label><br />
                                        <?php
                                    }
                                    ?>
                                </li>
                                <li>
                                    Add a note to the Administrator (<em>Optional</em>)
                                </li>
                                <li>
                                    <textarea name="note" id="note" class="my_tiny_mce"></textarea>
                                </li>
                                <li>
                                    <input type="submit" class="button-primary" value="Apply for the event" />
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
        <strong>Error!</strong> Either you are not authorized or you have already opted for this event.</p>
    </div>
</div>
                        <?php
                    }
                }
            }
            break;
    }
    ?>
</div>