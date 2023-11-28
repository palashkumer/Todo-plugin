<?php

/**
 * Plugin Name: A Simple Custom EMS
 * Description: This is an employee management system where you can create, read, update, and delete employees.
 * Version: 1.0.0
 * Author: Palash Kumer
 * Author URI: https://github.com/palashkumer
 */

register_activation_hook(__FILE__, 'ems_table_creator');
function ems_table_creator()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'ems';
    $sql = "CREATE TABLE $table_name (
        id mediumint(11) NOT NULL AUTO_INCREMENT,
        emp_id varchar(50) NOT NULL,
        emp_name varchar(250) NOT NULL,
        emp_email varchar(250) NOT NULL,
        emp_dept varchar(250) NOT NULL,
        emp_date date NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Enqueue scripts for admin page
function add_employee_scripts()
{
    wp_enqueue_script('validation-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js', array('jquery'), '1.20.0');
    wp_enqueue_script('employee-scripts', plugin_dir_url(__FILE__) . 'assets/employee-scripts.js', array('jquery'), true);
    
    $ajax_nonce = wp_create_nonce('employee_scripts_nonce');
    wp_localize_script('employee-scripts', 'myAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'), 
        'nonce' => $ajax_nonce, 
    ));
}

add_action('admin_enqueue_scripts', 'add_employee_scripts');
add_action('wp_enqueue_scripts', 'add_employee_scripts');



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

function ems_enqueue_styles()
{
    wp_enqueue_style('employee-table-styles', plugin_dir_url(__FILE__) . 'css/employee-list-styles.css');
}
add_action('admin_enqueue_scripts', 'ems_enqueue_styles');
add_action('wp_enqueue_scripts', 'ems_enqueue_styles');

function ems_add_callback(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $msg = '';
    if (isset($_POST["emp_data"])) {
        $emp_data = $_POST["emp_data"];
        //Sanitization input fields
        $emp_id = sanitize_text_field($emp_data['emp_id']);
        $emp_name = sanitize_text_field($emp_data['emp_name']);
        $emp_email = sanitize_email($emp_data['emp_email']);
        $emp_dept = sanitize_text_field($emp_data['emp_dept']);
        $emp_date = sanitize_text_field($emp_data['emp_date']);

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

        if ($wpdb->insert_id > 0) {
            $msg = "Data Saved Successfully";
        } else {
            $msg = "Failed to save data";
        }
    }
    wp_send_json_success($msg);
}
function ems_add_form_callback()
{

    ?>
    <form method="post" class="add-employee-style" id="emsform">
        <div>
            <h1 class="add-emp-title">Add Employee</h1>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">EMP ID</label>
            <input class="input-box-style" id="emp_id" type="text" name="emp_id" placeholder="Enter ID" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Name</label>
            <input class="input-box-style" type="text" id="emp_name" name="emp_name" placeholder="Enter Name" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Email</label>
            <input class="input-box-style" type="email" id="emp_email" name="emp_email" placeholder="Enter Email" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Department</label>
            <input class="input-box-style" type="text" id="emp_dept" name="emp_dept" placeholder="Enter Department" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Date</label>
            <input class="input-box-style" type="date" id="emp_date" name="emp_date" required>
        </div>
        <div class="employee-submit-btn">
            <button class="btn-style add-btn" type="submit" name="submit">Add</button>
        </div>
    </form>

    <?php
}

function ems_list_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $employee_list = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if (count($employee_list) > 0):
        ?>
        <div style="margin-top: 40px;">
            <div>
                <h1 class="emp-list-heading">Employee List</h1>
            </div>
            <table class="employee-list-table" border="1" cellpadding="10">
                <tr>
                    <th>S.No.</th>
                    <th>EMP ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Date</th>
                    <?php if (current_user_can('manage_options')): ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
                <?php
        
                foreach ($employee_list as $employee):
                    ?>
                    <tr>
                        <td class="emp_uid">
                            <?php echo $employee['id']; ?>
                        </td>
                        <td class="emp_uid">
                            <?php echo $employee['emp_id']; ?>
                        </td>
                        <td class="emp_uid">
                            <?php echo $employee['emp_name']; ?>
                        </td>
                        <td class="emp_uid">
                            <?php echo $employee['emp_email']; ?>
                        </td>
                        <td class="emp_uid">
                            <?php echo $employee['emp_dept']; ?>
                        </td>
                        <td class="emp_uid">
                            <?php echo $employee['emp_date']; ?>
                        </td>
                        <?php if (current_user_can('manage_options')): ?>
                            <td class="emp_table_data_action">
                            <a href="<?php echo admin_url('admin.php?page=update-emp&id=' . $employee['id']); ?>"
                                    class="btn-success">Edit</a>
                                <button data-id="<?php echo $employee['id'] ?>"  class="btn-danger emp-data-delete">Delete </button>
                                
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
    else:
        echo "<h2>Employee Record Not Found</h2>";
    endif;
}

function ems_update_callback(){
    error_log(print_r($_POST['emp_data'],1));
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $msg = '';
    $employee_data = $_POST['emp_data'];
    $id = isset($employee_data['id']) ? intval($employee_data['id']) : 0;


    if (isset($employee_data['id'])) {
        if (!empty($id)) {
            $emp_id = sanitize_text_field($employee_data['emp_id']);
            $emp_name = sanitize_text_field($employee_data['emp_name']);
            $emp_email = sanitize_email($employee_data['emp_email']);
            $emp_dept = sanitize_text_field($employee_data['emp_dept']);
            $emp_date = sanitize_text_field($employee_data['emp_date']);
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
        wp_send_json_success($msg);
    }
}
function ems_update_form_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
    ?>

    <form method="post" class="add-employee-style">
        <div>
            <h1 class="add-emp-title">Edit Employee</h1>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">EMP ID</label>
            <input class="input-box-style" type="text" id ="emp_id" name="emp_id" placeholder="Enter ID"
                value="<?php echo $employee_details['emp_id']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Name</label>
            <input class="input-box-style"  id ="emp_name" type="text" name="emp_name" placeholder="Enter Name"
                value="<?php echo $employee_details['emp_name']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Email</label>
            <input class="input-box-style"  id ="emp_email" type="email" name="emp_email" placeholder="Enter Email"
                value="<?php echo $employee_details['emp_email']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Department</label>
            <input class="input-box-style"  id ="emp_dept" type="text" name="emp_dept" placeholder="Enter Department"
                value="<?php echo $employee_details['emp_dept']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Date</label>
            <input class="input-box-style"  id ="emp_date" type="date" name="emp_date" value="<?php echo $employee_details['emp_date']; ?>"
                required>
        </div>
        <div class="employee-submit-btn">
            <button class="btn-style update-btn" data-id="<?php echo $id ?>" type="submit" name="update">Update</button>
        </div>
    </form>
    <?php
}


// Delete Method
function ems_delete_callback()
{
    check_ajax_referer('employee_scripts_nonce', 'nonce');
    error_log(print_r($_POST, true));
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;
    $msg = '';

    if (isset($id)) {
        $confirmation = sanitize_text_field($_POST['conf']);
        if ($confirmation === 'yes' && $id > 0) {
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
            $msg = 'Data Deleted Successfully';
        }else{
            $msg = "Deletion Failed";
        }
    }
    wp_send_json_success($msg);
}

//Add Shortcode
add_shortcode('employee_list', 'ems_list_callback');

// ajax testing
function ajax_method_testing()
{

    check_ajax_referer('employee_scripts_nonce', 'security');

    $emp_data = $_POST["emp_data"];

    
}
add_action("wp_ajax_ajax_method_testing", "ajax_method_testing");
add_action("wp_ajax_ems_add_callback", "ems_add_callback");
add_action("wp_ajax_ems_delete_callback", "ems_delete_callback");
add_action("wp_ajax_ems_update_callback", "ems_update_callback");
add_action('wp_ajax_ems_get_employee_details', 'ems_get_employee_details');
