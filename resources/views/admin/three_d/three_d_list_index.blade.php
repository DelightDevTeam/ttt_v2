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
                            <h5 class="mb-0">3D All History Dashboards
                                {{-- <span>
                                    <a class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1"
                                    href="{{ url('/admin/three-digit-one-month-history-conclude') }}">တလအတွင်းပေါင်းချုပ်ကြည့်ရန်</a>
                                </span> --}}
                            </h5>

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
                    <table class="table table-flush" id="twod-search">
            <thead>
            <tr>
                <th>User Name</th>
                <th>Phone</th>
                <th>Bet Digit</th>
                <th>Result Date</th>
                <th>Result Time</th>
                <th>Sub Amount</th>
                <th>Prize Sent</th>
                <th>Match Status</th>
                <th>Match Start Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $result)
                <tr>
                    <td>{{ $result->user_name }}</td>
                    <td>{{ $result->user_phone }}</td>
                    <td>{{ $result->bet_digit }}</td>
                    <td>{{ $result->res_date }}</td>
                    <td>{{ $result->res_time }}</td>
                    <td>{{ $result->sub_amount }}</td>
                    <td>{{ $result->prize_sent ? 'Yes' : 'No' }}</td>
                    <td>{{ $result->match_status }}</td>
                    <td>{{ $result->match_start_date ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No data found.</td>
                </tr>
            @endforelse
        </tbody>
       </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <p class="text-center">
                        Total Sub Amount: {{ $totalSubAmount }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
    {{-- <script>
    const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
      searchable: true,
      fixedHeight: true
    });
  </script> --}}
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
