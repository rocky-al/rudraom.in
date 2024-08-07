
 <header class="full nutri-header">
  <div class="navigation-wrap start-header start-style">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="header-menu">
            <nav class="navbar navbar-expand-md navbar-light">
              <a class="navbar-brand" href="{{route('frontend.dashboard')}}">
                <img src="{{getSettingValue('logo') ? url('uploads/logo').'/'.getSettingValue('logo') : URL::asset('images/logo.png')}}"  alt="web-logo" width="100px">
              </a>  
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto py-0 py-md-0">
                  <li class="nav-item pl-4 pl-md-0 ml-0">
                   <!--  <a class="nav-link" href="index.html">Home</a> -->
                     <a href="{{route('frontend.dashboard')}}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                     My Account</a>
                  </li>
                  <li class="nav-item pl-4 pl-md-0 ml-0">
                     <a href="{{route('feed.list')}}" class="nav-link {{ request()->is('feed_list') ? 'active' : '' }}">Feeds </a>
                  </li>

                    <li class="nav-item pl-4 pl-md-0 ml-0">
                     <a href="{{route('order.list')}}" class="nav-link {{ request()->is('order_list') ? 'active' : '' }}" >Orders</a>
                  </li>

                  <li class="nav-item pl-4 pl-md-0 ml-0">
                    <div class="main-profile-menu dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="userdetails">
                          <div class="usershape">

                             <?php
                    if(isset(Session::get('user')->profile_img) && !empty(Session::get('user')->profile_img)){
                    ?>

                    <img src="{{url('uploads/business_profile').'/'.Session::get('user')->profile_img}}" class="image-icon" alt="profile image"> 
                    <?php } else { ?>
                              <img src="{{url('uploads/business_profile/default_user.png')}}" class="image-icon" alt="profile image"> 
                        <?php     }   ?>
                        
                            
                          </div>
                          <div class="username">{{ Session::get('user')->name; }}</div>
                          <!-- <div class="arrowicon"><i class="fa fa-caret-down" aria-hidden="true"></i></div> -->
                        </div>
                      </button>
                       <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        
                        <a href="" id="change_pswd" data-toggle="modal" data-target="#changepasswordmodal" data-id=""   class="nav-link"> Change Password</a>
                        <a href="#" id="logout" class="nav-link"> <span>Logout</span> </a>
                      </div> 
                    </div>
                  </li>
                    <!--<li class="nav-item pl-4 pl-md-0 ml-0 logout">
                     <a href="#" id="logout" class="nav-link"><i class="fa fa-power-off" aria-hidden="true"></i> <span>Log Out</span> </a>
                    </li>-->
                  
                </ul>
              </div>
            </nav> 
           
          </div> 
        </div>
      </div>
    </div>
  </div>
</header>






    
