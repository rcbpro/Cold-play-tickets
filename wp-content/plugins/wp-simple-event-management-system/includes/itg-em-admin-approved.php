<?php
if ('itg-em-admin-attende.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_admin')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}

/**
 * View and edit approved application
 * Unlike Attendee it will show only approved application
 * status=1 AND apply = 0
 * Will just list down
 * Editing and deleting will be done via attendee page only
 * Saves my time
 * may be in future I will make different module for this
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
    <h2>View Approved Application</h2>
    <div class="widefat">
        <div class="wrap">
        <h3>Filter Listing:</h3>
        <form action="" method="post">
            <input type="text" name="search_q" id="search_q" value="<?php echo (isset($_POST['search_op'])? $_POST['search_op'] : 'Enter Search Term...'); ?>" />
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
    <p><span class="description">The search result will display all approved and unapproved candidates</span></p>
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
                        <th scope="col" colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $attendee_search_results = ((0 == $_POST['search_op'])? itg_em_admin_attendee_search_uid($_POST['search_op'], 1, 0) : itg_em_admin_attendee_search_email($_POST['search_op'], 1, 0));
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
                            <td colspan="10">Howdy! No result. Please check your query.</td>
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
                            array_multisort($atd_price, SORT_DESC);
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
                            <td colspan="3"><?php echo $ult_price; ?></td>
                            <td colspan="3">
                                <a href="admin.php?page=itg_event_admin_attendee&pmode=mass_approve&item_id=<?php echo $mass_approve_uid; ?>">Approve All</a>
                            </td>
                            <?php
                        }
                        else {
                            ?>
                            <td colspan="10">Try refining your search term. We accept only user id or email.</td>
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
            $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $itg_em_db_table_name[reg_table] WHERE user_apply=%d AND pay_status=%d", 0, 1));
            if($pagination_count > 0) {
                $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
                $total_page = ceil($pagination_count/10);
                $p = new pagination;
                $p->Items($pagination_count);
                $p->limit(10);
                $p->target("admin.php?page=itg_event_admin_approved");
                $p->currentPage($this_page);
                $p->parameterName('p');
                /** Done with the pagination */
                
                /** Now get the entries */
                $list_start = ($this_page-1)*10;
                if($list_start >= $pagination_count) $list_start = ($pagination_count - 10);
                if($list_start < 0) $list_start = 0;
                
                $attendees = itg_em_admin_attendee_approved_list($list_start);
                if($attendees) {
                    ?>
                    <div class="tablenav">
                        <div class="tablenav-pages">
                            <?php $p->show(); ?>
                        </div>
                    </div>
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
                                    <td><?php echo $itg_em_options['currency']; ?> <?php echo $atd->price; ?></td>
                                    <td><?php echo $atd->reg_id; ?></td>
                                    <td><?php echo $payment_status[$atd->pay_status]; ?></td>
                                    <td>
                                        <a href="admin.php?page=itg_event_admin_attendee&pmode=delete&item_id=<?php echo $atd->id; ?>" class="button-secondary del_button">Delete</a>
                                        <br /><br />
                                    <a class="button-secondary thickbox" href="admin-ajax.php?action=itg_em_event_app_detail_ajax&item_id=<?php echo $atd->id; ?>&height=600&width=800">View/Print Application</a>
                                    </td>
                                    <td>
                                        <a href="admin.php?page=itg_event_admin_attendee&pmode=edit&item_id=<?php echo $atd->id; ?>" class="button-secondary">Edit</a>
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
                <div class="error fade">Sorry Admin! You have not approved yet! May be later.</div>
                <?php
            }
        }
        ?>
</div>