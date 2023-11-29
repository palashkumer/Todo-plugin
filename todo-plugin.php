<?php
/**
 * Plugin Name: A Simple Custom EMS
 * Description: This is an employee management system where you can create, read, update, and delete employees.
 * Version: 1.0.0
 * Author: Palash Kumer
 * Author URI: https://github.com/palashkumer
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Function to create the database table on plugin activation
 */
register_activation_hook(__FILE__, 'ems_table_creator');

// include ajax-functions file
require_once plugin_dir_path(__FILE__) . 'includes/ajax-functions.php';

/**
 * Enqueue scripts for admin page
 */
function add_employee_scripts()
{
    // Enqueue jQuery validation and custom scripts
    wp_enqueue_script('validation-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js', array('jquery'), '1.20.0');
    wp_enqueue_script('employee-scripts', plugin_dir_url(__FILE__) . 'assets/js/employee-scripts.js', array('jquery'), true);

    // Localize script for AJAX
    $ajax_nonce = wp_create_nonce('employee_scripts_nonce');
    wp_localize_script(
        'employee-scripts', 'myAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => $ajax_nonce,
        )
    );
}

/**
 *  Add script enqueuing actions
 */
add_action('admin_enqueue_scripts', 'add_employee_scripts');
add_action('wp_enqueue_scripts', 'add_employee_scripts');

/**
 * Add menu and submenus to the admin panel
 */
add_action('admin_menu', 'ems_display_menu');
function ems_display_menu()
{
    global $current_user;
    // print_r($current_user);
    $role = $current_user->roles;
    $accepts_roles = array('contributor', 'editor', 'administrator', 'subscriber');
    if (in_array($role[0], $accepts_roles)) {
        add_menu_page('EMS', 'EMS', $role[0], 'emp-list', 'ems_list_callback', '', 5);
        add_submenu_page('emp-list', 'Employee List', 'Employee List', $role[0], 'emp-list', 'ems_list_callback');
        add_submenu_page('emp-list', 'Add Employee', 'Add Employee', $role[0], 'add-emp', 'ems_add_form_callback');
        add_submenu_page(null, 'Update Employee', 'Update Employee', $role[0], 'update-emp', 'ems_update_form_callback');
        add_submenu_page(null, 'Delete Employee', 'Delete Employee', $role[0], 'delete-emp', 'ems_delete_callback');
    }
}

/**
 * Enqueue styles for the admin page
 */
function ems_enqueue_styles()
{
    wp_enqueue_style('employee-table-styles', plugin_dir_url(__FILE__) . 'assets/css/employee-list-styles.css');
}
add_action('admin_enqueue_scripts', 'ems_enqueue_styles');
add_action('wp_enqueue_scripts', 'ems_enqueue_styles');

/**
 *  Callback for displaying the add employee form
 */
function ems_add_form_callback()
{

    include plugin_dir_path(__FILE__) . 'templates/add-employee-form.php';

}

/**
 * Callback for displaying the employee list
 */
function ems_list_callback()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $employee_list = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    include plugin_dir_path(__FILE__) . 'templates/employee-list.php';

}

/**
 * Callback for displaying the update employee form
 */
function ems_update_form_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Get employee details based on the ID
    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);

    include plugin_dir_path(__FILE__) . 'templates/update-employee-form.php';

}

/**
 * Shortcode For displaying emp list in front end
 */
add_shortcode('employee_list', 'ems_list_callback');

/**
 * hooks
 */
add_action("wp_ajax_ems_add_callback", "ems_add_callback");
add_action("wp_ajax_ems_delete_callback", "ems_delete_callback");
add_action("wp_ajax_ems_update_callback", "ems_update_callback");
add_action('wp_ajax_ems_get_employee_details', 'ems_get_employee_details');
