<?php
/*
Plugin Name: WP Simple Event Management System
Plugin URI: http://www.intechgrity.com/wp-simple-event-management-plugin-by-itg-manage-college-fests-and-events/
Description: A simple online event management and registration system made for the University/College Fests.
Version: 0.9.9rc3
Author: Swashata
Author URI: http://www.swashata.me/
License: GPL2
*/

/*  Copyright 2010  Swashata Ghosh  (email : swashata4u@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ('itg-event-management.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');

/** Define this file */
define('WP_ITGEM_ABSFILE', __FILE__);
define('WP_ITGEM_ABSPATH', dirname(__FILE__));

/** The activation function */
function itg_em_plugin_act_hook() {
    global $itg_em_options, $charset_collate, $wpdb;
    
    include (plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-install.php');
}
register_activation_hook(WP_ITGEM_ABSFILE, 'itg_em_plugin_act_hook');

/** Get Options Make it global*/
$itg_em_db_table_name = get_option('itg_em_db_table_name');
$itg_em_options = get_option('itg_em_options');

/** Include the core db functions */
include_once(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-core-db.php');

/**
 * Load SESSION
 * We use this to get and store id of the current from user_table
 * Also, this generates admin_notices if the user have not currently added his information
 */
//add_action( 'admin_notices', 'help_text');
function itg_em_session_manage_hook() {
    if(current_user_can('itg_em_cap_subs')) {
        /** Start the session if not already */
        if(!session_id()) {
            session_start();
        }
        
        /** Check and get the user id and from user_table */
        if(!$_SESSION['itg_em_wpuid'] || !!$_SESSION['itg_em_uid'] || !$_SESSION['itg_em_complete']) {
            $session_data = itg_em_user_session_set();
            
            $_SESSION['itg_em_wpuid'] = $session_data->wp_uid;
            $_SESSION['itg_em_uid'] = $session_data->id;
            $_SESSION['itg_em_complete'] = $session_data->complete;
        }
        
        if(0 == $_SESSION['itg_em_complete']) {
            add_action('admin_notices', 'itg_em_user_ci_notice');
        }
    }
}

/**
 * The notice function
 */
function itg_em_user_ci_notice() {
    $notice = <<<EOD
<div class="ui-widget" style="clear: both; margin-top: 20px;" id="itg_em_user_notice">
    <div class="ui-state-error ui-corner-all error fade" style="padding: 0 .7em;"> 
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
        <strong>Alert:</strong> You have not updated your contact information. Please do so by going to Contact Information menu. <a href="admin.php?page=itg_em_user_profile_page" class="button-primary">Update NOW</a></p>
    </div>
</div>
EOD;
    if(0 == $_SESSION['itg_em_complete'])
        echo $notice;
}
/**
 * Hook it up
 */
add_action('init', 'itg_em_session_manage_hook');

/**
 * The Session destroy function
 */
function itg_em_destroy_session() {
    if($_GET['action'] == 'logout') {
        session_destroy();
        echo '<!-- Session destroyed -->';
    }
}

add_action('admin_head-wp-login.php', 'itg_em_destroy_session');

/************************************
 ************Administrator***********
 ************************************
 */
/**
 * The whole admin area
 * 1. Institution Detail
 * 2. Add Event
 * 3. Edit Event
 * 4. Active Attendees
 * 5. Approved Application
 * 6. Cancelled Applications
 * 7. Print Registration
 * 8. Users
 * 9. About
 */

/** Function to add admin page */
function itg_em_admin_menu_hook() {
    global $wpdb, $itg_em_db_table_name;
    $num_unapp = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$itg_em_db_table_name['reg_table']} WHERE pay_status = %d AND user_apply = %d", 0, 1));
    
    /** All Page array */
    $itg_em_admin_page = array();
    
    /** The main page */
    $itg_em_admin_page[] = add_menu_page('iTg Event Manager Administration - by Swashata', 'Events Admin', 'itg_em_cap_admin', 'itg_event_admin', 'itg_em_admin_menu_main', plugins_url('images/admin-icon.png', WP_ITGEM_ABSFILE), 10);
    /** Institution Detail */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'Edit Institution Details', 'Institution Detail', 'itg_em_cap_admin', 'itg_event_admin', 'itg_em_admin_menu_main');
    /** Add an event page */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'Add an Event', 'Add Event', 'itg_em_cap_admin', 'itg_event_admin_add', 'itg_em_admin_menu_add');
    /** View/Edit events page */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'View and Edit Events', 'Edit Events', 'itg_em_cap_admin', 'itg_event_admin_edit', 'itg_em_admin_menu_edit');
    /** View/Edit Attendee */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'View and Edit Attendee', 'Active Attendees<span class="update-plugins count-' . $num_unapp . '"><span class="plugin-count">' . $num_unapp . '</span></span>', 'itg_em_cap_admin', 'itg_event_admin_attendee', 'itg_em_admin_menu_attendee');
    /** View/Edit Approved Application */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'View and Edit Approved Application', 'Approved Applications', 'itg_em_cap_admin', 'itg_event_admin_approved', 'itg_em_admin_menu_approved');
    /** View/Edit Closed Application */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'View and Edit Cancelled Application', 'Cancelled Applications', 'itg_em_cap_admin', 'itg_event_admin_cancelled', 'itg_em_admin_menu_cancelled');
    /** Get All app details */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'Get Printable Tabular data of registration', 'Print Registrations', 'itg_em_cap_admin', 'itg_event_admin_list_atd', 'itg_em_admin_menu_list_atd');
    /** View/Edit/Modify users */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'View and Edit Users', 'Users', 'itg_em_cap_admin', 'itg_event_admin_user', 'itg_em_admin_menu_user');
    /** About Page */
    $itg_em_admin_page[] = add_submenu_page('itg_event_admin', 'About WP Simple Event Management', 'About', 'itg_em_cap_admin', 'itg_event_admin_about', 'itg_em_admin_menu_about');
    /** Done all */
    /**
     * Now enqueue the css file
     * And the JS file
     */
    foreach($itg_em_admin_page as $itg_em_admin_paged) {
        add_action('admin_print_styles-' . $itg_em_admin_paged, 'itg_em_admin_style_hook');
        add_action('admin_print_scripts-' . $itg_em_admin_paged, 'itg_em_admin_js_hook');
    }
}
add_action('admin_menu', 'itg_em_admin_menu_hook');

/**
 * Global style & js register function
 */
function itg_em_global_style_js_hook() {
    wp_register_script('itg_em_jquery_ui', plugins_url('js/jquery-ui-1.8.4.custom.min.js', WP_ITGEM_ABSFILE), 'jquery', '1.8.4');
    wp_register_script('itg_em_admin_js', plugins_url('js/admin-js.js', WP_ITGEM_ABSFILE), array('jquery', 'itg_em_jquery_ui'), '1.0.0');
    wp_register_script('itg_em_global_print', plugins_url('js/jquery.PrintArea.js', WP_ITGEM_ABSFILE), array('jquery', 'itg_em_jquery_ui'), '1.0.0');
    wp_register_script('itg_em_user_jqui', plugins_url('js/jquery-ui-1.8.4.custom.min.js', WP_ITGEM_ABSFILE), array('jquery'), '1.0.0');
    wp_register_script('itg_em_user_jqval', plugins_url('js/jquery.validationEngine.js', WP_ITGEM_ABSFILE), array('jquery'), '1.0.0');
    wp_register_script('itg_em_user_jqvalen', plugins_url('js/jquery.validationEngine-en.js', WP_ITGEM_ABSFILE), array('jquery', 'itg_em_user_jqval'), '1.0.0');
    wp_register_script('itg_em_user_jqcus', plugins_url('js/user-js.js', WP_ITGEM_ABSFILE), array('jquery', 'itg_em_user_jqui', 'itg_em_user_jqval', 'itg_em_user_jqvalen'), '1.0.0');
}
add_action('admin_init', 'itg_em_global_style_js_hook');

/**
 * The Admin style and script enqueue functions
 */
function itg_em_admin_style_hook() {
    wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
    wp_enqueue_style('jquery_ui_css', plugins_url('css/jquery-ui-1.8.4.custom.css', WP_ITGEM_ABSFILE));
    wp_enqueue_style('admin_em_css', plugins_url('css/admin.css', WP_ITGEM_ABSFILE));
}
function itg_em_admin_js_hook() {
    //jquery.PrintArea.js
    wp_enqueue_script('thickbox');
    wp_enqueue_script('itg_em_jquery_ui');
    wp_enqueue_script('itg_em_admin_js');
    wp_enqueue_script('itg_em_global_print');
}
/**
 * The admin menu functions
 */
/** The main menu */
function itg_em_admin_menu_main() {
    global $itg_em_options;
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-general.php');
}

/** Add an Event */
function itg_em_admin_menu_add() {
    global $wpdb, $itg_em_db_table_name;
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-ad-event.php');
}

/** View/Edit Events */
function itg_em_admin_menu_edit() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-edit-event.php');
}

/** View Edit Attendees */
function itg_em_admin_menu_attendee() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-attende.php');
}

/** View Edit Approved application */
function itg_em_admin_menu_approved() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-approved.php');
}

/** View Edit Cancelled application */
function itg_em_admin_menu_cancelled() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-canceled.php');
}

/** Get Printable data */
function itg_em_admin_menu_list_atd() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-list-atd.php');
}

/** View Edit Users */
function itg_em_admin_menu_user() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-user.php');
}

/** A little about us */
function itg_em_admin_menu_about() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-about.php');
}

/************************************
 **************User Page*************
 ************************************
 */
/**
 * The subscriber USER area
 * 1. Help & Support
 * 2. Contact Information
 * 3. Add Team Member
 * 4. Edit Team members
 * 5. Apply for Events
 * 6. Application Status
 */
/** The hook function */
function itg_em_user_menu_hook() {
    global $wpdb, $itg_em_db_table_name;
    /** All page array */
    $itg_em_user_page = array();
    
    /** Get the number of approved application for displaying */
    $num_app = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$itg_em_db_table_name['reg_table']} WHERE user_apply = %d AND pay_status = %d AND uid = %d", 0, 1, $_SESSION['itg_em_uid']));
    /** The main page */
    $itg_em_user_page[] = add_menu_page('Events Information', 'Events Info.', 'itg_em_cap_subs', 'itg_em_user_page', 'itg_em_user_help_page', plugins_url('images/user-icon.png', WP_ITGEM_ABSFILE), 11);
    /** Clone Help and Support */
    $itg_em_user_page[] = add_submenu_page('itg_em_user_page', 'View Basic Help', 'Help &amp; Support', 'itg_em_cap_subs', 'itg_em_user_page', 'itg_em_user_help_page');
    /** Contact Information */
    $itg_em_user_page[] = add_submenu_page('itg_em_user_page', 'Edit Your Contact Information', 'Contact Information', 'itg_em_cap_subs', 'itg_em_user_profile_page', 'itg_em_user_profile');
    /** Add Team member */
    $itg_em_user_page[] = add_submenu_page('itg_em_user_page', 'Add Your Team member', 'Add Team Member', 'itg_em_cap_subs', 'itg_em_user_add_team_page', 'itg_em_user_add_team');
    /** Edit Team Member */
    $itg_em_user_page[] = add_submenu_page('itg_em_user_page', 'Edit Your Team member', 'Edit Team Members', 'itg_em_cap_subs', 'itg_em_user_edit_team_page', 'itg_em_user_edit_team');
    /** Event Apply */
    $itg_em_user_page[] = add_submenu_page('itg_em_user_page', 'Apply for an Event', 'Apply for Events', 'itg_em_cap_subs', 'itg_em_user_apply_event_page', 'itg_em_user_apply_event');
    /** App status */
    $itg_em_user_page[] = add_submenu_page('itg_em_user_page', 'Edit or View your Application', 'Application Status <span title="' . $num_app . ' Approved Applications" class="update-plugins count-' . $num_app . '"><span class="plugin-count">' . $num_app . '</span></span>', 'itg_em_cap_subs', 'itg_em_user_app_status_page', 'itg_em_user_app_status');
    
    
    foreach($itg_em_user_page as $user) {
        add_action('admin_print_styles-' . $user, 'itg_em_user_style_hook');
        add_action('admin_print_scripts-' . $user, 'itg_em_user_js_hook');
    }
}
add_action('admin_menu', 'itg_em_user_menu_hook');

/** The stylesheet and js enqueue function */
function itg_em_user_style_hook() {
    wp_enqueue_style('itg_em_user_jqui_css', plugins_url('css/jquery-ui-1.8.4.custom.css', WP_ITGEM_ABSFILE));
    wp_enqueue_style('itg_em_user_jqval_css', plugins_url('css/validationEngine.jquery.css', WP_ITGEM_ABSFILE));
    wp_enqueue_style('itg_em_user_css', plugins_url('css/user.css', WP_ITGEM_ABSFILE));
    wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
}
function itg_em_user_js_hook() {
    /** Deregister jquery ui default script */
    wp_deregister_script('jquery-ui-core ');
    wp_deregister_script('jquery-ui-tabs ');
    
    /** Enqueue our own
     * This is for compatibility issue i had with some other nasty plugin
     * echoing their script throughout the admin panel
     */
    wp_enqueue_script('thickbox');
    wp_enqueue_script('itg_em_user_jqui');
    wp_enqueue_script('itg_em_user_jqval');
    wp_enqueue_script('itg_em_user_jqvalen');
    wp_enqueue_script('itg_em_user_jqcus');
    wp_enqueue_script('itg_em_global_print');
}

/** View the basic Help page */
function itg_em_user_help_page() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-user-help.php');
}

/** Profile Page */
function itg_em_user_profile() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-user-contact.php');
}

/** Add Team Page */
function itg_em_user_add_team() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-user-team-add.php');
}

/** Edit Team Page */
function itg_em_user_edit_team() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-user-team-edit.php');
}

/** Apply for events */
function itg_em_user_apply_event() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-user-event-apply.php');
}

/** App status */
function itg_em_user_app_status() {
    include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-user-event-status.php');
}

/**
 * AJAX references
 */
include(plugin_dir_path(WP_ITGEM_ABSFILE) . 'includes/itg-em-admin-ajax.php');

/**
 * User deletion Hook for deleting user
 * This will remove the users record
 * Also It will remove users application
 * Team members
 * etc..
 * @param int $id The ID of the user, ie, wp_uid
 * @global $wpdb
 * @global $itg_em_db_table_name
 * @return null
 */
function itg_em_admin_remove_user($id) {
    global $wpdb, $itg_em_db_table_name;
    
    /** Get itg_em_uid from the database */
    $itg_em_uid = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$itg_em_db_table_name['user_table']} WHERE wp_uid = %d", $id));
    
    /** Return if not exists
     * Although not possible, but still
     */
    if(!$itg_em_uid) {
        return;
    }
    else {
        /** Delete all record from reg_table */
        $wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['reg_table']} WHERE uid = %d", $itg_em_uid));
        
        /** Delete Team members */
        $wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['user_team']} WHERE itgem_uid = %d", $itg_em_uid));
        
        /** Finally delete the record */
        $wpdb->query($wpdb->prepare("DELETE FROM {$itg_em_db_table_name['user_table']} WHERE id = %d", $itg_em_uid));
    }
    /** Done */
}
/**
 * Hook it
 */
add_action('delete_user', 'itg_em_admin_remove_user');