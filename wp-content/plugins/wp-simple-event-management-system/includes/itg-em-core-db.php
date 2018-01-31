<?php
if ('itg-em-core-db.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');

/**
 * This file contains all the database interaction function of itg_em
 * We use $wbdb->get_row to properly use the variable
 * $wbdb->get_row, $wbdb->get_results
 * Also it caches the data for future use
 */

/**
 * Admin List Events
 */
function itg_em_admin_list_events($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    $event_list = $wpdb->get_results("SELECT * FROM {$itg_em_db_table_name['admin_event']} LIMIT $start,10");
    return $event_list;
}

/**
 * Specific Event detail
 */
function itg_em_admin_event_detail($id) {
    global $wpdb, $itg_em_db_table_name;
    
    $event_detail = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['admin_event']} WHERE id=%d", $id));
    return $event_detail;
}

/**
 * General Attendee List
 * user_apply = 1 pay_status = 0|1
 * Just in case admin does something buggy directly from the db
 * as when pay_status == 1 user_apply has to be == 0 ;)
 */
function itg_em_admin_attendee_list($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    $attendee_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE user_apply=%d LIMIT %d, 10", 1, $start));
    
    if($attendee_list) {
        foreach($attendee_list as $atd) {
            $atd_info = $wpdb->get_var($wpdb->prepare("SELECT wp_uid FROM {$itg_em_db_table_name['user_table']} WHERE id=%d",$atd->uid));
            $atd_info = get_userdata($atd_info);
            $atd->email = $atd_info->user_email;
            $atd->name = $atd_info->first_name . ' ' . $atd_info->last_name;
            
            $atd_event = $wpdb->get_row($wpdb->prepare("SELECT event_name, price FROM {$itg_em_db_table_name['admin_event']} WHERE id=%d", $atd->event_id));
            $atd->event_name = $atd_event->event_name;
            $atd->price = $atd_event->price;
        }
    }
    return $attendee_list;
}
/**
 * Get Complete user data by uid
 * @param int $id The id of the table user_table
 * @global $wpdb
 */
function itg_em_admin_user_info($id) {
    global $wpdb, $itg_em_db_table_name;
    /** First get the wp_uid and other information */
    $user_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_table']} WHERE id = %d", $id));
    if($user_info) {
        /** Get the information from wp_users */
        $wp_user_info = get_userdata($user_info->wp_uid);
        
        $user_info->first_name = $wp_user_info->first_name;
        $user_info->last_name = $wp_user_info->last_name;
        $user_info->email = $wp_user_info->user_email;
        $user_info->nickname = $wp_user_info->nickname;
        $user_info->user_nicename = $wp_user_info->user_nicename;
        $user_info->user_login = $wp_user_info->user_login;
    }
    return $user_info;
}
/**
 * Approved Attendee List
 * pay_status = 1 user_apply = 0
 */
function itg_em_admin_attendee_approved_list($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    $attendee_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE pay_status=%d AND user_apply=%d LIMIT %d, 10", 1, 0, $start));
    
    if($attendee_list) {
        foreach($attendee_list as $atd) {
            $atd_info = itg_em_admin_user_info($atd->uid);
            $atd->email = $atd_info->email;
            $atd->name = $atd_info->first_name . ' ' . $atd_info->last_name;
            $atd->ph_no = $atd_info->ph_no;
            
            $atd_event = $wpdb->get_row("SELECT event_name, price FROM {$itg_em_db_table_name['admin_event']} WHERE id=$atd->event_id");
            $atd->event_name = $atd_event->event_name;
            $atd->price = $atd_event->price;
        }
    }
    return $attendee_list;
}

/**
 * Cancelled Attendee List
 * pay_status = 0 user_apply = 0
 */
function itg_em_admin_attendee_cancel_list($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    $attendee_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE pay_status=%d AND user_apply=%d LIMIT %d, 10", 0, 0, $start));
    
    if($attendee_list) {
        foreach($attendee_list as $atd) {
            $atd_info = itg_em_admin_user_info($atd->uid);
            $atd->email = $atd_info->email;
            $atd->name = $atd_info->first_name . ' ' . $atd_info->last_name;
            $atd->ph_no = $atd_info->ph_no;
            
            $atd_event = $wpdb->get_row("SELECT event_name, price FROM {$itg_em_db_table_name['admin_event']} WHERE id=$atd->event_id");
            $atd->event_name = $atd_event->event_name;
            $atd->price = $atd_event->price;
        }
    }
    return $attendee_list;
}

/**
 * Attendee Search by uid
 */
function itg_em_admin_attendee_search_uid($uid, $pay_status = 0, $user_apply = 1) {
    global $wpdb, $itg_em_db_table_name;
    
    $attendee_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE uid=%d AND pay_status=%d AND user_apply=%d", $uid, $pay_status, $user_apply));
    
    if($attendee_results) {
        $atd_info = itg_em_admin_user_info($uid);
        foreach($attendee_results as $atd) {
            $atd_event =  $wpdb->get_row("SELECT event_name, per_disc, price FROM {$itg_em_db_table_name['admin_event']} WHERE id=$atd->event_id");
            $atd->event_name = $atd_event->event_name;
            $atd->price = $atd_event->price;
            $atd->per_disc = $atd_event->per_disc;
            $atd->email = $atd_info->user_email;
            $atd->name = $atd_info->first_name . ' ' . $atd_info->last_name;
            $atd->ph_no = $atd_info->ph_no;
        }
    }
    return $attendee_results;
}

/**
 * Attendee Search by email
 */
function itg_em_admin_attendee_search_email($email, $pay_status = 0, $user_apply = 1) {
    global $wpdb, $itg_em_db_table_name;
    
    /** First get the wp_uid for the current email
     * Email is stored on the wordpress db
     * So we will join two tables to compare users.id = user_table.wp_uid
     * If found then it will fetch both the ids as id, wp_uid for future use
     */
    $attendee_uids = $wpdb->get_row($wpdb->prepare("SELECT {$itg_em_db_table_name['user_table']}.id, {$wpdb->users}.id AS wp_uid FROM {$itg_em_db_table_name['user_table']}, {$wpdb->users} WHERE {$itg_em_db_table_name['user_table']}.wp_uid = {$wpdb->users}.id AND {$wpdb->users}.user_email=%s", $email));
    //$wpdb->show_errors(); $wpdb->print_error();
    if($attendee_uids->id) {
        $attendee_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE uid=%d AND pay_status=%d AND user_apply=%d", $attendee_uids->id, $pay_status, $user_apply));
        
        if($attendee_results) {
            $atd_info = itg_em_admin_user_info($attendee_uids->id);
            foreach($attendee_results as $atd) {
                $atd_event =  $wpdb->get_row("SELECT event_name, per_disc, price FROM {$itg_em_db_table_name['admin_event']} WHERE id=$atd->event_id");
                $atd->event_name = $atd_event->event_name;
                $atd->price = $atd_event->price;
                $atd->per_disc = $atd_event->per_disc;
                $atd->email = $atd_info->user_email;
                $atd->name = $atd_info->first_name . ' ' . $atd_info->last_name;
                $atd->ph_no = $atd_info->ph_no;
            }
        }
        return $attendee_results;
    }
    else {
        return false;
    }
}

/**
 * Get single row detail for Attendee
 */
function itg_em_admin_attendee_single_detail($id) {
    global $wpdb, $itg_em_db_table_name;
    
    $attendee_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['reg_table']} WHERE id=%d", $id));
    
    if($attendee_result) {
        $atd_info = itg_em_admin_user_info($attendee_result->uid);
        $atd_event =  $wpdb->get_row("SELECT event_name, per_disc, price FROM {$itg_em_db_table_name['admin_event']} WHERE id=$attendee_result->event_id");
        
        $attendee_result->event_name = $atd_event->event_name;
        $attendee_result->price = $atd_event->price;
        $attendee_result->per_disc = $atd_event->per_disc;
        $attendee_result->email = $email;
        $attendee_result->name = $atd_info->first_name . ' ' . $atd_info->last_name;
        $attendee_result->ph_no = $atd_info->ph_no;
    }
    return $attendee_result;
}

/**
 * A simple function to remove slash
 * if magic quote GPC is on
 * @param $value string The value of the POST/GET data
 * @return string the stripslashed value
 */
function stripslash_gpc( &$value ) {
    $value = stripslashes( $value );
}

/**
 * strip tags from all array
 */


/**
 * Generate random string
 * 8 character
 * We pass in the user id
 * and concatenate to make it ultimately unique
 * @param $length int the lenght of the random string except the prefix
 * @param $prefix mixed int|string The prefix which would be concatenated before the random code
 * @return string The random generated string
 */
function getUniqueCode($length = "", $prefix = '') {
    $code = md5(uniqid(rand(), true));
    if ($length != "") return $prefix . substr($code, 0, $length);
    else return $prefix . $code;
}

/**
 * Function to get $_SESSION['itg_em_uid'] and get current user information
 * If not already registered on the database, then it will register
 * @param void
 * @return object the db row from user_table
 */
function itg_em_user_session_set() {
    global $wpdb, $itg_em_db_table_name, $current_user;
    /** First get the user option */
    get_currentuserinfo();
    
    /** Now query the db */
    $itg_em_current_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_table']} WHERE wp_uid = %d", $current_user->ID));
    
    /** Check if present */
    if(false == $itg_em_current_user) {
        /**Insert it */
        $wpdb->insert($itg_em_db_table_name['user_table'], array('wp_uid' => $current_user->ID, 'complete' => 0), '%d');
        
        /** Now repopulate */
        $itg_em_current_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_table']} WHERE wp_uid = %d", $current_user->ID));
    }
    
    /** Populate the object */
    $itg_em_current_user->first_name = $current_user->user_firstname;
    $itg_em_current_user->last_name = $current_user->user_lastname;
    $itg_em_current_user->email = $current_user->user_email;
    
    /** Return */
    return $itg_em_current_user;
}

/**
 * The small class behind the session return object
 * not using
 */
class itgemSession {
    var $id;
    var $wp_uid;
    var $complete;
    var $ph_no;
    var $address;
    var $state;
    var $city;
    var $country;
}

/**
 * List down team members
 * @param int $start The start offset
 * @global $_SESSION['itg_em_uid'] to populate the listing
 * @global $wpdb
 * @global $itg_em_db_table_name
 * @return object The list of team members
 */
function itg_em_user_member_list($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    $member_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d LIMIT %d,10", $_SESSION['itg_em_uid'], $start));
    return $member_list;
}

/**
 * Fetch team member information
 * Checks for authenticity
 * @param int $id the if of the team member
 * @global $_SESSION['itg_em_uid'] to populate the listing
 * @global $wpdb
 * @global $itg_em_db_table_name
 * @return object The detail of the specific team member
 */
function itg_em_user_member_detail($id) {
    global $wpdb, $itg_em_db_table_name;
    
    $member_detail = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d AND id = %d", $_SESSION['itg_em_uid'], $id));
    return $member_detail;
}

/**
 * List down upcoming events for users
 * @param int $start the row offset
 * @global object $wpdb
 * @global array $itg_em_db_table_name
 * @return object the list of rows
 */
function itg_em_user_event_list($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    $today = date('Y-m-d');
    /** Get the basic list */
    $event_lists = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['admin_event']} WHERE end_date >= %s LIMIT %d,10", $today, $start));
    
    /** Extend it for action */
    foreach($event_lists as $event_list) {
        $app_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$itg_em_db_table_name['reg_table']} WHERE uid = %d AND event_id = %d", $_SESSION['itg_em_uid'], $event_list->id));
        
        if(!$app_id) {
            $event_list->app_id = false;
        }
        else {
            $event_list->app_id = $app_id;
        }
    }
    return $event_lists;
}

/**
 * Get all the users of current member
 */
function itg_em_user_cur_mem() {
    global $wpdb, $itg_em_db_table_name;
    
    $user_team = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d", $_SESSION['itg_em_uid']));
    return $user_team;
}

/**
 * Get only the list of ids of the members under the current user
 */
function itg_em_user_cur_mem_ids() {
    global $wpdb, $itg_em_db_table_name;
    
    $ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d", $_SESSION['itg_em_uid']));
    return $ids;
}

/**
 * Verification function for Event Application
 * Level 1: If user not already registered for that event
 * Level 2: If the event end date is not less than today's date
 * @param int $id The event id
 * @global $wpdb, $itg_em_db_table_name
 * @return bool true if User is authorized to do this false if not
 */
function itg_em_user_event_verification($id) {
    global $wpdb, $itg_em_db_table_name;
    
    /**
     * Level 1: If he is not registered yet
     */
    $ev_verification = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$itg_em_db_table_name['reg_table']} WHERE uid = %d AND event_id = %d", $_SESSION['itg_em_uid'], $id));
    if($ev_verification) {
        return false;
    }
    else {
        /**
         * Level 2: If the event is upcoming
         */
        $ev_verification = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$itg_em_db_table_name['admin_event']} WHERE end_date <= %s AND id = %d", date('Y-m-d'), $id));
        if($ev_verification) {
            return false;
        }
        else {
            return true;
        }
    }
}

/**
 * List down the applied events
 * @param int $start The start row offset
 * @global $wpdb
 * @global $_SESSION['ite_em_uid']
 * Advanced MySQL technique to join tables
 * LEFT reg_table JOIN admin_event
 * ON reg_table.event_id = admin_event.id
 */
function itg_em_user_event_status($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    $ev_list = $wpdb->get_results($wpdb->prepare("SELECT {$itg_em_db_table_name['reg_table']}.id, uid, team_ids, event_id, reg_id, pay_status, user_apply, note, date, event_name, start_date, end_date FROM {$itg_em_db_table_name['reg_table']} LEFT JOIN {$itg_em_db_table_name['admin_event']} ON {$itg_em_db_table_name['reg_table']}.event_id = {$itg_em_db_table_name['admin_event']}.id WHERE uid = %d", $_SESSION['itg_em_uid']));
    
    /**
     * Loop through and get member informaiton
     * Also append event information
     */
    if($ev_list) {
        foreach($ev_list as $ev) {
            /** Team member */
            if($ev->team_ids != '') {
                $tem_mem = itg_em_admin_list_members($ev->team_ids);
            }
            else {
                $tem_mem = false;
            }
            $ev->tem_mem = $tem_mem;
        }
    }
    //$wpdb->show_errors(); $wpdb->print_error();
    return $ev_list;
}

/**
 * Function to give detailed information on a particular application
 * @param int $id The id of the application on reg_table
 * @global $wpdb
 * @global $_SESSION['itg_em_uid'] --  Not Using Rather a verification is made on the ajax function
 * Advanced MySQL technique to join tables
 * LEFT reg_table JOIN admin_event
 * ON reg_table.event_id = admin_event.id
 */
function itg_em_user_app_detail($id) {
    global $wpdb, $itg_em_db_table_name;
    
    $ev_detail = $wpdb->get_row($wpdb->prepare("SELECT {$itg_em_db_table_name['reg_table']}.id, uid, team_ids, event_id, reg_id, pay_status, user_apply, note, date, event_name, start_date, end_date, event_desc, round, venue FROM {$itg_em_db_table_name['reg_table']} LEFT JOIN {$itg_em_db_table_name['admin_event']} ON {$itg_em_db_table_name['reg_table']}.event_id = {$itg_em_db_table_name['admin_event']}.id WHERE {$itg_em_db_table_name['reg_table']}.id = %d", $id));
    
    if($ev_detail) {
        if($ev_detail->team_ids != '') {
            $tem_mem = itg_em_admin_list_members($ev_detail->team_ids);
        }
        else {
            $tem_mem = false;
        }
        $ev_detail->tem_mem = $tem_mem;
    }
    return $ev_detail;
}

/**
 * Get member list from an comma deliminated id list
 * @param string $id Comma deliminated id list
 * @return object All information of team members
 */
function itg_em_admin_list_members($id) {
    global $wpdb, $itg_em_db_table_name;
    
    $ids = wp_parse_id_list($id);
    $ids = '(' . implode(',', $ids) . ')';
    
    /** Query the database */
    $mem_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_team']} WHERE id IN {$ids}"));
    
    return $mem_list;
}

/**
 * Get all the information of Currently logged in User
 * @param void
 * @global $wpdb
 * @global $_SESSION['itg_em_wpuid']
 */
function itg_em_user_current_user_info() {
    global $wpdb, $itg_em_db_table_name;
    /** First get the wp_uid and other information */
    $user_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_table']} WHERE id = %d", $_SESSION['itg_em_uid']));
    if($user_info) {
        /** Get the information from wp_users */
        $wp_user_info = get_userdata($user_info->wp_uid);
        
        $user_info->first_name = $wp_user_info->first_name;
        $user_info->last_name = $wp_user_info->last_name;
        $user_info->email = $wp_user_info->user_email;
    }
    return $user_info;
}

/**
 * Fetch user list for admin
 * @param int $start The offset of the row
 */
function itg_em_admin_user_list($start = 0) {
    global $wpdb, $itg_em_db_table_name;
    
    /** Get basic user list first */
    $user_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$itg_em_db_table_name['user_table']} LIMIT %d, 10", $start));
    
    if($user_list) {
        /**
         * Loop through and populate other information
         * 1. First Name - Last Name
         * 2. Email
         * 4. Team Member
         */
        foreach($user_list as $user_info) {
            /** First the basic details */
            $wp_user_info = get_userdata($user_info->wp_uid);
            
            $user_info->first_name = $wp_user_info->first_name;
            $user_info->last_name = $wp_user_info->last_name;
            $user_info->email = $wp_user_info->user_email;
            $user_info->nickname = $wp_user_info->nickname;
            $user_info->user_nicename = $wp_user_info->user_nicename;
            $user_info->user_login = $wp_user_info->user_login;
            
            /** Thats it. We would show the team members via an AJAX interface */
        }
    }
    
    return $user_list;
}