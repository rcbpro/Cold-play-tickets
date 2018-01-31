<?php
if ('itg-em-admin-edit-event.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_admin')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}

/**
 * Initialize the global variables
 */
global $wpdb, $itg_em_db_table_name, $itg_em_options;
/**
 * Handles the Event view and edit options
 * pmode = view | edit | delete
 */
?>
<div class="wrap">
    <h2>Manage Events <a href="admin.php?page=itg_event_admin_add" class="button-secondary">Add New</a></h2>
    <?php
    $pmode = (isset($_GET['pmode']))? $_GET['pmode'] : 'view';
    switch($pmode) {
        default:
        case 'view' :
            /** The tabular data */
            
            /** First the pagination */
            include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/pagination.class.php');
            $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $itg_em_db_table_name[admin_event]"));
            if($pagination_count > 0) {
                $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
                $total_page = ceil($pagination_count/10);
                $p = new pagination;
                $p->Items($pagination_count);
                $p->limit(10);
                $p->target("admin.php?page=itg_event_admin_edit&pmode=view");
                $p->currentPage($this_page);
                $p->parameterName('p');
                //$p->calculate();
                
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
                $events = itg_em_admin_list_events($list_start);
                
                if($events) {
                    ?>
                    <table class="widefat">
                        <caption>
                            A total of <?php echo $pagination_count; ?> Events. Showing Page <?php echo $this_page; ?>/<?php echo $total_page; ?>
                        </caption>
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Event Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Members</th>
                                <th scope="col">Round</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Event Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Members</th>
                                <th scope="col">Round</th>
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
                                        <?php
                                        if(-1.00 == $event->price)
                                            echo 'Announced Later';
                                        else if(0.00 == $event->price)
                                            echo 'Free Event';
                                        else
                                            echo $event->price;
                                        ?>
                                    </td>
                                    <td><?php echo $event->team_mem; ?></td>
                                    <td><?php echo $event->round; ?></td>
                                    <td>
                                        <a class="button-secondary" href="admin.php?page=itg_event_admin_edit&pmode=edit&item_id=<?php echo $event->id; ?>">Edit</a>
                                        <a class="button-secondary del_button" href="admin.php?page=itg_event_admin_edit&pmode=delete&item_id=<?php echo $event->id; ?>">Delete</a>
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
            }
            else {
                ?>
                <div class="error fade">No Records found</div>
                <?php
            }
            break;
        case 'edit':
            if(!isset($_GET['item_id'])) {
                ?>
                <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
                <?php
            }
            else {
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    /** Strip slash */
                    if ( get_magic_quotes_gpc() ) {
                        $_POST = array_map( 'stripslashes_deep', $_POST );
                    }
                    
                    /** Check for error! As usual */
                    $error = '';
                    if((int)$_POST['team_mem'] <= 0) {
                        $error .= '<p>Team member can not be 0. It has been updated to 1</p>';
                        $_POST['team_mem'] = 1;
                    }
                    if((int)$_POST['mem_op'] != 0 && (int)$_POST['mem_op'] != 1) {
                        $error .= '<p>Member Allocation data tampered. It has been set to Optional</p>';
                        $_POST['mem_op'] = 0;
                    }
                    if($_POST['round'] <= 0) {
                        $error .= '<p>Number of rounds can not be 0. It has been set to 1</p>';
                        $_POST['round'] = 1;
                    }
                    
                    /**
                     * Set the price
                     */
                    if('cus' == $_POST['price']) {
                        $_POST['price'] = (float) $_POST['price_cus'];
                    }
                    if($_POST['price'] < 0 && $_POST['price'] != -1.00) {
                        $_POST['price'] = -1*$_POST['price'];
                        $error .= '<p>The price cannot be negative. The positive value has been used.</p>';
                    }
                    
                    /**
                     * Set the date
                     */
                    $start_date_raw = strtotime($_POST['start_date']);
                    $start_date = date('Y-m-d', $start_date_raw);
                    
                    $end_date_raw = strtotime($_POST['end_date']);
                    $end_date = date('Y-m-d', $end_date_raw);
                    /** Validate */
                    if($end_date_raw < $start_date_raw) {
                        $end_date = $start_date;
                        $error .= '<p>End date is earlier than start date! This is humanly not possible. The developer being human, has decided to make end_date = start_date in such cases!</p>';
                    }
                    
                    /** Update the entry */
                    //$wpdb->update( $table, $data, $where, $format = null, $where_format = null )
                    $wpdb->update($itg_em_db_table_name['admin_event'], array(
                        'event_name' => $_POST['event_name'],
                        'event_desc' => $_POST['event_desc'],
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'team_mem' => $_POST['team_mem'],
                        'mem_op' => $_POST['mem_op'],
                        'round' => $_POST['round'],
                        'price' => $_POST['price'],
                        'per_disc' => $_POST['per_disc'],
                        'venue' => $_POST['venue'],
                        'ref' => $_POST['ref']
                    ), array('id' => $_POST['id']), array('%s', '%s', '%s', '%s', '%d', '%d', '%d', '%f', '%d', '%s', '%s'), array('%d'));
                    if($error != '') {
                        ?>
                        <div class="error fade">Following error occured. <?php echo $error; ?></div>
                        <?php
                    }
                    ?>
                    <div class="updated fade">Entry Successfully updated. Use the back button to go back to listing.</div>
                    <?php
                }
                $event_detail = itg_em_admin_event_detail($_GET['item_id']);
                if($event_detail) {
                    wp_tiny_mce(false, array(
                        "editor_selector" => "my_tiny_mce"
                    ));
                    ?>
                    <h3>Edit this event</h3>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="id" value="<?php echo $event_detail->id; ?>" />
                        <ul>
                            <li>
                                <label for="event_name">Event Name:</label>
                                <input type="text" name="event_name" id="event_name" value="<?php echo $event_detail->event_name;?>" />
                            </li>
                            <li>
                                <label for="event_desc">Event Description:</label>
                                <textarea name="event_desc" id="event_desc" class="my_tiny_mce"><?php echo esc_html($event_detail->event_desc);?></textarea>
                            </li>
                            <li>
                                <label for="team_mem">Team Members:</label>
                                <input type="text" name="team_mem" id="team_mem" value="<?php echo $event_detail->team_mem;?>" />
                            </li>
                            <li>
                                <label for="mem_op">Member Allocation:</label>
                                <select name="mem_op" id="mem_op">
                                    <option value="0"<?php if(0 == $event_detail->mem_op) echo ' selected="selected"'; ?>>Optional</option>
                                    <option value="1"<?php if(1 == $event_detail->mem_op) echo ' selected="selected"'; ?>>Mandatory</option>
                                </select>
                            </li>
                            <li>
                                <label for="round">Number of Rounds:</label>
                                <input type="text" name="round" id="round" value="<?php echo $event_detail->round;?>" />
                            </li>
                            <li>
                                <label for="price">Price of the Event:</label>
                                <select name="price" id="price">
                                    <option value="-1.00"<?php if(-1.00 == $event_detail->price) echo ' selected="selected"'; ?>>To be announced later</option>
                                    <option value="0.00"<?php if(0.00 == $event_detail->price) echo ' selected="selected"'; ?>>Free Event</option>
                                    <option value="cus"<?php if(-1.00 != $event_detail->price && 0.00 != $event_detail->price) echo ' selected="selected"'; ?>>Custom</option>
                                </select>
                                <label for="price_cus">Enter Custom Price:</label>
                                <input type="text" name="price_cus" id="price_cus" value="<?php if(-1.00 != $event_detail->price && 0.00 != $event_detail->price) echo $event_detail->price; ?>" /> <?php echo $itg_em_options['currency']; ?>
                            </li>
                            <li>
                                <label for="start_date">Start Date:</label>
                                <input type="text" name="start_date" id="start_date" class="date_field" value="<?php echo $event_detail->start_date; ?>" />
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <label for="end_date">End Date:</label>
                                <input type="text" name="end_date" id="end_date" class="date_field" value="<?php echo $event_detail->end_date; ?>" />
                            </li>
                            <li>
                                <label for="per_disc">Discount on bulk registration: </label>
                                <input type="text" name="per_disc" id="per_disc" value="<?php echo $event_detail->per_disc;?>" /> - percent
                            </li>
                            <li>
                                <label for="venue">Venue: </label>
                                <textarea id="venue" name="venue" class="my_tiny_mce"><?php echo esc_html($event_detail->venue);?></textarea>
                            </li>
                            <li>
                                <label for="ref">Reference URL:</label>
                                <input type="text" id="ref" name="ref" value="<?php echo $event_detail->ref;?>" />
                            </li>
                            <li>
                                <input type="submit" class="button-primary" value="Update the Event" />
                            </li>
                        </ul>
                    </form>
                    <a class="button-primary" href="admin.php?page=itg_event_admin_edit&pmode=view">Go BACK</a>
                    <?php
                }
            }
            break;
        case 'delete' :
            if(!isset($_GET['item_id'])) {
                ?>
                <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
                <?php
            }
            else {
                $sql = $wpdb->prepare("DELETE FROM $itg_em_db_table_name[admin_event] WHERE id=%d", $_GET['item_id']);
                
                if($wpdb->query($sql)) {
                    ?>
                    <div class="updated fade">Successfully deleted the entry</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Could not delete! Please check database.</div>
                    <?php
                }
                
                /** Now delete the registration of the event */
                if($wpdb->query($wpdb->prepare("DELETE FROM $itg_em_db_table_name[reg_table] WHERE event_id = %d", $_GET['item_id']))) {
                    ?>
                    <div class="updated fade">Successfully deleted the applications for this event</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">No Application found for this event. So we didn delete anything</div>
                    <?php
                }
            }
            ?>
            <a href="admin.php?page=itg_event_admin_edit&pmode=view" class="button-primary">Go Back</a>
            <?php
            break;
        
    }
    ?>
</div>