<?php

/**
 * Function to create the database table
 */
function ems_table_creator()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ems';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        emp_id varchar(255) NOT NULL,
        emp_name varchar(255) NOT NULL,
        emp_email varchar(255) NOT NULL,
        emp_dept varchar(255) NOT NULL,
        emp_date varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

/**
 * Ajax callback function for adding an employee
 */
function ems_add_callback()
{
    check_ajax_referer('employee_scripts_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $msg = '';

    if (isset($_POST["emp_data"])) {
        $emp_data = $_POST["emp_data"];
        // Sanitization input fields
        $emp_id = sanitize_text_field($emp_data['emp_id']);
        $emp_name = sanitize_text_field($emp_data['emp_name']);
        $emp_email = sanitize_email($emp_data['emp_email']);
        $emp_dept = sanitize_text_field($emp_data['emp_dept']);
        $emp_date = sanitize_text_field($emp_data['emp_date']);

        // Insert data into the database
        $wpdb->insert(
            $table_name,
            array(
                'emp_id' => $emp_id,
                'emp_name' => $emp_name,
                'emp_email' => $emp_email,
                'emp_dept' => $emp_dept,
                'emp_date' => $emp_date,
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );

        // Check if data was inserted successfully
        if ($wpdb->insert_id > 0) {
            $msg = "Data Saved Successfully";
        } else {
            $msg = "Failed to save data";
        }
    }

    //Send JSON response with the message
    wp_send_json_success($msg);
}

/**
 * Ajax callback function for Editing an employee
 */
function ems_update_callback()
{
    
    check_ajax_referer('employee_scripts_nonce', 'nonce');
    error_log(print_r($_POST, 1));
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $msg = '';
    $employee_data = $_POST['emp_data'];
    $id = isset($employee_data['id']) ? intval($employee_data['id']) : 0;

    // Check if employee ID is set
    if (isset($employee_data['id'])) {
        if (!empty($id)) {
            // Sanitize input fields
            $emp_id = sanitize_text_field($employee_data['emp_id']);
            $emp_name = sanitize_text_field($employee_data['emp_name']);
            $emp_email = sanitize_email($employee_data['emp_email']);
            $emp_dept = sanitize_text_field($employee_data['emp_dept']);
            $emp_date = sanitize_text_field($employee_data['emp_date']);

            // Update data in the database
            $wpdb->update(
                $table_name,
                array(
                    'emp_id' => $emp_id,
                    'emp_name' => $emp_name,
                    'emp_email' => $emp_email,
                    'emp_dept' => $emp_dept,
                    'emp_date' => $emp_date,
                ),
                array('id' => $id),
                array('%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );
            $msg = 'Data Updated Successfully';
        }

        // Send JSON response with the message
        wp_send_json_success($msg);
    }
}




/**
 * AJAX callback function for deleting an employee
 */
function ems_delete_callback()
{
    check_ajax_referer('employee_scripts_nonce', 'nonce');
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;
    $msg = '';

    if (isset($id)) {
        $confirmation = sanitize_text_field($_POST['conf']);
        if ($confirmation === 'yes' && $id > 0) {
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
            $msg = 'Data Deleted Successfully';
        } else {
            $msg = "Deletion Failed";
        }
    }

    // Send JSON response with the message
    wp_send_json_success($msg);
}
