<?php
if ('itg-em-admin-user.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_admin')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}

/**
 * Control the user
 * Basically the user and its related data is deleted when you delete a user from wordpress
 * This just gives you a control to
 * 1. Reset His profile
 * 2. Reset Team Members
 * 3. Reset Registration
 * And thats all
 * Obviously this is done via a pmode switch case ;)
 */
global $itg_em_db_table_name, $wpdb;
?>
<div class="wrap">
    <h2>Manage Users:</h2>
    <p>
        <span class="description">
            Howdy! Remember when you delete a user from WordPress' system then the user along with all his data [Profile, Team Members, Registration] are deleted.
            <br />
            <strong>However you can just use this to reset the data for individual user without actually deleting them.</strong>
            <br />
            Note that, if you reset the profile then it wil be deleted from the db. When the user logs in then it will be created again.
        </span>
    </p>
<?php
$pmode = ((isset($_GET['pmode']))? $_GET['pmode'] : 'view');

switch($pmode) {
    default :
    case 'view' :
            /** The pagination */
            include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/pagination.class.php');
            $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $itg_em_db_table_name[user_table]"));
            if($pagination_count > 0) {
                $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
                $total_page = ceil($pagination_count/10);
                $p = new pagination;
                $p->Items($pagination_count);
                $p->limit(10);
                $p->target("admin.php?page=itg_event_admin_user&pmode=view");
                $p->currentPage($this_page);
                $p->parameterName('p');
                /** Done with the pagination */
                
                /** Now get the entries */
                $list_start = ($this_page-1)*10;
                if($list_start >= $pagination_count) $list_start = ($pagination_count - 10);
                if($list_start < 0) $list_start = 0;
                $user_list = itg_em_admin_user_list($list_start);
                ?>
                <div class="tablenav">
                    <div class="tablenav-pages">
                        <?php $p->show(); ?>
                    </div>
                </div>
                <table class="widefat">
                    <caption>Total <?php echo $pagination_count; ?> Users.</caption>
                    <thead>
                        <tr>
                            <th scope="col">UID:</th>
                            <th scope="col">WP UID:</th>
                            <th scope="col">Name:</th>
                            <th scope="col">WP Login:</th>
                            <th scope="col">Contact:</th>
                            <th scope="col">Team Members:</th>
                            <th scope="col" width="200">Action:</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th scope="col">UID:</th>
                            <th scope="col">WP UID:</th>
                            <th scope="col">Name:</th>
                            <th scope="col">WP Login:</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Team Members:</th>
                            <th scope="col" width="200">Action:</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        foreach($user_list as $user) {
                            ?>
                            <tr>
                                <td><?php echo $user->id; ?></td>
                                <td><?php echo $user->wp_uid; ?></td>
                                <td><?php echo $user->first_name . ' ' . $user->last_name; ?> a.k.a (nickname) <?php echo $user->nickname; ?></td>
                                <td><?php echo $user->user_login; ?></td>
                                <td>
                                    <ol>
                                        <li>
                                            <strong>Email:</strong> <?php echo $user->email; ?>
                                        </li>
                                        <li>
                                            <strong>Phone Number:</strong> <?php echo $user->ph_no; ?>
                                        </li>
                                        <li>
                                            <strong>Address:</strong> <?php echo $user->address; ?>
                                        </li>
                                        <li>
                                            <strong>State:</strong> <?php echo $user->state; ?>
                                        </li>
                                        <li>
                                            <strong>City:</strong> <?php echo $user->city; ?>
                                        </li>
                                        <li>
                                            <strong>Country:</strong> <?php echo $user->country; ?>
                                        </li>
                                    </ol>
                                </td>
                                <td>
                                    <a class="button-secondary thickbox" href="admin-ajax.php?action=itg_em_ajax_admin_user_team&uid=<?php echo $user->id; ?>&height=600&width=800">View Team Members</a>
                                    <br /><br />
                                    <a class="button-primary thickbox" href="admin-ajax.php?action=itg_em_ajax_admin_user_info&uid=<?php echo $user->id; ?>&height=600&width=800">View Profile</a>
                                </td>
                                <td>
                                    <a href="admin.php?page=itg_event_admin_user&pmode=reset_prof&uid=<?php echo $user->id; ?>" class="reset_but button-secondary">Reset Profile</a>
                                    &nbsp;
                                    <a href="admin.php?page=itg_event_admin_user&pmode=reset_team&uid=<?php echo $user->id; ?>" class="reset_but button-secondary">Reset Team</a>
                                    <br /><br />
                                    <a href="admin.php?page=itg_event_admin_user&pmode=reset_app&uid=<?php echo $user->id; ?>" class="reset_but button-secondary">Reset Applications</a>
                                    <br /><br />
                                    <a href="admin.php?page=itg_event_admin_user&pmode=reset_all&uid=<?php echo $user->id; ?>" class="reset_but button-primary">Reset ALL</a>
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
                <div class="error fade">Sorry Admin! No user data yet.</div>
                <?php
            }
            break;
        case 'reset_all' :
            if(!$_GET['uid']) {
                ?>
                <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
                <?php
            }
            else {
                /** Delete Profile */
                if($wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['user_table']} WHERE id=%d", $_GET['uid']))) {
                    ?>
                    <div class="updated fade">Deleted User Profile</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Could not delete User Profile. DB error. Contact Developer.</div>
                    <?php
                }
                
                /** Delete Team member */
                if($wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d", $_GET['uid']))) {
                    ?>
                    <div class="updated fade">Deleted User Team members</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Could not delete User Team. <strong>The user does not have any team member</strong>.</div>
                    <?php
                }
                
                /** Delete Registration */
                if($wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['reg_table']} WHERE uid = %d", $_GET['uid']))) {
                    ?>
                     <div class="updated fade">Deleted User Registrations.</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Could not delete User Registration. <strong>The user does not have any active/cancelled registration</strong>.</div>
                    <?php
                }
            }
            ?>
            <a href="admin.php?page=itg_event_admin_user&pmode=view" class="button-primary">Go Back</a>
            <?php
            break;
        case 'reset_prof' :
            if(!$_GET['uid']) {
                ?>
                <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
                <?php
            }
            else {
                /** Delete Profile */
                if($wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['user_table']} WHERE id=%d", $_GET['uid']))) {
                    ?>
                    <div class="updated fade">Deleted User Profile</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Could not delete User Profile. DB error. Contact Developer.</div>
                    <?php
                }
            }
            ?>
            <a href="admin.php?page=itg_event_admin_user&pmode=view" class="button-primary">Go Back</a>
            <?php
            break;
        case 'reset_team' :
            if(!$_GET['uid']) {
                ?>
                <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
                <?php
            }
            else {
                
                /** Delete Team member */
                if($wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d", $_GET['uid']))) {
                    ?>
                    <div class="updated fade">Deleted User Team members</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Could not delete User Team. <strong>The user does not have any team member</strong>.</div>
                    <?php
                }
            }
            ?>
            <a href="admin.php?page=itg_event_admin_user&pmode=view" class="button-primary">Go Back</a>
            <?php
            break;
        case 'reset_app' :
            if(!$_GET['uid']) {
                ?>
                <div class="error fade">Sorry! No item specified. Damn! You suck... Supposed to be an admin! :-/</div>
                <?php
            }
            else {
                /** Delete Registration */
                if($wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['reg_table']} WHERE uid = %d", $_GET['uid']))) {
                    ?>
                     <div class="updated fade">Deleted User Registrations.</div>
                    <?php
                }
                else {
                    ?>
                    <div class="error fade">Could not delete User Registration. <strong>The user does not have any active/cancelled registration</strong>.</div>
                    <?php
                }
            }
            ?>
            <a href="admin.php?page=itg_event_admin_user&pmode=view" class="button-primary">Go Back</a>
            <?php
            break;
}
?>
</div>