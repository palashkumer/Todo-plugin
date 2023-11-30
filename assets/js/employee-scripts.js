(function ($) {
     /**
      * AJAX method for adding an employee
      */ 
    function ajaxMethodCalling(emp_data) {
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: { action: "ems_add_callback", emp_data: emp_data, nonce: myAjax.nonce },
            success: function (response) {
                console.log(response);
                console.log("Succesfully Working");
                // Show alert for success
                alert(response?.data);
                // Clear input fields
                $("#emp_id, #emp_name, #emp_email, #emp_dept, #emp_date").val('');
            }
        });
    }

    /**
     *  Ajax Method for deleting
     */
    function ajaxMethodDeleting(emp_id,$button) {
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: { action: "ems_delete_callback", emp_id: emp_id,conf:"yes", nonce: myAjax.nonce },
            success: function (response) {

                if (response) {
                    $button.closest('tr').fadeOut('slow', function () {
                        $(this).remove();
                      });
                    alert(response?.data);
                  }
                
            }
        });
    }

    /**
     *  For form validation
     */ 
    $(document).ready(function () {
        $('#emsform').validate({
            rules: {
                emp_id: {
                    required: true,
                    number: true
                },
                emp_name: {
                    required: true
                },
                emp_email: {
                    required: true,
                    email: true
                },
                emp_dept: {
                    required: true
                }
            }
        });
    });


    /**
     * Document ready function for adding an employee
     */ 
    $(document).ready(function () {

        jQuery(".add-btn").click(function (e) {
            let emp_id = $("#emp_id").val();
            let emp_name = $("#emp_name").val();
            let emp_email = $("#emp_email").val();
            let emp_dept = $("#emp_dept").val();
            let emp_date = $("#emp_date").val();

            const emp_data = {
                emp_id: emp_id,
                emp_name: emp_name,
                emp_email: emp_email,
                emp_dept: emp_dept,
                emp_date: emp_date
            };
            e.preventDefault();
            if (emp_id && emp_name && emp_email && emp_dept && emp_date) {
                ajaxMethodCalling(emp_data)
            } else {
                alert("Please Enter Data");
            }

        });

        /**
         * Ajax Request for deleting data
         */ 
        $('.emp-data-delete').on('click', function () {
        // Use $(this) to refer to the current button element
        let $button = $(this)
        var dataValue = $button.data("id");
       // Show a confirmation dialog
        var confirmation = confirm("Are you sure you want to delete this data?");
    
    // If the user confirms, proceed with the deletion
        if (confirmation) {
         ajaxMethodDeleting(dataValue,$button);
        }
        // Log the value to the console (you can use it as needed)
        console.log('Data attribute value:', dataValue);
    });

    });


  /**
   *  Ajax Mehtod for Editing data
   */ 
//   function ajaxEditMethodCalling(emp_data){
//     jQuery.ajax({
//         type: "post",
//         dataType: "json",
//         url: myAjax.ajaxurl,
//         data: { action: "ems_update_callback", emp_data: emp_data, nonce: myAjax.nonce },
//         success: function (response) {
//             console.log(response);
//             // Show alert for success
//             alert(response?.data);
//         }
//     });
//   }

  /**
   * Document ready function for editing an employee
   */ 
//   $(document).ready(function () {
//     // Edit button click event
//     $(document).on('click', '.edit-btn', function () {
//         var $row = $(this).closest('.employee-row');

//         // Hide edit button
//         $row.find('.edit-btn').hide();

//         // Show update and cancel buttons
//         $row.find('.update-btn').show();
//         $row.find('.cancel-btn').show();

//         // Convert text content to editable input fields
//         $row.find('.emp_id, .emp_name, .emp_email, .emp_dept, .emp_date').each(function () {
//             var currentValue = $(this).text().trim();
//             $(this).html('<input type="text" class="edit-field" value="' + currentValue + '">');
//         });
//     });

//     // Cancel button click event
//     $(document).on('click', '.cancel-btn', function () {
//         var $row = $(this).closest('.employee-row');

//         // Show edit button
//         $row.find('.edit-btn').show();

//         // Hide update and cancel buttons
//         $row.find('.update-btn').hide();
//         $row.find('.cancel-btn').hide();

//         // Convert input fields back to text
//         $row.find('.emp_id, .emp_name, .emp_email, .emp_dept, .emp_date').each(function () {
//             var currentValue = $(this).find('input').val();
//             $(this).html(currentValue);
//         });
//     });

//   });
// employee-scripts.js
// employee-scripts.js
// jQuery(document).ready(function ($) {
//     // Edit button click event
//     $(document).on('click', '.edit-btn', function () {
//         // Get the row containing the clicked "Edit" button
//         var $row = $(this).closest('tr');

//         // Find each cell in the row and replace its content with an input field
//         $row.find('td:not(:last-child)').each(function () {
//             var currentValue = $(this).text().trim();
//             $(this).html('<input type="text" class="edit-field" value="' + currentValue + '">');
//         });

//         // Change "Edit" button to "Update" button and "Delete" button to "Cancel" button
//         $(this).removeClass('edit-btn').addClass('update-btn').text('Update');
//         $row.find('.emp-data-delete').removeClass('emp-data-delete').addClass('cancel-btn').text('Cancel');
//     });

//     // Cancel button click event
//     $(document).on('click', '.cancel-btn', function () {
//         // Get the row containing the clicked "Cancel" button
//         var $row = $(this).closest('tr');

//         // Retrieve the original data from hidden fields
//         $row.find('.edit-field').each(function () {
//             var originalValue = $(this).prev('.original-value').val();
//             $(this).parent().text(originalValue);
//         });

//         // Change "Update" button back to "Edit" button and "Cancel" button back to "Delete" button
//         $row.find('.update-btn').removeClass('update-btn').addClass('edit-btn').text('Edit');
//         $row.find('.cancel-btn').removeClass('cancel-btn').addClass('emp-data-delete').text('Delete');
//     });

//     // Update button click event
//     $(document).on('click', '.update-btn', function (e) {
//         e.preventDefault();
//         // Get the row containing the clicked "Update" button
//         var $row = $(this).closest('tr');

//         // Extract values from input fields
//         var emp_id = $row.find('.edit-field:eq(0)').val();
//         var emp_name = $row.find('.edit-field:eq(1)').val();
//         var emp_email = $row.find('.edit-field:eq(2)').val();
//         var emp_dept = $row.find('.edit-field:eq(3)').val();
//         var emp_date = $row.find('.edit-field:eq(4)').val();

//         // AJAX request to update data in the database
//         jQuery.ajax({
//             type: "post",
//             dataType: "json",
//             url: myAjax.ajaxurl,
//             data: {
//                 action: "ems_update_callback",
//                 emp_data: {
//                     id: emp_id,
//                     emp_id: emp_id,
//                     emp_name: emp_name,
//                     emp_email: emp_email,
//                     emp_dept: emp_dept,
//                     emp_date: emp_date
//                 },
//                 nonce: myAjax.nonce
//             },
//             success: function (response) {
//                 // Show alert for success
//                 alert(response?.data);

//                 // Update UI with new data
//                 $row.find('.edit-field').each(function () {
//                     var newValue = $(this).val();
//                     $(this).parent().text(newValue);
//                 });

//                 // Change "Update" button back to "Edit" button and "Cancel" button back to "Delete" button
//                 $row.find('.update-btn').removeClass('update-btn').addClass('edit-btn').text('Edit');
//                 $row.find('.cancel-btn').removeClass('cancel-btn').addClass('emp-data-delete').text('Delete');
//             }
//         });
//     });
// });


$(document).ready(function () {
    // Edit button click event
    $(document).on('click', '.edit-btn', function () {
        console.log('Edit Button is cliked');
        var $row = $(this).closest('.employee-row');

        // Hide edit button
        $row.find('.edit-btn').hide();
        $row.find('.emp-data-delete').hide();

        // Show update and cancel buttons
        $row.find('.update-btn, .cancel-btn').show();

        // Convert text content to editable input fields
        $row.find('.emp_id, .emp_name, .emp_email, .emp_dept, .emp_date').each(function () {
            var currentValue = $(this).text().trim();
            $(this).html('<input type="text" class="edit-field" value="' + currentValue + '">');
        });
    });


    function ajaxEditMethodCalling(emp_data, successCallback) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: { action: "ems_update_callback", emp_data: emp_data, nonce: myAjax.nonce },
            success: function (response) {
                console.log(response);
                // Show alert for success
                alert(response?.data);
                if (successCallback && typeof successCallback === 'function') {
                    successCallback(response);
                }
            }
        });
    }

    // Update button click event
    $(document).on('click', '.update-btn', function (e) {
        e.preventDefault();
        var $row = $(this).closest('.employee-row');

        // Extract values from input fields
        
        var uid = $row.find('.emp_uid.uid');
        console.log(uid);
        var emp_id = $row.find('.emp_id input').val();
        var emp_name = $row.find('.emp_name input').val();
        var emp_email = $row.find('.emp_email input').val();
        var emp_dept = $row.find('.emp_dept input').val();
        var emp_date = $row.find('.emp_date input').val();

        // Prepare employee data for AJAX
        const emp_data = {
            id: uid,
            emp_id: emp_id,
            emp_name: emp_name,
            emp_email: emp_email,
            emp_dept: emp_dept,
            emp_date: emp_date
        };

        ajaxEditMethodCalling(emp_data);
            // console.log(dataValue);
            console.log(emp_data);
    });

    // Cancel button click event
    $(document).on('click', '.cancel-btn', function () {
        var $row = $(this).closest('.employee-row');

        // Show edit & delete button
        $row.find('.edit-btn, .emp-data-delete').show();

        // Hide update and cancel buttons
        $row.find('.update-btn, .cancel-btn').hide();

        // Convert input fields back to text
        $row.find('.emp_id, .emp_name, .emp_email, .emp_dept, .emp_date').each(function () {
            var currentValue = $(this).find('input').val();
            $(this).html(currentValue);
        });
    });

});




})(jQuery);


