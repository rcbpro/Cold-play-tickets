<?php
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
if ('uninstall' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
/**
 * The plugin uninstallation script
 * 1. Remove the tables
 * 2. Remove capabilities
 * 3. Remove options
 * Done
 */
/** Get DB Table Option */
$itg_em_db_table_name = get_option('itg_em_db_table_name');

/**
 * Set Global variable
 */
global $wpdb;

/** Remove databases */
foreach($itg_em_db_table_name as $table){
    if ($wpdb->get_var("show tables like '$table'") == $table) {
        //delete it
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}

/** Remove capabilities */
$role = get_role('administrator');
if ($role !== NULL){
    $role->remove_cap('itg_em_cap_admin');
    $role->remove_cap('itg_em_cap_subs');
}
$role = get_role('editor');
if ($role !== NULL){
    $role->remove_cap('itg_em_cap_admin');
    $role->remove_cap('itg_em_cap_subs');
}
$role = get_role('author');
if ($role !== NULL)
    $role->remove_cap('itg_em_cap_subs');
$role = get_role('contributor');
if ($role !== NULL)
    $role->remove_cap('itg_em_cap_subs');
$role = get_role('subscriber');
if ($role !== NULL)
    $role->remove_cap('itg_em_cap_subs');

/** Delete options */
delete_option('itg_em_options');
delete_option('itg_em_db_table_name');

/** Destroy Session */
if(session_id()) {
    session_destroy();
}