(function ($) {
    function ajaxMethodCalling(emp_data){
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : myAjax.ajaxurl,
            data : {action: "ems_add_callback", emp_data : emp_data, nonce: myAjax.nonce},
            success: function(response) {
             console.log(response);
             console.log("Succesfully Working");
              // Show alert for success
             alert(response?.data);
             // Clear input fields
             $("#emp_id, #emp_name, #emp_email, #emp_dept, #emp_date").val('');
            }
         });
    }
    $(document).ready(function(){
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

            jQuery(".btn-style").click( function(e) {
                let emp_id = $("#emp_id").val();
                let emp_name = $("#emp_name").val();
                let emp_email = $("#emp_email").val();
                let emp_dept = $("#emp_dept").val();
                let emp_date = $("#emp_date").val();
    
                const emp_data ={
                    emp_id : emp_id,
                    emp_name : emp_name,
                    emp_email : emp_email,
                    emp_dept : emp_dept,
                    emp_date : emp_date
                };
                e.preventDefault(); 
                if(emp_id && emp_name && emp_email && emp_dept && emp_date ){
                    ajaxMethodCalling(emp_data)
                }else{
                    alert("Please Enter Data");
                }
                
             });
            
          });

          // Ajax Request for deleting data
          $("td").on("click","btn-del", function(){
            console.log("Button clicked");
          })
})(jQuery);


