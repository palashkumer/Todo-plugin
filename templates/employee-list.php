<?php
  /**
  * Display the employee list in a table
  */ 
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