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
     <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Result Number</th>
            <th>Session</th>
            <th>Status</th>
            <th>Open/Close</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($results as $result)
            <tr>
                <td>{{ $result->result_date }}</td>
                <td>{{ $result->result_time }}</td>
                <td>{{ $result->result_number ?? 'N/A' }}</td>
                <td>{{ ucfirst($result->session) }}</td>
                <td>{{ ucfirst($result->status) }}</td>
                <td>
                <button class="toggle-status"
                        data-id="{{ $result->id }}"
                        data-status="{{ $result->status === 'open' ? 'closed' : 'open' }}">
                    Open/Close
                </button>
                
            </td>
            </tr>
        @endforeach
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