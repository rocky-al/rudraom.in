<div class="sidebar-wrapper" data-simplebar="true">

@php
    $segment1 = request()->segment(1);
    $segment2 = request()->segment(2);
    @endphp
    <div class="sidebar-header">


        <!-- <img src="{{URL::asset('images/logo.png')}}" class="logo-icon" alt="logo icon"> -->
       <a href="{{route('admin.index')}}"> <img id="site_logo" src="{{getSettingValue('logo') ? url('uploads/logo').'/'.getSettingValue('logo') : URL::asset('images/logo.png')}}" class="logo-icon" alt="logo icon"></a>
        <div>
            <!-- <h4 class="logo-text">{{Constant::APP_NAME}}</h4> -->
        </div>

        <div class="closecross">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
        </div>


           <!--<div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>   -->
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

   
        <li>
            <a  href="{{route('admin.index')}}" class="{{ ($segment2 == 'dashboard') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-home-circle'></i>
                </div>
                <div class="menu-title">{{__('Dashboard')}}</div>
            </a>
        </li>

        <!-- <li class="menu-label"> Management </li> -->

     
      
      <li>
            <a href="{{route('users.list')}}" class="{{ ($segment2 == 'users') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-user"></i>
                </div>
                <div class="menu-title">{{__('User Management')}}</div>
            </a>
        </li>  
        <!--   <li>
            <a href="{{route('business.list')}}" class="{{ ($segment2 == 'business') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bxs-business"></i>
                </div>
                <div class="menu-title">{{__('Business Management')}}</div>
            </a>
        </li> 

         <li>
            <a href="{{route('category.list')}}" class="{{ ($segment2 == 'category') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-category-alt"></i>
                </div>
                <div class="menu-title">{{__('Business Category')}}</div>
            </a>
        </li> 

      
        <li>
            <a href="{{route('emailTemplate.index')}}" class="{{ ($segment2 == 'email-template') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-envelope"></i>
                </div>
                <div class="menu-title">{{__('Email Template')}}</div>
            </a>
        </li> 
        <li>
            <a href="{{route('content.index')}}" class="{{ ($segment2 == 'content') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-file"></i>
                </div>
                <div class="menu-title">{{__('Page Management')}}</div>
            </a>
        </li> 
        <li>
            <a href="{{route('notification.form')}}" class="{{ ($segment2 == 'notification') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx bx-bell"></i>
                </div>
                <div class="menu-title">{{__('Notification')}}</div>
            </a>
        </li>   

        <li>
            <a href="{{route('query.list')}}" class="{{ ($segment2 == 'query') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-message"></i>
                </div>
                <div class="menu-title">{{__('Raise a query')}}</div>
            </a>
        </li> 


          <li>
            <a href="{{route('country.list')}}" class="{{ ($segment2 == 'country') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-category-alt"></i>
                </div>
                <div class="menu-title">{{__('Country')}}</div>
            </a>
        </li> 


          <li>
            <a href="{{route('city.list')}}" class="{{ ($segment2 == 'city') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-category-alt"></i>
                </div>
                <div class="menu-title">{{__('City')}}</div>
            </a>
        </li>  -->


          <li>
            <a href="{{route('admin.setting')}}" class="{{ ($segment2 == 'setting') ? 'active' : '' }}">
                <div class="parent-icon"><i class="bx bx-cog"></i>
                </div>
                <div class="menu-title">{{__('Settings')}}</div>
            </a>
        </li> 
        


<!-- 
        @if(Auth::user()->id == 1)

        <li>
            <a href="{{route('role.index')}}">
                <div class="parent-icon"><i class="bx bx-lock"></i>
                </div>
                <div class="menu-title">{{__('Role Management')}}</div>
            </a>
        </li>


        <li>
            <a href="{{route('permission.index')}}">
                <div class="parent-icon"><i class="bx bx-lock"></i>
                </div>
                <div class="menu-title">{{__('Permission')}}</div>
            </a>
        </li>

        @endif -->


    </ul>
    <!--end navigation-->
</div>
<style>
    .active{
        color: #008cff;
    text-decoration: none;
    background: rgb(13 110 253 / .12);
    }
</style>