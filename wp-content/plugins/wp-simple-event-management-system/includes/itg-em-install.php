<?php
if ('itg-em-install.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');

/**
 * This file deals with the first time installation options
 * 0. Checks if PHP 5
 * 1. Adds capabilities
 * 2. Sets default options
 * 3. Adds database
 */

/** Check for PHP 5 */
/**
 * Check to see if PHP 5. Thanks Gautam <http://gaut.am>
 */
if (version_compare(PHP_VERSION, '5.0.0', '<')) {
        deactivate_plugins(basename(WP_ITGEM_ABSFILE));
        wp_die('WP Category List Wordpress plugin requires PHP5. Sorry!');
        return;
}
/**
 * Include the necessary files
 * Also the global options
 */
if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
} else {
    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
}

global $itg_em_options, $charset_collate, $wpdb;

/**
 * Add capability
 * 1. itg_em_admin to the administrator
 * 2. itg_em_subs to the subscriber
 */
function itg_em_user_cap() {
    /** The admin */
    $role = get_role('administrator');
    if(NULL !== $role) {
        $role->add_cap('itg_em_cap_admin');
        $role->add_cap('itg_em_cap_subs');
    }
    
    /** The editor */
    $role = get_role('editor');
    if(NULL !== $role) {
        $role->add_cap('itg_em_cap_admin');
        $role->add_cap('itg_em_cap_subs');
    }
    
    /** The author */
    $role = get_role('author');
    if(NULL !== $role) {
        $role->add_cap('itg_em_cap_subs');
    }
    
    /** The contributor */
    $role = get_role('contributor');
    if(NULL !== $role) {
        $role->add_cap('itg_em_cap_subs');
    }
    
    /** The subscriber */
    $role = get_role('subscriber');
    if(NULL !== $role) {
        $role->add_cap('itg_em_cap_subs');
    }
}
itg_em_user_cap();

/**
 * Set default Options
 */
function itg_em_def_options() {
    /** User options */
    $itg_em_options = array(
        'institute_name' => 'RCC Institute of Information Technology',
        'institute_short_name' => 'RCCIIT',
        'fest_name' => 'Techtrix\'X',
        'address1' => 'Canal South Road',
        'address2' => 'Beliaghata',
        'country' => 'India',
        'state' => 'West Bengal',
        'city' => 'Kolkata',
        'pincode' => '700015',
        'contact_email' => get_option('admin_email'),
        'phone_num1' => '033-2323-2463',
        'phone_num2' => '033-2323-3356',
        'notes' => '',
        'currency' => 'Rs.'
    );
    add_option('itg_em_options', $itg_em_options, '', 'no');
}
itg_em_def_options();

/**
 * The Database structure
 * As of 1.0.0 I only create the database
 */
/** Create and store the db table names */
$itg_em_db_table_name = array(
    'user_table' => $wpdb->prefix . 'itgem_user',
    'user_team' => $wpdb->prefix . 'itgem_user_team',
    'admin_event' => $wpdb->prefix . 'itgem_admin_event',
    'reg_table' => $wpdb->prefix . 'itgem_reg'
);
add_option('itg_em_db_table_name', $itg_em_db_table_name, '', 'no');

/** Now create the tables */
if($wpdb->get_var("SHOW TABLES LIKE '$itg_em_db_table_name[user_table]'") != $itg_em_db_table_name['user_table']) {
    $sql =  "CREATE TABLE {$itg_em_db_table_name[user_table]} (
            id INT(11) NOT NULL auto_increment,
            wp_uid INT(11) NOT NULL default 0,
            ph_no VARCHAR(15) NOT NULL default '',
            univ VARCHAR(100) NOT NULL default '',
            year INT(4) NOT NULL default 0,
            dept VARCHAR(100) NOT NULL default '',
            roll_no VARCHAR(50) NOT NULL default '',
            address TEXT NOT NULL,
            state VARCHAR(100) NOT NULL default '',
            city VARCHAR(100) NOT NULL default '',
            country VARCHAR(100) NOT NULL default '',
            complete TINYINT(1) NOT NULL default 0,
            UNIQUE KEY wp_uid (wp_uid),
            PRIMARY KEY (id)
            ) $charset_collate;";
    
    dbDelta($sql);
}

if($wpdb->get_var("SHOW TABLES LIKE '$itg_em_db_table_name[user_team]'") != $itg_em_db_table_name['user_team']) {
    $sql =  "CREATE TABLE {$itg_em_db_table_name[user_team]} (
            id INT(11) NOT NULL auto_increment,
            itgem_uid INT(11) NOT NULL default 0,
            first_name VARCHAR(255) NOT NULL default '',
            last_name VARCHAR(255) NOT NULL default '',
            email VARCHAR(255) NOT NULL default '',
            ph_no VARCHAR(15) NOT NULL default '',
            PRIMARY KEY (id)
            ) $charset_collate;";
    
    dbDelta($sql);
}

if($wpdb->get_var("SHOW TABLES LIKE '$itg_em_db_table_name[admin_event]'") != $itg_em_db_table_name['admin_event']) {
    $sql =  "CREATE TABLE {$itg_em_db_table_name[admin_event]} (
            id INT(11) NOT NULL auto_increment,
            event_name VARCHAR(255) NOT NULL default '',
            event_desc TEXT NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            team_mem INT(3) NOT NULL default 1,
            mem_op TINYINT(1) NOT NULL default 0,
            round INT(3) NOT NULL default 1,
            price DEC(7,2) NOT NULL default '-1.00',
            per_disc INT(3) NOT NULL default 0,
            venue TEXT NOT NULL,
            ref VARCHAR(255) NOT NULL default '',
            PRIMARY KEY (id)
            ) $charset_collate;";
    
    dbDelta($sql);
}

if($wpdb->get_var("SHOW TABLES LIKE '$itg_em_db_table_name[reg_table]'") != $itg_em_db_table_name['reg_table']) {
    $sql =  "CREATE TABLE {$itg_em_db_table_name[reg_table]} (
            id INT(11) NOT NULL auto_increment,
            uid INT(11) NOT NULL default 0,
            team_ids VARCHAR(255) NOT NULL default '',
            event_id INT(11) NOT NULL default 0,
            reg_id VARCHAR(40) NOT NULL default '',
            date DATE NOT NULL,
            pay_status TINYINT(1) NOT NULL default 0,
            user_apply TINYINT(1) NOT NULL default 1,
            note TEXT NOT NULL,
            KEY uid (uid),
            KEY event_id (event_id),
            PRIMARY KEY (id)
            ) $charset_collate;";
    
    dbDelta($sql);
}