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

function add_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('validation-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js', 'jquery', '1.20.0');
}

add_action('admin_enqueue_scripts', 'add_scripts');

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
        add_submenu_page('emp-list', 'Add Employee', 'Add Employee', $role[0], 'add-emp', 'ems_add_callback');
        add_submenu_page(null, 'Update Employee', 'Update Employee', $role[0], 'update-emp', 'ems_update_callback');
        add_submenu_page(null, 'Delete Employee', 'Delete Employee', $role[0], 'delete-emp', 'ems_delete_callback');
    }
}

function ems_enqueue_styles()
{
    wp_enqueue_style('employee-table-styles', plugin_dir_url(__FILE__) . 'css/employee-list-styles.css');
}
add_action('admin_enqueue_scripts', 'ems_enqueue_styles');
add_action('wp_enqueue_scripts', 'ems_enqueue_styles');

function ems_add_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $msg = '';

    if (isset($_POST['submit'])) {

        //Sanitization input fields
        $emp_id = sanitize_text_field($_POST['emp_id']);
        $emp_name = sanitize_text_field($_POST['emp_name']);
        $emp_email = sanitize_email($_POST['emp_email']);
        $emp_dept = sanitize_text_field($_POST['emp_dept']);
        $emp_date = sanitize_text_field($_POST['emp_date']);

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
?>
    <h4 id="msg"><?php echo $msg; ?></h4>
    <form method="post" class="add-employee-style" id="emsform">
        <div>
            <h1 class="add-emp-title">Add Employee</h1>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">EMP ID</label>
            <input class="input-box-style" type="text" name="emp_id" placeholder="Enter ID" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Name</label>
            <input class="input-box-style" type="text" name="emp_name" placeholder="Enter Name" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Email</label>
            <input class="input-box-style" type="email" name="emp_email" placeholder="Enter Email" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Department</label>
            <input class="input-box-style" type="text" name="emp_dept" placeholder="Enter Department" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Date</label>
            <input class="input-box-style" type="date" name="emp_date" required>
        </div>
        <div class="employee-submit-btn">
            <button class="btn-style" type="submit" name="submit">Add</button>
        </div>
    </form>

    <script>
        JQuery(function() {
            JQuery('#emsform').validate({

                rules: {
                    emp_id: {
                        required: true,
                        number: true
                    },
                    emp_name: {
                        required: true,

                    },
                    emp_email: {
                        required: true,
                    },
                    emp_dept: {
                        required: true,
                    }
                }
            });

        });
    </script>
    <?php
}

function ems_list_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $employee_list = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if (count($employee_list) > 0) :
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
                    <?php if (current_user_can('manage_options')) : ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
                <?php
                $i = 1;
                foreach ($employee_list as $employee) :
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $employee['emp_id']; ?></td>
                        <td><?php echo $employee['emp_name']; ?></td>
                        <td><?php echo $employee['emp_email']; ?></td>
                        <td><?php echo $employee['emp_dept']; ?></td>
                        <td><?php echo $employee['emp_date']; ?></td>
                        <?php if (current_user_can('manage_options')) : ?>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=update-emp&id=' . $employee['id']); ?>">Edit</a>
                                <a href="<?php echo admin_url('admin.php?page=delete-emp&id=' . $employee['id']); ?>">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php
    else :
        echo "<h2>Employee Record Not Found</h2>";
    endif;
}

function ems_update_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $msg = '';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (isset($_POST['update'])) {
        if (!empty($id)) {
            $emp_id = sanitize_text_field($_POST['emp_id']);
            $emp_name = sanitize_text_field($_POST['emp_name']);
            $emp_email = sanitize_email($_POST['emp_email']);
            $emp_dept = sanitize_text_field($_POST['emp_dept']);
            $emp_date = sanitize_text_field($_POST['emp_date']);
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
    }
    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
    ?>
    <h4><?php echo $msg; ?></h4>
    <form method="post" class="add-employee-style">
        <div>
            <h1 class="add-emp-title">Edit Employee</h1>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">EMP ID</label>
            <input class="input-box-style" type="text" name="emp_id" placeholder="Enter ID" value="<?php echo $employee_details['emp_id']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Name</label>
            <input class="input-box-style" type="text" name="emp_name" placeholder="Enter Name" value="<?php echo $employee_details['emp_name']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Email</label>
            <input class="input-box-style" type="email" name="emp_email" placeholder="Enter Email" value="<?php echo $employee_details['emp_email']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Department</label>
            <input class="input-box-style" type="text" name="emp_dept" placeholder="Enter Department" value="<?php echo $employee_details['emp_dept']; ?>" required>
        </div>
        <div class="employee-input-field">
            <label class="lable-style">Date</label>
            <input class="input-box-style" type="date" name="emp_date" value="<?php echo $employee_details['emp_date']; ?>" required>
        </div>
        <div class="employee-submit-btn">
            <button class="btn-style" type="submit" name="update">Update</button>
        </div>
    </form>
<?php
}

function ems_delete_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ems';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $msg = '';

    if (isset($_POST['delete'])) {
        $confirmation = sanitize_text_field($_POST['conf']);
        if ($confirmation === 'yes' && $id > 0) {
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
            $msg = 'Data Deleted Successfully';
        }
    }
?>
    <h4><?php echo $msg; ?></h4>
    <form method="post">
        <div>
            <label>Are you sure you want to delete?</label><br>
            <input type="radio" name="conf" value="yes">Yes
            <input type="radio" name="conf" value="no" checked>No
        </div>
        <div>
            <button type="submit" name="delete">Delete</button>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </div>
    </form>
<?php
}

//Add Shortcode
add_shortcode('employee_list', 'ems_list_callback');
