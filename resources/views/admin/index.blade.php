@extends('layout.admin')
@section('content')


<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 mt-3">


  <div class="col">
    <div class="card radius-10 border-start border-0 border-3 border-info">
      <div class="card-body">
      <a href="{{route('users.list')}}">
        <div class="d-flex align-items-center">
     
          <div>
            <p class="mb-0 text-secondary"> Total Users </p>
            
            <h4 class="my-1 text-info"> {{$user_count ?? 0}} </h4>
        
          </div>
          <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto"><i class='bx bxs-group'></i>
          </div>
         

        </div>
        </a>
      </div>
    </div>
  </div>


  <div class="col">
    <div class="card radius-10 border-start border-0 border-3 border-success">
      <div class="card-body">
      <a href="{{route('users.list')}}">
        <div class="d-flex align-items-center">
      
          <div>
            <p class="mb-0 text-secondary">Total Active Users</p>
            
            <h4 class="my-1 text-success"> {{$user_count_active ?? 0}} </h4>
         
          </div>
          <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-group'></i>
          </div>
        
        </div>
        </a>
      </div>
    </div>
  </div>



  <div class="col">
    <div class="card radius-10 border-start border-0 border-3 border-danger">
      <div class="card-body">
      <a href="{{route('users.list')}}">
        <div class="d-flex align-items-center">
     
          <div>
            <p class="mb-0 text-secondary"> Total Inactive Users </p>
             
            <h4 class="my-1 text-danger"> {{$user_count_inactive ?? 0}}  </h4>
            
          </div>
          <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i class='bx bxs-group'></i>
          </div>
        
        </div>
        </a>
      </div>
    </div>
  </div>





 

 










</div>
<!--end row-->



@if(roleName() == 'admin')
<div class="row d-none">
  <div class="col-12 col-lg-12">
    <div class="card radius-10">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div>
            <h6 class="mb-0">Users Overview</h6>
          </div>
        </div>
        <div class="d-flex align-items-center ms-auto font-13 gap-2 my-3">
          <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1" style="color: #14abef"></i> Users </span>
          <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1" style="color: #ffc107"></i> Business </span>
        </div>
        <div class="chart-container-1">
          <canvas id="chart1"></canvas>
        </div>
      </div>
      <div class="row row-cols-1 row-cols-md-3 row-cols-xl-3 g-0 row-group text-center border-top">
        <div class="col">
          <div class="p-3">
            <h5 class="mb-0"> {{$user_count ?? 0 }}  </h5>
            <small class="mb-0"> Overall User </small>
          </div>
        </div>
        <div class="col">
          <div class="p-3">
            <h5 class="mb-0"> {{$business_count ?? 0 }} </h5>
            <small class="mb-0"> Overall Business  </small>
          </div>
        </div>
        <!-- <div class="col">
          <div class="p-3">
            <h5 class="mb-0">639.82</h5>
            <small class="mb-0">Pages/Visit <span> <i class="bx bx-up-arrow-alt align-middle"></i> 5.62%</span></small>
          </div>
        </div> -->
      </div>
    </div>
  </div>
  <!-- <div class="col-12 col-lg-4">
    <div class="card radius-10">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div>
            <h6 class="mb-0"> Top team leader </h6>
          </div>
          <hr>
        </div>
        <div class="chart-container-2 mt-4">
          <canvas id="chart2"></canvas>
        </div>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">Jeans <span class="badge bg-success rounded-pill">25</span>
        </li>
        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">T-Shirts <span class="badge bg-danger rounded-pill">10</span>
        </li>
        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">Shoes <span class="badge bg-primary rounded-pill">65</span>
        </li>
        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">Lingerie <span class="badge bg-warning text-dark rounded-pill">14</span>
        </li>
      </ul>
    </div>
  </div> -->
</div>
@endif
<!--end row-->
@endsection

@push('scripts')
    <script src="{{URL::asset('admin/plugins/chartjs/js/Chart.min.js')}}"></script>

    <script>
$(function() {
	
  // chart 1
    var ctx = document.getElementById("chart1").getContext('2d');
    var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
        gradientStroke1.addColorStop(0, '#6078ea');  
        gradientStroke1.addColorStop(1, '#17c5ea'); 
     
    var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
        gradientStroke2.addColorStop(0, '#ff8359');
        gradientStroke2.addColorStop(1, '#ffdf40');
  
        var myChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: <?php echo $month_array; ?>,
            datasets: [{
              label: 'Users',
              data: <?php echo $monthly_users; ?>,
              borderColor: gradientStroke1,
              backgroundColor: gradientStroke1,
              hoverBackgroundColor: gradientStroke1,
              pointRadius: 0,
              fill: false,
              borderWidth: 0
            }, {
              label: 'Business',
              data: <?php echo $subscription_monthly; ?>,
              borderColor: gradientStroke2,
              backgroundColor: gradientStroke2,
              hoverBackgroundColor: gradientStroke2,
              pointRadius: 0,
              fill: false,
              borderWidth: 0
            }]
          },
      
      options:{
        maintainAspectRatio: false,
        legend: {
          position: 'bottom',
                display: false,
          labels: {
                  boxWidth:8
                }
              },
        tooltips: {
          displayColors:false,
        },	
        scales: {
          xAxes: [{
          barPercentage: .5
          }]
           }
      }
        });
      
     
  // chart 2
  
   var ctx = document.getElementById("chart2").getContext('2d');
  
    var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
        gradientStroke1.addColorStop(0, '#fc4a1a');
        gradientStroke1.addColorStop(1, '#f7b733');
  
    var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
        gradientStroke2.addColorStop(0, '#4776e6');
        gradientStroke2.addColorStop(1, '#8e54e9');
  
  
    var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
        gradientStroke3.addColorStop(0, '#ee0979');
        gradientStroke3.addColorStop(1, '#ff6a00');
      
    var gradientStroke4 = ctx.createLinearGradient(0, 0, 0, 300);
        gradientStroke4.addColorStop(0, '#42e695');
        gradientStroke4.addColorStop(1, '#3bb2b8');
  
        var myChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ["Jeans", "T-Shirts", "Shoes", "Lingerie"],
            datasets: [{
              backgroundColor: [
                gradientStroke1,
                gradientStroke2,
                gradientStroke3,
                gradientStroke4
              ],
              hoverBackgroundColor: [
                gradientStroke1,
                gradientStroke2,
                gradientStroke3,
                gradientStroke4
              ],
              data: ["10","20","30","40"],
              // borderWidth: '',
            }]
          },
          options: {
        maintainAspectRatio: false,
        cutoutPercentage: 75,
              legend: {
          position: 'bottom',
                display: false,
          labels: {
                  boxWidth:8
                }
              },
        tooltips: {
          displayColors:false,
        }
          }
        });
     });	 
      </script>
@endpush
