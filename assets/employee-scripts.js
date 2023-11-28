(function ($) {
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

    // Ajax Method for deleting
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

          // Ajax Request for deleting data
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


  // Editing Ajax Mehtod
  function ajaxEditMethodCalling(emp_data){
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: { action: "ems_update_callback", emp_data: emp_data, nonce: myAjax.nonce },
        success: function (response) {
            console.log(response);
            // Show alert for success
            alert(response?.data);
        }
    });
  }
  $(document).ready(function(){
    jQuery(".update-btn").click(function (e) {
        e.preventDefault();
    
        let $button = $(this)
        var dataValue = $button.data("id");
        let emp_id = $("#emp_id").val();
            let emp_name = $("#emp_name").val();
            let emp_email = $("#emp_email").val();
            let emp_dept = $("#emp_dept").val();
            let emp_date = $("#emp_date").val();

            const emp_data = {
                id : dataValue,
                emp_id: emp_id,
                emp_name: emp_name,
                emp_email: emp_email,
                emp_dept: emp_dept,
                emp_date: emp_date
            };
            ajaxEditMethodCalling(emp_data);
        console.log(dataValue);
        console.log(emp_data);
        

    });
  })



})(jQuery);


