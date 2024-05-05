@extends('layouts.admin_app')
@section('styles')

@endsection
@section('content')
{{-- 2d income row --}}
    {{-- <div class="row align-items-center">
        <div class="col-lg-12 col-sm-8">
          <div class="nav-wrapper position-relative end-0">
            <ul class="nav nav-pills nav-fill p-1" role="tablist">
              <li class="nav-item">
                <a class="nav-link mb-0 px-0 py-2 active btn btn-primary" href="{{ url('/admin/real-live-master-create') }}" aria-selected="true" style="color: aliceblue">
                  + New Master Create
                </a>
              </li>
              <li class="nav-item ms-1">
                <a class="nav-link mb-0 px-0 py-2 active btn btn-info" href="{{ url('/admin/real-live-master-list') }}" aria-selected="true" style="color: aliceblue">
                   Master List
                </a>
              </li>
              <li class="nav-item ms-1">
                <a class="nav-link mb-0 px-0 py-2 active btn btn-info btn-sm" href="{{ url('/admin/agent-user-play-early-morning') }}" aria-selected="true" style="color: aliceblue">
                 2D - 9:30 AM
                </a>
              </li>

              <li class="nav-item ms-1">
                <a class="nav-link mb-0 px-0 py-2 active btn btn-info btn-sm" href="{{ url('/admin/agent-user-play-morning') }}" aria-selected="true" style="color: aliceblue">
                 2D - 12:1 PM
                </a>
              </li>

              <li class="nav-item ms-1">
                <a class="nav-link mb-0 px-0 py-2 active btn btn-info btn-sm" href="{{ url('/admin/agent-user-play-early-evening-digit') }}" aria-selected="true" style="color: aliceblue">
                 2D - 2 PM
                </a>
              </li>

              <li class="nav-item ms-1">
                <a class="nav-link mb-0 px-0 py-2 active btn btn-info btn-sm" href="{{ url('/admin/agent-user-play-evening-digit') }}" aria-selected="true" style="color: aliceblue">
                 2D - 4:30 PM
                </a>
              </li>
              <li class="nav-item ms-1">
                <a class="nav-link mb-0 px-0 py-2 active btn btn-primary btn-sm" href="{{ url('/admin/agent-three-d-list') }}" aria-selected="true" style="color: aliceblue">
                 3D
                </a>
              </li>

            </ul>
          </div>
        </div>
    </div> --}}
        {{-- <div class="row mt-4">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card  mb-2 p-3">
                <div class="card-header p-3 pt-2">
                  <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet text-white"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Master List</p>
                    <h4 class="mb-0">
                      <a href="{{ url('/admin/real-live-master-list')}}" class="btn btn-primary">Go To Master List</a>
                    </h4>
                  </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                  <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card  mb-2 p-3">
                <div class="card-header p-3 pt-2">
                  <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet text-white"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Master Create</p>
                    <h4 class="mb-0">
                      <a href="{{ url('/admin/real-live-master-create')}}" class="btn btn-info">Create New Master</a>
                    </h4>
                  </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                  <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
                </div>
              </div>
            </div>
          </div> --}}
          <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet text-white"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">2D Daily Income</p>
                    <h4 class="mb-0">{{ number_format($dailyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-info shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">2D Weekly Income</p>
                    <h4 class="mb-0">{{ number_format($weeklyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-warning shadow-success text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize ">2D Monthly Income</p>
                    <h4 class="mb-0 ">{{ number_format($monthlyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-danger shadow-info text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize ">2D Yearly Income </p>
                    <h4 class="mb-0 ">{{ number_format($yearlyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- 2d income end  --}}
          {{-- 3d income start --}}
          <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet text-white"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">3D Daily Income</p>
                    <h4 class="mb-0">{{ number_format($three_d_dailyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mt-sm-0 mt-4">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-info shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">3D Weekly Income</p>
                    <h4 class="mb-0">{{ number_format($three_d_weeklyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-warning shadow-success text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize ">3D Monthly Income</p>
                    <h4 class="mb-0 ">{{ number_format($three_d_monthlyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
              <div class="card ">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-danger shadow-info text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize ">3D Yearly Income </p>
                    <h4 class="mb-0 "> {{ number_format($three_d_yearlyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- 3d income end --}}
          {{-- jackpot start --}}
          {{-- <div class="row mb-3 mt-5">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet text-white"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Jackpot Daily Income</p>
                    <h4 class="mb-0">{{ number_format($jackpot_dailyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-info shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Jackpot Weekly Income</p>
                    <h4 class="mb-0">{{ number_format($jackpot_weeklyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-warning shadow-success text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize ">Jackpot Monthly Income</p>
                    <h4 class="mb-0 ">{{ number_format($jackpot_monthlyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
              <div class="card">
                <div class="card-header p-3 pt-2 bg-transparent">
                  <div class="icon icon-lg icon-shape bg-gradient-danger shadow-info text-center border-radius-xl mt-n4 position-absolute">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize ">Jackpot Yearly Income </p>
                    <h4 class="mb-0 ">{{ number_format($jackpot_yearlyTotal) }} <small>MMK</small></h4>
                  </div>
                </div>
              </div>
            </div>
          </div> --}}
          {{-- jackpot end --}}
          {{-- second row start --}}
          <div class="row mt-5">
            {{-- session two reset start 1 --}}
            <div class="col-lg-6 col-md-6 col-sm-6 mb-5">
              <div class="card  mb-2 p-3">
                <div class="d-flex mt-n2">
                            <div class="avatar avatar-xl bg-info border-radius-xl p-2 mt-n4">
                              <i class="fas fa-rotate fa-2x"></i>
                                {{-- <img src="{{ asset('admin_app/assets/img/small-logos/logo-slack.svg') }}" alt="slack_logo"> --}}
                            </div>
                            <div class="ms-3 my-auto">
                                <h6 class="mb-0"> 2D Session Reset</h6>
                                <div class="avatar-group mt-4">
                                    {{-- <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip"
                                        data-original-title="Jessica Rowland">
                                        <img alt="Image placeholder" src="{{ asset('admin_app/assets/img/team-3.jpg') }}"
                                            class="">
                                    </a> --}}
                                    <form action="{{ route('admin.SessionReset') }}" method="POST">
                                      @csrf
                                      <button class="btn btn-primary" type="submit">Reset</button>
                                  </form>
                                </div>
                            </div>

                        </div>

                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                  <p class="mb-0"><span class="text-success text-sm font-weight-bolder">ပွဲချိန်ပြီး တခုပြီးတိုင်း  </span>၁၅ မိနစ်အတွင်း လုပ်ပေးရပါမည်။</p>
                </div>
              </div>
            </div>
            {{-- session reset 1 end --}}
            {{-- session reset 2 start --}}
            {{-- <div class="col-lg-6 col-md-6 col-sm-6 mb-5">
              <div class="card  mb-2 p-3">
                 <div class="d-flex mt-n2">
                            <div class="avatar avatar-xl bg-{{ $three_d_lottery_matches->is_active ? 'success' : 'danger' }} border-radius-xl p-2 mt-n4">
                              <i class="fas fa-door-{{ $three_d_lottery_matches->is_active ? 'open' : 'closed' }} fa-2x"></i>
                            </div>
                            <div class="ms-3 my-auto">
                                <h6 class="mb-0">3D Open / Close
                                </h6>
                                <div class="avatar-group mt-2">
                                  
                                        <form action="{{ route('admin.OpenCloseThreeD' , $three_d_lottery_matches->id) }}" method="post">
                                            @csrf
                                              <button class="btn mt-2" type="submit">
                                                <i class="fas fa-toggle-{{ $three_d_lottery_matches->is_active ? 'on' : 'off' }} fa-2x text-{{ $three_d_lottery_matches->is_active ? 'success' : 'danger' }}"></i>
                                              </button>
                                        </form>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                          <p class="mb-0"><span class="text-success text-sm font-weight-bolder">3D အဖွင့်အပိတ်ကို ဤနေရာတွင် လုပ်ဆောင်ရန်</p>
                        </div>
                    </div>
            </div> --}}
            {{-- session reset 2 --}}
            {{-- <div class="col-lg-6 col-md-6 col-sm-6 mb-2 mt-lg-0 mt-4">
              <div class="card  mb-2 p-3">
                <div class="d-flex mt-n2">
                            <div class="avatar avatar-xl bg-{{ $lottery_matches->is_active ? 'success' : 'danger' }} border-radius-xl p-2 mt-n4">
                              <i class="fas fa-door-{{ $lottery_matches->is_active ? 'open' : 'closed' }} fa-2x"></i>
                            </div>
                            <div class="ms-3 my-auto">
                                <h6 class="mb-0">2D Open / Close</h6>
                                <div class="avatar-group mt-2">
                                        <form action="{{ route('admin.OpenCloseTwoD' , $lottery_matches->id) }}" method="post">
                                            @csrf
                                           
                                            <button class="btn mt-2" type="submit">
                                              <i class="fas fa-toggle-{{ $lottery_matches->is_active ? 'on' : 'off' }} fa-2x text-{{ $lottery_matches->is_active ? 'success' : 'danger' }}"></i>
                                            </button>
                                        </form>
                                </div>
                            </div>
                        </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                  <p class="mb-0 "><span class="text-success text-sm font-weight-bolder">2D အဖွင့်အပိတ်ကို ဤနေရာတွင်လုပ်ဆောင်ရန်</p>
                </div>
              </div>
            </div> --}}
            <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
              <div class="card p-3">
                
                <div class="d-flex mt-n2">
                            <div class="avatar avatar-xl bg-warning border-radius-xl p-2 mt-n4">
                              <i class="fas fa-rotate fa-2x"></i>
                               
                            </div>
                            <div class="ms-3 my-auto">
                                <h6 class="mb-0"> 3D Reset</h6>
                                <div class="avatar-group" style="margin-top: 40px;">
                                   
                                 <form action="{{ route('admin.ThreeDReset') }}" method="POST">
                                      @csrf
                                      <button class="btn btn-primary" type="submit">3D Reset</button>
                                  </form>
                                </div>
                            </div>

                        </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                  <p class="mb-0"><span class="text-success text-sm font-weight-bolder">3D ထွက်ပြီး </span>၁၀ နာရီအတွင်း လုပ်ဆောင်ပေးရပါမည်။</p>
                </div>
              </div>
            </div>
           
            
          </div>
          
        {{-- fourth row end --}}
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ asset('admin_app/assets/js/plugins/chartjs.min.js')}}"></script>
{{-- pie chart --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js">
</script>
<script src="{{ asset('admin_app/assets/js/dashboard.js')}}"></script>
<script src="{{ asset('admin_app/assets/js/v_1_dashboard.js')}}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    @if(session('SuccessRequest'))
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: '{{ session("SuccessRequest") }}',
      timer: 3000,
      showConfirmButton: false
    });
    @endif

    // If you want to show an error or other types of alerts, you can add more conditions here
    @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: '{{ session("error") }}'
    });
    @endif
});

// For the reset confirmation, you can replace the native confirm with SweetAlert
$('form').on('submit', function(e) {
    e.preventDefault(); // prevent the form from submitting immediately
    var form = this;
    Swal.fire({
        title: 'Are you sure you want to reset?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // submit the form if confirmed
        }
    });
});


</script>

{{-- first chart end --}}
@endsection
