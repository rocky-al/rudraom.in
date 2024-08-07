$(document).on('click', '.submit_btn', function () {
    $(".form_submit").validate({
        rules: {
            name: {
                required: true,
                maxlength:30
            },

            first_name: {
                required: true,
                maxlength:30
            },

            amount: {
                required: true
            },

            offer_expire: {
                required: true
            },
            last_name: {
                required: true,
                maxlength:30
            },

            mobile: {
                required: true,
                minlength:7,
                maxlength:15,
            },

            status: {
                required: true,
            },

            map_link: {
                required: true,
            },

            discount: {
                required: true,
            },

            total_amount: {
                required: true,
            },

            user_id: {
                required: true,
            },
            
            email: {
                required: true,
                email: true,
                maxlength:50
            },
            'permission_id[]': {
                required: true
            },

            address: {
                required: true
            },
            
            module_id: {
                required: true,
            },

            state_id: {
                required: true,
                maxlength:50
            },

            city_id: {
                required: true,
                maxlength:50
            },

            main_locality_id: {
                required: true,
                maxlength:50
            },

            locality_id: {
                required: true,
                maxlength:50
            },

            title: {
                required: true,
                maxlength:50
            },

            short_description: {
                required: true,
                maxlength:250
            },

            permission_id: {
                required: true,
            },


            current_password: {
                required: true,
                minlength: 6,
                maxlength: 15,
            },


            password: {
                required: true,
                minlength: 6,
                maxlength: 15,
            },

            confirm_password: {
                required: true,
                equalTo: "#password"
            },

            business_type: {
                required: true,
                maxlength:30
            },


            business_name: {
                required: true,
                maxlength:30
            },

            user_type: {
                required: true,
            },

            address: {
                required: true,
            },

            zip_code: {
                required: true,
            },

            pen_card: {
                required: true,
            },




        },
        messages: {
            'permission_id[]':{
                required: "Please select at least 1 Permission"
             }
        },

       
      
    });
});



$(document).on('click', '.submit_btn_bank_details', function () {
    $(".form_submit_bank_details").validate({
        rules: {
            bank_holder_name: {
                required: true,
                maxlength:40
            },


            bank_name: {
                required: true,
                maxlength:30
            },


            ifsc_code: {
                required: true,
                maxlength:20
            },


            account_number: {
                required: true,
                maxlength:30
            },

            confirm_account_number: {
                required: true,
                equalTo: "#account_number",
                maxlength:30
            },

           


        },
        messages: {
            // 'permission_id[]':{
            //     required: "Please select at least 1 Permission"
            //  }
        },
    });
});


$(document).on('click', '.submit_btn_family_member', function () {
    $(".form_submit_family_member").validate({
        rules: {
            'name[]': {
                required: true
            },


            'mobile[]': {
                required: true
            },


            'age[]': {
                required: true
            },
        },
        messages: {
            // 'permission_id[]':{
            //     required: "Please select at least 1 Permission"
            //  }
        },
    });
});
