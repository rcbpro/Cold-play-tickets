<?php
if ('itg-em-admin-attende.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_admin')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}

/**
 * List downs the attendee
 * Provides Search feature as well
 */
/**
 * Also done via pmode
 * $pmode = view | edit | delete | accept | mass_approve
 * The search is integreted with the view.
 * We will show the result inside the table
 * For normal, list down the table with pagination
 */

/**
 * Some globals
 */
global $wpdb, $itg_em_db_table_name, $itg_em_options;
/**
 * Some shortcuts
 */
$payment_status = array(
    0 => 'Unpaid',
    1 => 'Paid'
);
$app_status = array(
    0 => 'Close',
    1 => 'Open'
);
?>
<div class="wrap">
    <h2>View or Edit Attendee</h2>
<?php
$pmode = (isset($_GET['pmode']))? $_GET['pmode'] : 'view';
switch($pmode) {
    default :
    case 'view' :
?>
    <div class="widefat">
        <div class="wrap">
        <h3>Filter Listing:</h3>
        <form action="" method="post">
            <input type="text" name="search_q" id="search_q" value="<?php echo (isset($_POST['search_q'])? $_POST['search_q'] : 'Enter Search Term...'); ?>" />
            <br />
            <span class="description">Search for:  </span>
            <label for="search_op_email">User Email:</label>
            <input type="radio" name="search_op" id="search_op_email" value="0" checked="checked" />
            <label for="search_op_uid">User ID:</label>
            <input type="radio" name="search_op" id="search_op_uid" value="1" />
            <input type="submit" name="search_subm" value="Search" class="button-primary" />
        </form>
        </div>
    </div>
    <p><span class="description">Hittin the approve button will mark the application as paid. You can undo by going to the Edit portion</span></p>
        <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            /** Strip slash */
            if ( get_magic_quotes_gpc() ) {
                $_POST = array_map( 'stripslashes_deep', $_POST );
            }
            ?>
            <h3>Showing Search Result:</h3>
            <table class="widefat">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col" colspan="2">(UID) User</th>
                        <th scope="col">Event</th>
                        <th scope="col">Apply Date</th>
                        <th scope="col">Price</th>
                        <th scope="col">Reg. No.</th>
                        <th scope="col">Payment</th>
                        <th scope="col" colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $attendee_search_results = ((1 == $_POST['search_op'])? itg_em_admin_attendee_search_uid($_POST['search_q']) : itg_em_admin_attendee_search_email($_POST['search_q']));
                    if($attendee_search_results) {
                        $atd_price = array();
                        $atd_per_disc = array();
                        $mass_approve_uid = false;
                        foreach($attendee_search_results as $atd) {
                            if(!$mass_approve_uid)
                                $mass_approve_uid = $atd->uid;
                            ?>
                            <tr>
                                <th scope="col"><?php echo $atd->id; ?></th>
                                <td><?php echo $atd->uid; ?></td>
                                <td><?php echo $atd->name; ?><br /><br /><a href="admin-ajax.php?action=itg_em_ajax_admin_user_info&uid=<?php echo $atd->uid; ?>&height=600&width=800" class="thickbox button-secondary">View Profile</a></td>
                                <td><?php echo $atd->event_name; ?></td>
                                <td><?php echo $atd->date; ?></td>
                                <td><?php echo $itg_em_options['currency']; ?> <?php echo $atd->price; ?></td>
                                <td><?php echo $atd->reg_id; ?></td>
                                <td><?php echo $payment_status[$atd->pay_status]; ?></td>
                                <td>
                                    <a href="admin.php?page=itg_event_admin_attendee&pmode=accept&item_id=<?php echo $atd->id; ?>" class="button-secondary">Accept</a>
                                </td>
                                <td>
                                    <a href="admin.php?page=itg_event_admin_attendee&pmode=edit&item_id=<?php echo $atd->id; ?>" class="button-secondary">Edit</a>
                                    <br /><br />
                                    <a class="button-secondary thickbox" href="admin-ajax.php?action=itg_em_event_app_detail_ajax&item_id=<?php echo $atd->id; ?>&height=600&width=800">View/Print Application</a>
                                </td>
                                <td>
                                    <a href="admin.php?page=itg_event_admin_attendee&pmode=delete&item_id=<?php echo $atd->id; ?>" class="button-secondary del_button">Delete</a>
                                </td>
                            </tr>
                            <?php
                            /** Add the price and discount thing */
                            $atd_price[$atd->id] = $atd->price;
                            $atd_per_disc[$atd->id] = $atd->per_disc;
                        }
                    }
                    else {
                        ?>
                        <tr>
                            <td colspan="11">Howdy! No result. Please check your query.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <?php
                        if($attendee_search_results) {
                            /** Sort the array for the pricing */
                            arsort($atd_price);
                            //$atd_price = array_reverse($atd_price);
                            $total_price = array();
                            $flag = true;
                            $possibility = true;
                            foreach($atd_price as $key => $val) {
                                if($flag && $val != -1.00) {
                                    $flag = false;
                                    $total_price[] = $val;
                                }
                                else if ($val != -1.00) {
                                    $total_price[] = $val - (($atd_per_disc[$key]/100)*$val);
                                }
                                else {
                                    $possibility = false;
                                    break;
                                }
                            }
                            if($possibility) {
                                $ult_price = array_sum($total_price);
                            }
                            else {
                                $ult_price = 'Sorry! Some events dont have price set.';
                            }
                            ?>
                            <th scope="col" colspan="4">Total Price</th>
                            <td colspan="4"><?php echo $ult_price; ?></td>
                            <td colspan="3">
                                <a class="button-primary" href="admin.php?page=itg_event_admin_attendee&pmode=mass_approve&item_id=<?php echo $mass_approve_uid; ?>">Approve All</a>
                            </td>
                            <?php
                        }
                        else {
                            ?>
                            <td colspan="11">Try refining your search term. We accept only user id or email.</td>
                            <?php
                        }
                        ?>
                    </tr>
                </tfoot>
            </table>
            <?php
        }
        else {
            /**
             * The pagination
             */
            include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/pagination.class.php');
            $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $itg_em_db_table_name[reg_table] WHERE user_apply=%d", 1));
            if($pagination_count > 0) {
                $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
                $total_page = ceil($pagination_count/10);
                $p = new pagination;
                $p->Items($pagination_count);
                $p->limit(10);
                $p->target("admin.php?page=itg_event_admin_attendee&pmode=view");
                $p->currentPage($this_page);
                $p->parameterName('p');
                /** Done with the pagination */
                
                /** Now get the entries */
                $list_start = ($this_page-1)*10;
                if($list_start >= $pagination_count) $list_start = ($pagination_count - 10);
                if($list_start < 0) $list_start = 0;
                
                $attendees = itg_em_admin_attendee_list($list_start);
                if($attendees) {
                    ?>
                    <div class="tablenav">
                        <div class="tablenav-pages">
                            <?php $p->show(); ?>
                        </div>
                    </div>
                    <table class="widefat">
                        <caption>Total Attendees: <?php echo $pagination_count; ?> - Showing page <?php echo $this_page . ' / ' . $total_page; ?></caption>
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col" colspan="2">(UID) User</th>
                                <th scope="col">Event</th>
                                <th scope="col">Apply Date</th>
                                <th scope="col">Price</th>
                                <th scope="col">Reg. No.</th>
                                <th scope="col">Payment</th>
                                <th scope="col" colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col" colspan="2">(UID) User</th>
                                <th scope="col">Event</th>
                                <th scope="col">Apply Date</th>
                                <th scope="col">Price</th>
                                <th scope="col">Reg. No.</th>
                                <th scope="col">Payment</th>
                                <th scope="col" colspan="2">Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            foreach($attendees as $atd) {
                                ?>
                                <tr>
                                    <th scope="col"><?php echo $atd->id; ?></th>
                                    <td><?php echo $atd->uid; ?></td>
                                    <td><?php echo $atd->name; ?><br /><br /><a href="admin-ajax.php?action=itg_em_ajax_admin_user_info&uid=<?php echo $atd->uid; ?>&height=600&width=800" class="thickbox button-secondary">View Profile</a></td>
                                    <td><?php echo $atd->event_name; ?></td>
                                    <td><?php echo $atd->date; ?></td>
                                    <td>
                                        <?php 
                                        if($atd->price == -1.00)
                                            echo '(To be announced later)';
                                        elseif($atd->price == 0.00)
                                            echo 'Free Event';
                                        else
                                            echo $itg_em_options['currency'] . ' ' . $atd->price;
                                        ?>
                                    </td>
                                    <td><?php echo $atd->reg_id; ?></td>
                                    <td><?php echo $payment_status[$atd->pay_status]; ?></td>
                                    <td>
                                        <a href="admin.php?page=itg_event_admin_attendee&pmode=accept&item_id=<?php echo $atd->id; ?>" class="button-secondary">Accept</a>
                                        <br /><br />
                                    <a class="button-secondary thickbox" href="admin-ajax.php?action=itg_em_event_app_detail_ajax&item_id=<?php echo $atd->id; ?>&height=600&width=800">View/Print Application</a>
                                    </td>
                                    <td>
                                        <a href="admin.php?page=itg_event_admin_attendee&pmode=edit&item_id=<?php echo $atd->id; ?>" class="button-secondary">Edit</a>
                                        <br /><br />
                                        <a href="admin.php?page=itg_event_admin_attendee&pmode=delete&item_id=<?php echo $atd->id; ?>" class="button-secondary del_button">Delete</a>
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
                    <div class="error fade">Some error occured! It found some data on the database but could not retrieve. Please contact the developer with the content of the table <?php echo $itg_em_db_table_name['reg_table']; ?></div>
                    <?php
                }
            }
            else {
                ?>
                <div class="error fade">Sorry Admin! No one has registered yet! May be later.</div>
                <?php
            }
        }
        break;  
    case 'edit' :
        if(!$_GET['item_id']) {
            ?>
            <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
            <?php
        }
        else {
            /**
             * We have item id
             * Now get its detail
             * Edible contents are
             * Reg id
             * status
             * apply
             * note
             */
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                /** Strip slash */
                if ( get_magic_quotes_gpc() ) {
                    $_POST = array_map( 'stripslashes_deep', $_POST );
                }
                
                /**
                 * First check for any error
                 */
                $error = '';
                if($_POST['pay_status'] != 0 && $_POST['pay_status'] != 1) {
                    $_POST['pay_status'] = 0;
                    $error .= '<p>The status data tampered. Restored to Unpaid</p>';
                }
                
                if($_POST['user_apply'] != 0 && $_POST['user_apply'] != 1) {
                    $_POST['user_apply'] = 1;
                    $error .= '<p>Application data tampered. Restored to Open</p>';
                }
                /**
                 * If the application is marked as paid
                 * then close that as well
                 */
                if($_POST['pay_status'] == 1) {
                    $_POST['user_apply'] = '0';
                    $error .= '<p>As you have make the application paid, the status was automatically closed</p>';
                }
                $update_array = array(
                    'pay_status' => $_POST['pay_status'],
                    'user_apply' => $_POST['user_apply']
                );
                $update_dt_array = array('%d', '%d');
                
                /**
                 * Check if the note is set to clear
                 */
                if($_POST['note']) {
                    $update_array['note'] = '';
                    $update_dt_array[] = '%s';
                }
                
                /**
                 * Now update the database
                 */
                if($wpdb->update($itg_em_db_table_name['reg_table'], $update_array, array('id' => $_POST['id']), $update_dt_array, array('%d'))) {
                    ?>
                    <div class="updated fade">Successfully Updated the application.</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Something went wrong and could not update the database</div>
                    <?php
                }
                
                if('' != $error) {
                    ?>
                    <div class="error fade">The following errors occured. <?php echo $error; ?></div>
                    <?php
                }
            }
            $reg_detail = itg_em_admin_attendee_single_detail($_GET['item_id']);
            if($reg_detail) {
                ?>
                <h3>Showing detail of application <?php echo $reg_detail->id; ?></h3>
                <form action="" method="post">
                    <input type="hidden" name="id" id="id" value="<?php echo $_GET['item_id']; ?>" />
                    <ul>
                        <li>
                            <strong>Name: </strong> <?php echo $reg_detail->name; ?>
                            <br />
                            <strong>User ID: </strong> <?php echo $reg_detail->uid; ?>
                            <br />
                            <strong>Application Date: </strong> <?php echo date('l jS \of F Y', strtotime($reg_detail->date)); ?>
                        </li>
                        <li>
                            <strong>Event Name: </strong> <?php echo $reg_detail->event_name; ?><br />
                            <strong>Event ID: </strong> <?php echo $reg_detail->event_id; ?>
                        </li>
                        <li>
                            <strong>Registration Number: </strong> <?php echo $reg_detail->reg_id; ?>
                            <br />
                            <div class="itg-em-msgbox">
                                <h4>User Message</h4>
                                <div class="wrap">
                                    <?php echo $reg_detail->note; ?>
                                </div>
                            </div>
                            <br />
                            <p>
                                <label for="note">Clear User Note: </label>
                                <input type="checkbox" name="note" id="note" value="1" />
                            </p>
                        </li>
                        <li>
                            <label for="pay_status">Payment Status:</label>
                            <select name="pay_status" id="pay_status">
                                <option value="0"<?php if($reg_detail->pay_status == 0) echo ' selected="selected"'; ?>>Unpaid</option>
                                <option value="1"<?php if($reg_detail->pay_status == 1) echo ' selected="selected"'; ?>>Paid</option>
                            </select>
                        </li>
                        <li>
                            <label for="user_apply">Application Status:</label>
                            <select name="user_apply" id="user_apply">
                                <option value="0"<?php if($reg_detail->user_apply == 0) echo ' selected="selected"'; ?>>Close</option>
                                <option value="1"<?php if($reg_detail->user_apply == 1) echo ' selected="selected"'; ?>>Open</option>
                            </select>
                        </li>
                        <li>
                            <input type="submit" class="button-primary" value="Update" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="admin.php?page=itg_event_admin_attendee&pmode=delete&item_id=<?php echo $reg_detail->id; ?>" class="button-secondary">Delete</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="admin.php?page=itg_event_admin_attendee" class="button-secondary">Go Back</a>
                        </li>
                    </ul>
                </form>
                <?php
            }
            else {
                ?>
                <div class="error fade">The entry could not be found! R u hacking the url?</div>
                <?php
            }
        }
        break;
    case 'delete' :
        if(!$_GET['item_id']) {
            ?>
            <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
            <?php
        }
        else {
            $sql = $wpdb->prepare("DELETE FROM $itg_em_db_table_name[reg_table] WHERE id=%d", $_GET['item_id']);
                
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
        }
        ?>
        <a href="admin.php?page=itg_event_admin_attendee" class="button-primary">Go Back</a>
        <?php
        break;
    case 'accept' :
        if(!$_GET['item_id']) {
            ?>
            <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
            <?php
        }
        else {
            $update_array = array(
                'pay_status' => 1,
                'user_apply' => 0
            );
                
            if($wpdb->update($itg_em_db_table_name['reg_table'], $update_array, array('id' => $_GET['item_id']), '%d', '%d')) {
                ?>
                <div class="updated fade">Successfully Approved the entry. It has been moved to the Approved Application now.</div>
                <?php
            }
            else {
                ?>
                <div class="error fade">Could not update! Please check database.</div>
                <?php
            }
        }
        ?>
        <a href="admin.php?page=itg_event_admin_attendee" class="button-primary">Go Back</a>
        <?php
        break;
    case 'mass_approve' :
        if(!$_GET['item_id']) {
            ?>
            <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
            <?php
        }
        else {
            $update_array = array(
                'pay_status' => 1,
                'user_apply' => 0
            );
                
            if($af_rows = $wpdb->update($itg_em_db_table_name['reg_table'], $update_array, array('uid' => $_GET['item_id']), '%d', '%d')) {
                ?>
                <div class="updated fade">Successfully Approved a total of <?php echo $af_rows; ?> Entries. It has been moved to the Approved Application now.</div>
                <?php
            }
            else {
                ?>
                <div class="error fade">Could not update! Please check database.</div>
                <?php
            }
        }
        ?>
        <a href="admin.php?page=itg_event_admin_attendee" class="button-primary">Go Back</a>
        <?php
        break;
}
    ?>
</div>