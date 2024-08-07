$(document).ready(function () {

    var URL = window.location.origin;


    // All Submit ajax function  
    $(document).on('click', '.submit_btn', function () {
        $(".form_submit").unbind().submit(function (event) {
            event.preventDefault(); //prevent default action 
            var formData = new FormData(this);

            var old_text = $('button[type="submit"]').closest('.submit_btn').text();

            if ($('.form_submit').valid()) {
                $('button[type="submit"]').prop('disabled', true);
                $('button[type="submit"]').text('Please wait...');
                //model loader add 
                $('#model_loader').removeClass('model_loader_class');
                $('#model_loader').addClass('loader_image_heder');

                $.ajax({
                    url: $(this).attr("action"),
                    type: $(this).attr("method"),
                    data: formData,
                    contentType: false, //this is requireded please see answers above
                    processData: false,
                    success: function (data) {
                        if (data.status === true) {
                            if (typeof (data.redirect_url) != "undefined" && data.redirect_url !== null) {
                                window.location.replace(data.redirect_url);
                            }
                            toastr.success(data.message);
                            $('#manage_data_model').modal('hide');
                            $('#data_table').DataTable().ajax.reload();
                            $('button[type="submit"]').prop('disabled', false);
                            $('button[type="submit"]').text(old_text);
                        } else {
                            toastr.error(data.message);
                            $('button[type="submit"]').prop('disabled', false);
                            $('button[type="submit"]').text(old_text);
                            $('#model_loader').addClass('model_loader_class');
                            $('#model_loader').removeClass('loader_image_heder');
                            $('button[type="submit"]').prop('disabled', false);
                            $('button[type="submit"]').text(old_text);
                        }
                    },
                    error: function (e) {
                        $('button[type="submit"]').prop('disabled', false);
                        $('button[type="submit"]').text(old_text);
                        $('#model_loader').addClass('model_loader_class');
                        $('#model_loader').removeClass('loader_image_heder');
                        $('button[type="submit"]').prop('disabled', false);
                        $('button[type="submit"]').text(old_text);
                        toastr.error(e.responseJSON.message);
                    }
                });
            }
        });
    });


// only use bank details manage 
    $(document).on('click', '.submit_btn_bank_details', function () {
        $(".form_submit_bank_details").unbind().submit(function (event) {
            event.preventDefault(); //prevent default action 
            var formData = new FormData(this);
            if ($('.form_submit_bank_details').valid()) {
                $.ajax({
                    url: $(this).attr("action"),
                    type: $(this).attr("method"),
                    data: formData,
                    contentType: false, //this is requireded please see answers above
                    processData: false,
                    success: function (data) {
                        if (data.status === true) {
                            if (typeof (data.redirect_url) != "undefined" && data.redirect_url !== null) {
                                window.location.replace(data.redirect_url);
                            }
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (e) {
                        toastr.error(e.responseJSON.message);
                    }
                });
            }
        });
    });


    $(document).on('click', '.submit_btn_family_member', function () {
        $(".form_submit_family_member").unbind().submit(function (event) {
            event.preventDefault(); //prevent default action 
            var formData = new FormData(this);
            if ($('.form_submit_family_member').valid()) {
                $.ajax({
                    url: $(this).attr("action"),
                    type: $(this).attr("method"),
                    data: formData,
                    contentType: false, //this is requireded please see answers above
                    processData: false,
                    success: function (data) {
                        if (data.status === true) {
                            if (typeof (data.redirect_url) != "undefined" && data.redirect_url !== null) {
                                window.location.replace(data.redirect_url);
                            }
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (e) {
                        toastr.error(e.responseJSON.message);
                    }
                });
            }
        });
    });


    //all Pop model open  function
    $(document).on('click', '.model_open', function () {
        var url = $(this).attr("url");
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'html',
            success: function (data) {
                $('.ajax_response').empty();
                $('#manage_data_model').modal('show');
                $('.ajax_response').html(data);
            },
            error: function (e) {
                console.log(e.responseJSON.message);
            }
        });
    });

    // all projec status changed function
    $(document).on('change', '.statusAction', function () {
        var value = $(this).val();
        var id = $(this).attr('id');
        var path = $(this).data('path');

        $("#overlay").fadeIn();
        $.ajax({
            url: path,
            method: 'get',
            data: { 'id': id, 'status': value },
            success: function (result) {
                toastr.success(result.message);
            },
            error: function (e) {
                toastr.error(result.message);
                console.log(e.responseJSON.message);
            }
        });
    });

  // all projec status changed function
  $(document).on('change', '.verificationAction', function () {
    var value = $(this).val();
    var id = $(this).attr('id');
    var path = $(this).data('path');

    $("#overlay").fadeIn();
    $.ajax({
        url: path,
        method: 'get',
        data: { 'id': id, 'status': value },
        success: function (result) {
            toastr.success(result.message);
        },
        error: function (e) {
            toastr.error(result.message);
            console.log(e.responseJSON.message);
        }
    });
});


    // all projec Delete record
    $(document).on('click', '.delete_record', function (event) {
        event.preventDefault(); //prevent default action 
        var path = $(this).attr("url");
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure you want continue!',
            buttons: {
                btnClass: 'btn-blue',
                confirm: function () {

                    $.ajax({
                        url: path,
                        method: 'get',
                        // data: { 'id': id, 'status': value },
                        success: function (result) {
                            toastr.success(result.message);
                            $('#data_table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            toastr.error(error.message);
                            console.log(error.responseJSON.message);
                        }
                    });
                },
                cancel: function () {



                    btnClass: 'btn-blue'
                },
            }
        });
    });


    //logout function 
    $(document).on('click', '.logout_btn', function (event) {
        event.preventDefault(); //prevent default action 
        var path = $(this).attr("href");
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure you want continue!',
            buttons: {
                confirm: function () {
                    $.ajax({
                        url: path,
                        method: 'get',
                        success: function (result) {
                            toastr.success(result.message);
                            if (typeof (result.redirect_url) != "undefined" && result.redirect_url !== null) {
                                window.location.replace(result.redirect_url);
                            }
                        },
                        error: function (error) {
                            toastr.error(error.message);
                            console.log(error.responseJSON.message);
                        }
                    });
                },
                cancel: function () {
                    btnClass: 'btn-blue'
                },
            }
        });
    });


    // theme style function 
    $(document).on('click', '.theme_mode', function (event) {
        event.preventDefault(); //prevent default action 
        var theme_mode = $(this).val();
        $.ajax({
            url: URL+"/admin/theme_style",
            method: 'post',
            data: { 
                theme_mode: theme_mode
              },
            success: function (result) {
                toastr.success(result.message);
            },
            error: function (error) {
                toastr.error(error.message);
                console.log(error.responseJSON.message);
            }
        });
    });



    $(document).on('change', '.state', function() {
        var state_id = $(this).val();

        $.ajax({
            url: URL+"/admin/city-list",
            method: 'get',
            data: {
                'state_id': state_id
            },
            success: function(result) {
                $(".city").empty();
                $.each(result, function(key, value) {
                    $(".city").append('<option value=' + key + '>' + value + '</option>');
                });
            }
        });
    });


    $(document).on('change', '.city', function() {
        var city_id = $(this).val();
        $.ajax({
            url: URL+"/admin/main-locality",
            method: 'get',
            data: {
                'city_id': city_id
            },
            success: function(result) {
                $(".main_locality").empty();
                $.each(result, function(key, value) {
                    $(".main_locality").append('<option value=' + key + '>' + value + '</option>');
                });
            }
        });
    });

    $(document).on('change', '.main_locality', function() {
        var main_locality_id = $(this).val();
        $.ajax({
            url: URL+"/admin/locality",
            method: 'get',
            data: {
                'main_locality_id': main_locality_id
            },
            success: function(result) {
                $(".locality").empty();
                $.each(result, function(key, value) {
                    $(".locality").append('<option value=' + key + '>' + value + '</option>');
                });
            }
        });
    });



// dynamic form add script

$(".city_select_2").select2({
    placeholder: "City By Filter",
    allowClear: true
});

$(".user_id_select_2").select2({
    placeholder: "Refer id By Filter",
    allowClear: true
});


  
  


// $("#multiple").select2({
//     placeholder: "Select a programming language",
//     allowClear: true
// });


});




