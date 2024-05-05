@extends('frontend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-4 col-md-4 offset-lg-4 offset-md-4 mt-4 pt-4 headers" style="height:100vh">
        <div class="winner-card mt-5 p-1">
            <p class="mt-2">တစ်လအတွင်း 2D ကံထူးရှင်များ</p>
        </div>

        <div class="d-flex justify-content-between p-3">
            <p style="color: #f5bd02;">Updated at: 
                <br>
                <span class="font-weight-bold">
                    <script>
                        var d = new Date();
                        document.write(d.toLocaleString());
                    </script>
                </span>
            </p>
        </div>

        <!-- Display error message if present -->
        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        <!-- Display total prize amount if available -->
        @if(isset($totalPrizeAmount))
            <div class="total-prize text-right mt-3">
                <strong>Total Prize Amount: {{ number_format($totalPrizeAmount, 2) }} MMK</strong>
            </div>
        @endif

        <!-- Display winners' table -->
        <div class="winners-list mt-3">
            @if(isset($results) && $results->isNotEmpty())
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>2D</th>
                            <th>Amount</th>
                            <th>Prize</th>
                            <th>Date</th>
                            <th>Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $winner)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($winner->user_profile)
                                        <img src="{{ $winner->user_profile }}" width="50px" height="50px" style="border-radius: 50%;" alt=""/>
                                    @else
                                        <i class="fa-regular fa-circle-user" style="font-size: 50px;"></i>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 10px">{{ $winner->user_name }}</span>
                                    <p style="font-size: 10px">{{ $winner->user_phone }}</p>
                                </td>
                                <td>{{ $winner->bet_digit }}</td>
                                <td>{{ $winner->sub_amount }}</td>
                                <td>{{ $winner->sub_amount * 85 }}</td>
                                <td>{{ $winner->res_date }}</td>
                                <td>{{ $winner->session }}
                                    <span>
                                        {{ $winner->res_time }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No winners found for the past month.</p>
            @endif
        </div>
    </div>
</div>

@include('frontend.layouts.footer')
@endsection
