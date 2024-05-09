@extends('layouts.admin_app')
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
      <h5 class="mb-0">2D Opening Date & Time Dashboards</h5>
      {{-- <p class="text-sm mb-0">
                    A lightweight, extendable, dependency-free javascript HTML table plugin.
                  </p> --}}
     </div>
     <div class="ms-auto my-auto mt-lg-0 mt-4">
      <div class="ms-auto my-auto">
       {{-- <a href="" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; New Product</a> --}}
       {{-- <button type="button" class="btn btn-outline-primary btn-sm mb-0 py-2" data-bs-toggle="modal"
        data-bs-target="#import">
        +&nbsp; Update Permission
       </button> --}}
      
       <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1 py-2" data-type="csv" type="button"
        name="button">Export</button>
      </div>
     </div>
    </div>
   </div>
   <div class="table-responsive">
    <div class="card">
        <div class="card-header">
            <p class="text-center">
                Morning Session - 12:1 PM
            </p>
        </div>
    </div>
    <table class="table table-flush" id="permission-search">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>Opening Date</th>
            <th>Opening Time</th>
            <th>Result Number</th>
            <th>Prize Number</th>
            <th>Status</th>
            <th>Session</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        @if ($morningResult)
        <tr>
            <td class="text-sm font-weight-normal">1</td>
            <td class="text-sm font-weight-normal">{{ $morningResult->result_date }}</td>
            <td class="text-sm font-weight-normal">{{ $morningResult->result_time }}</td>
            <td class="text-sm font-weight-normal">{{ $morningResult->result_number ?? 'Pending' }}</td>
            <td>
                <form method="POST" action="{{ route('admin.update_result_number', ['id' => $morningResult->id]) }}">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="result_number" placeholder="Enter result number" required class="form-control">
                    <button type="submit" class="btn btn-primary">Create Prize Number</button>
                </form>
            </td>
            <td class="text-sm font-weight-normal">{{ ucfirst($morningResult->status) }}</td>
            <td class="text-sm font-weight-normal">{{ ucfirst($morningResult->session) }}</td>
            <td>
                <button class="toggle-status"
                        data-id="{{ $morningResult->id }}"
                        data-status="{{ $morningResult->status === 'open' ? 'closed' : 'open' }}">
                    Open/Close
                </button>
                
            </td>
        </tr>
        @else
        <tr>
            <td colspan="8" class="text-center">No results found for today and current session.</td>
        </tr>
        @endif
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
                        Evening Session - 4:30 PM
                    </p>
                </div>
            </div>
            . <div class="table-responsive">
    <table class="table table-flush" id="permission-search">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>Opening Date</th>
            <th>Opening Time</th>
            <th>Result Number</th>
            <th>Prize Number</th>
            <th>Status</th>
            <th>Session</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        @if ($eveningResult)
        <tr>
            <td class="text-sm font-weight-normal">1</td>
            <td class="text-sm font-weight-normal">{{ $eveningResult->result_date }}</td>
            <td class="text-sm font-weight-normal">{{ $eveningResult->result_time }}</td>
            <td class="text-sm font-weight-normal">{{ $eveningResult->result_number ?? 'Pending' }}</td>
            <td>
                <form method="POST" action="{{ route('admin.update_result_number', ['id' => $eveningResult->id]) }}">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="result_number" placeholder="Enter result number" required class="form-control">
                    <button type="submit" class="btn btn-primary">Create Prize Number</button>
                </form>
            </td>
            <td class="text-sm font-weight-normal">{{ ucfirst($eveningResult->status) }}</td>
            <td class="text-sm font-weight-normal">{{ ucfirst($eveningResult->session) }}</td>
            <td>
                <!-- Toggle button to update status -->
                <button class="toggle-status-evening"
                        data-id="{{ $eveningResult->id }}"
                        data-status="{{ $eveningResult->status === 'open' ? 'closed' : 'open' }}">
                    Open/Close
                </button>
            </td>
        </tr>
        @else
        <tr>
            <td colspan="8" class="text-center">No results found for today and current session.</td>
        </tr>
        @endif
    </tbody>
</table>

   </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Include CSRF token in AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.toggle-status').on('click', function() {
        const resultId = $(this).data('id'); // The ID of the result
        const newStatus = $(this).data('status'); // The new status to set

        // Ask for confirmation before changing the status
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to change the status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/two-2-results/' + resultId + '/status', // Your route
                    method: 'PATCH',
                    data: {
                        status: newStatus,
                    },
                    success: function(response) {
                        // Display success message with SweetAlert
                        Swal.fire('Updated!', response.message, 'success');
                        // Optional: Update the status on the page
                        $('#status-' + resultId).text(newStatus);
                        // Auto-reload the page after a brief delay
                        setTimeout(function() {
                            location.reload();
                        }, 1500); // 1500 milliseconds = 1.5 seconds
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        Swal.fire('Error', 'Failed to update status.', 'error');
                    }
                });
            }
        });
    });
});
</script>


<script>
$(document).ready(function() {
    // Include CSRF token in AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.toggle-status-evening').on('click', function() {
        const resultId = $(this).data('id'); // The ID of the result
        const newStatus = $(this).data('status'); // The new status to set

        // Ask for confirmation before changing the status
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to change the status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/two-2-results/' + resultId + '/status', // Your route
                    method: 'PATCH',
                    data: {
                        status: newStatus,
                    },
                    success: function(response) {
                        // Display success message with SweetAlert
                        Swal.fire('Updated!', response.message, 'success');
                        // Optional: Update the status on the page
                        $('#status-' + resultId).text(newStatus);
                        // Auto-reload the page after a brief delay
                        setTimeout(function() {
                            location.reload();
                        }, 1500); // 1500 milliseconds = 1.5 seconds
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        Swal.fire('Error', 'Failed to update status.', 'error');
                    }
                });
            }
        });
    });
});
</script>


<script>
$(document).ready(function() {
    // Include CSRF token in AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.toggle-status-evening').on('click', function() {
        const resultId = $(this).data('id'); // The ID of the result
        const newStatus = $(this).data('status'); // The new status to set

        $.ajax({
            url: '/admin/two-2-results/' + resultId + '/status', // Your route
            method: 'PATCH',
            data: {
                status: newStatus,
            },
            success: function(response) {
                alert(response.message);
                // Optional: Update the status on the page
                $('#status-' + resultId).text(newStatus);
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to update status.');
            }
        });
    });
});
</script>



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