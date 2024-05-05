@extends('layouts.admin_app')
@section('styles')
    <style>
        .transparent-btn {
            background: none;
            border: none;
            padding: 0;
            outline: none;
            cursor: pointer;
            box-shadow: none;
            appearance: none;
            /* For some browsers */
        }
    </style>
@endsection
@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <!-- Card header -->
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">2D History Dashboard</h5>

                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                {{-- <a href="{{ route('admin.users.create') }}"
                                    class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; Create New
                                    User</a> --}}
                                <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1" data-type="csv"
                                    type="button" name="button">Export</button>
                            </div>
                        </div>
                    </div>
                </div>
               <div class="row">
                <div class="col-md-6">
                     <div class="card-body">
                    @php
                    $all_total = 0; // Default to zero

                    // Ensure both 'morning' and 'evening' keys exist before accessing
                    if (isset($sessionTotals['morning']) && isset($sessionTotals['evening'])) {
                        $all_total = $sessionTotals['morning'] + $sessionTotals['evening'];
                    } elseif (isset($sessionTotals['morning'])) {
                        $all_total = $sessionTotals['morning'];
                    } elseif (isset($sessionTotals['evening'])) {
                        $all_total = $sessionTotals['evening'];
                    }
                    $user = Auth::user();
                    $owner_balance = $user->balance;

                    $morningWin = $winAmounts['morning'] ?? 0;
                    $eveningWin = $winAmounts['evening'] ?? 0;

                    // Add the two values
                    $win_withdraw = $morningWin + $eveningWin; 

                @endphp
                <p class="btn btn-primary"> Owner Balance: {{ $owner_balance }}</p>
                <p class="btn btn-secondary"> Total Income : {{ $all_total }} MMK</p>
                <p class="btn btn-success">Morning Win Money - {{ $winAmounts['morning'] ?? 0 }} </p>
                 <p class="btn btn-warning">Evening Win Money - {{ $winAmounts['evening'] ?? 0 }} </p>
                 <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    @php 
                                    $profit = $owner_balance - $win_withdraw;
                                    @endphp
                                    {{ $profit }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                 </div>
                </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-3">
                        
                        <div class="card-body">
                            <!-- Form to save total income -->
                        <form action="{{ route('admin.net-income.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="total_income" value="{{ $all_total }}">
                            <button type="submit" class="btn btn-info">SaveIncomeMoney - {{ $all_total }}</button>
                        </form>

                        <!-- Form to save total win/withdraw -->
                        <form action="{{ route('admin.net-win-withdraw.update') }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="total_win_withdraw" value="{{ $win_withdraw }}">
                            <button type="submit" class="btn btn-success">SaveWinWithdraw - {{ $win_withdraw }}</button>
                        </form>

                        </div>
                    </div>
                </div>
               </div>
                <div class="table-responsive">
                    <table class="table table-flush" id="twod-search">
                        <thead>
        <tr>
            <th>စဉ်</th>
            <th>အမည်</th>
            <th>ဖုန်းနံပါတ်</th>
            <th>ဂဏန်း</th>
            <th>ထိုးကြေး</th>
            <th>Session</th>
            <th>ရက်စွဲ</th>
            <th>ထွက်မည့်အချိန်</th>
            <th>W/L</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lotteries as $index => $lottery)
                @if($lottery->session == 'morning')
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ optional($lottery->user)->name }}</td> <!-- Use optional in case user is null -->
                <td>{{ optional($lottery->user)->phone }}</td> <!-- Safely accessing user details -->
                <td>{{ $lottery->bet_digit }}</td>
                <td>{{ $lottery->sub_amount }}</td>
                <td>{{ $lottery->session }}</td>
                <td>{{ \Carbon\Carbon::parse($lottery->res_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($lottery->res_time)->format('h:i A') }}</td>
                <td>
                    @if($lottery->prize_sent == 1)
                        <span class="text-success">Win</span>
                    @else
                        <span class="text-danger">Lose</span>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-heard">
                     <h3 class="text-center"> Morning Total Sub Amount: 
                        @if($sessionTotals)
                        <span>
                         <p>Morning: {{ $sessionTotals['morning'] ?? 0 }} MMK</p>   
                        </span>
                        @else
                        <p>
                            No Data Found for this session
                        </p>
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- evening record --}}
     <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <!-- Card header -->
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">2D Evening History Dashboard</h5>

                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                {{-- <a href="{{ route('admin.users.create') }}"
                                    class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; Create New
                                    User</a> --}}
                                <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1" data-type="csv"
                                    type="button" name="button">Export</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-flush" id="twod-evening">
                        <thead>
        <tr>
            <th>စဉ်</th>
            <th>အမည်</th>
            <th>ဖုန်းနံပါတ်</th>
            <th>ဂဏန်း</th>
            <th>ထိုးကြေး</th>
            <th>Session</th>
            <th>ရက်စွဲ</th>
            <th>ထွက်မည့်အချိန်</th>
            <th>W/L</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lotteries as $index => $lottery)
                @if($lottery->session == 'evening')
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ optional($lottery->user)->name }}</td> <!-- Use optional in case user is null -->
                <td>{{ optional($lottery->user)->phone }}</td> <!-- Safely accessing user details -->
                <td>{{ $lottery->bet_digit }}</td>
                <td>{{ $lottery->sub_amount }}</td>
                <td>{{ $lottery->session }}</td>
                <td>{{ \Carbon\Carbon::parse($lottery->res_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($lottery->res_time)->format('h:i A') }}</td>
                <td>
                    @if($lottery->prize_sent == 1)
                        <span class="text-success">Win</span>
                    @else
                        <span class="text-danger">Lose</span>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-heard">
                     <h3 class="text-center">Evening Total Sub Amount: 
                        @if($sessionTotals)
                        <span>
                         <p>Evening: {{ $sessionTotals['evening'] ?? 0 }} MMK</p>   
                        </span>
                        @else
                        <p>
                            No Data Found for this session
                        </p>
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
    <script>
    if (document.getElementById('twod-evening')) {
            const dataTableSearch = new simpleDatatables.DataTable("#twod-evening", {
                searchable: true,
                fixedHeight: false,
                perPage: 7
            });
    });
  </script>
    <script>
        if (document.getElementById('twod-search')) {
            const dataTableSearch = new simpleDatatables.DataTable("#twod-search", {
                searchable: true,
                fixedHeight: false,
                perPage: 7
            });

            document.querySelectorAll(".export").forEach(function(el) {
                el.addEventListener("click", function(e) {
                    var type = el.dataset.type;

                    var data = {
                        type: type,
                        filename: "material-" + type,
                    };

                    if (type === "csv") {
                        data.columnDelimiter = ",";
                    }

                    dataTableSearch.export(data);
                });
            });
        };
    </script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
