
<?php if (count($employee_list) > 0): ?>
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
            <?php foreach ($employee_list as $index => $employee): ?>
                <tr class="employee-row" data-index="<?php echo $index; ?>">
                    <td class="emp_uid uid"><?php echo $employee['id']; ?></td>
                    <td class="emp_uid emp_id"><?php echo $employee['emp_id']; ?></td>
                    <td class="emp_uid emp_name"><?php echo $employee['emp_name']; ?></td>
                    <td class="emp_uid emp_email"><?php echo $employee['emp_email']; ?></td>
                    <td class="emp_uid emp_dept"><?php echo $employee['emp_dept']; ?></td>
                    <td class="emp_uid emp_date"><?php echo $employee['emp_date']; ?></td>
                    <?php if (current_user_can('manage_options')): ?>
                        <td class="emp_table_data_action">
                            <button type="button" class="btn-success edit-btn" data-index="<?php echo $index; ?>">Edit</button>
                            <button type="button" class="btn-success update-btn" data-index="<?php echo $index; ?>" style="display: none;">Update</button>
                            <button type="button" class="btn-danger cancel-btn" data-index="<?php echo $index; ?>" style="display: none;">Cancel</button>
                            <button type="button" class="btn-danger emp-data-delete" data-id="<?php echo $employee['id']; ?>">Delete</button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php else: ?>
    <h2>Employee Record Not Found</h2>
<?php endif; ?>
