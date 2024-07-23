@extends('layouts.app')
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
      <h5 class="mb-0">3D Report Detail Dashboards</h5>
      {{-- <p class="text-sm mb-0">
                    A lightweight, extendable, dependency-free javascript HTML table plugin.
                  </p> --}}
     </div>
     <div class="ms-auto my-auto mt-lg-0 mt-4">
      <div class="ms-auto my-auto">
       {{-- <a href="" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; New Product</a> --}}
       {{-- <button type="button" class="btn btn-outline-primary btn-sm mb-0 py-2" data-bs-toggle="modal"
        data-bs-target="#import">
        +&nbsp; New Permission
       </button> --}}
       
       <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1 py-2" data-type="csv" type="button"
        name="button">Export</button>
      </div>
     </div>
    </div>
   </div>
   <div class="table-responsive">
    <table class="table table-flush" id="permission-search">
       <thead>
        <tr>
            <th>User Name</th>
            <th>Phone</th>
            <th>Agent ID</th>
            <th>SlipNo</th>
            <th>Bet Digit</th>
            <th>Sub Amount</th>
            <th>Res Date</th>
            <th>Res Time</th>
            <th>Play Date</th>
            <th>Play Time</th>
            <th>Match Start Date</th>
            <th>Match Status</th>
            <th>Win Lose</th>
            {{-- <th>Prize Sent</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $report)
            <tr>
                <td>{{ $report->user_name }}</td>
                <td>{{ $report->phone }}</td>
                <td>{{ $report->agent_id }}</td>
                <td>{{ $report->slip_no }}</td>
                <td>{{ $report->bet_digit }}</td>
                <td>{{ $report->sub_amount }}</td>
                <td>{{ $report->res_date }}</td>
                <td>{{ $report->res_time }}</td>
                <td>{{ $report->play_date }}</td>
                <td>{{ $report->play_time }}</td>
                <td>{{ $report->match_start_date }}</td>
                <td>{{ $report->match_status }}</td>
                <td>
                 @if($report->win_lose == 1)
                 <span>
                  Pending
                 </span>
                 @elseif($report->prize_sent == 1)
                 <span>
                  Win
                 </span>
                 @else
                 <span>
                  Reject
                  @endif
                 </span>
                </td>
                {{-- <td>{{ $report->prize_sent }}</td> --}}
            </tr>
        @endforeach
    </tbody>

    </table>
   </div>
  </div>
  <div class="card mt-2">
    <div class="card-header">
     {{-- <p class="text-center">Total Amount 3D One Week History</p> --}}
    </div>
    <div class="card-body">
     <div>
           {{-- <h4 class="text-center">Total Sub Amount: </h4> --}}
     </div>
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
if (document.getElementById('permission-search')) {
 const dataTableSearch = new simpleDatatables.DataTable("#permission-search", {
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
    data.columnDelimiter = "|";
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
