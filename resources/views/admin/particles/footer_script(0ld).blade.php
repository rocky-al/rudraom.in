
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">View Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="modal-body" id="replaceModal"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modelImage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="replaceModal"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>                                
    </div>                            
</div>

<script src="{{URL::asset('admin/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{URL::asset('admin/plugins/simplebar/js/simplebar.min.js')}}"></script>
    <script src="{{URL::asset('admin/plugins/metismenu/js/metisMenu.min.js')}}"></script>
    <script src="{{URL::asset('admin/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
    <script src="{{URL::asset('admin/js/app.js')}}"></script>

    <script src="{{URL::asset('commonFile/js/toastr.min.js')}}"></script> 
    <script src="{{URL::asset('commonFile/js/validate.js')}}"></script>
    <script src="{{URL::asset('admin/js/custom_function.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script type="text/javascript">
        $('.mobile-toggle-menu').on("click", function () {
            $('.sidebar-wrapper').addClass("sidebar_open");
            $('body').addClass("open-sidebar-sh");
        });
        $('.closecross').on("click", function () {
            $('.sidebar-wrapper').removeClass("sidebar_open");
            $('body').removeClass("open-sidebar-sh");
        });  

        function imageZoom(folderPath, image) {

      
        $('#replaceModal').html('<img src="https://demo.dev9server.com/usermtoag/Quennections/' + folderPath + '/' + image + '" alt="Profile Image" style="width: 100%;">');
        
        jQuery('#exampleModal').modal('show')
    }

    </script>
    

    @include('commonFile.message')









