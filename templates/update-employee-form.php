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