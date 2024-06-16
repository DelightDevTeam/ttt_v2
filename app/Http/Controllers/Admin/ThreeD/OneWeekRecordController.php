<?php

namespace App\Http\Controllers\Admin\ThreeD;

use Illuminate\Http\Request;
use App\Models\ThreeDigit\Lotto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ThreeD\ThreedSetting;
use App\Models\ThreeDigit\ResultDate;
use App\Services\LottoHistoryRecordService;
use App\Services\LottoOneWeekRecordService;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;

class OneWeekRecordController extends Controller
{
    protected $lottoService;
    protected $records;

    public function __construct(LottoOneWeekRecordService $lottoService, LottoHistoryRecordService $records)
    {
        $this->lottoService = $lottoService;
        $this->records = $records;
    }


    

    public function showRecords()
    {
        $data = $this->records->GetRecord();
        //$total_sub_amount = $this->lottoService->GetRecordForOneWeek();

        return view('admin.three_d.history.all_history', compact('data'));
    }


    public function index()
    {
        // Get the match start date and result date from ThreedSetting
        $draw_date = ResultDate::where('status', 'open')->first();
        $start_date = $draw_date->match_start_date;
        $end_date = $draw_date->result_date;

        // Retrieve and group records by user_id within the specified date range
        $records = LotteryThreeDigitPivot::with('user')
            ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
            ->whereBetween('lotto_three_digit_pivot.match_start_date', [$start_date, $end_date])
            ->whereBetween('lotto_three_digit_pivot.res_date', [$start_date, $end_date])
            ->select('lotto_three_digit_pivot.user_id', 'lottos.slip_no', DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_sub_amount'))
            ->groupBy('lotto_three_digit_pivot.user_id', 'lottos.slip_no')
            ->get();
        // Calculate the total amount from the lottos table within the date range
        $total_amount = Lotto::whereBetween('created_at', [$start_date, $end_date])
            ->sum('total_amount');
        //return response()->json($records, $total_amount);

        // You can return the records to your view
        return view('admin.three_d.records.one_week_slip', compact('records', 'total_amount'));
    }

    public function show($user_id, $slip_no)
    {
        // Retrieve records for a specific user_id and slip_no
        $records = LotteryThreeDigitPivot::with('user')
            ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
            ->where('lotto_three_digit_pivot.user_id', $user_id)
            ->where('lottos.slip_no', $slip_no)
            ->select('lotto_three_digit_pivot.*', 'lottos.slip_no')
            ->get();

        // Calculate the total sub_amount for the specific user_id and slip_no
        $total_sub_amount = $records->sum('sub_amount');

        return view('admin.three_d.records.slip_show', compact('records', 'total_sub_amount', 'slip_no', 'user_id'));
    }

    public function showRecordsForOneWeek()
    {
        $data = $this->lottoService->GetRecordForOneWeek();
        //$total_sub_amount = $this->lottoService->GetRecordForOneWeek();

        return view('admin.three_d.records.one_week_rec', compact('data'));
    }


    public function indexAllSlip()
    {
        // try {


            // Retrieve records with the user's name
            $records = LotteryThreeDigitPivot::with('user')
                ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
                ->select('lotto_three_digit_pivot.user_id', 'lottos.slip_no', DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_sub_amount'))
                ->groupBy('lotto_three_digit_pivot.user_id', 'lottos.slip_no')
                ->get();

            // Check if records are found
            if ($records->isEmpty()) {
                return response()->json(['error' => 'No records found'], 404);
            }

            // Calculate the total amount from the lottos table
            $total_amount = Lotto::sum('total_amount');

            // Return the view with records and total amount
            return view('admin.three_d.history.index', [
                'records' => $records,
                'total_amount' => $total_amount,
            ]);

        // } catch (\Exception $e) {
        //     // Log the exception
        //     Log::error('Error retrieving records. Error: '.$e->getMessage());

        //     // Error message
        //     return response()->json(['error' => 'Failed to retrieve records'], 500);
        // }
    }

    public function showAllSlip($user_id, $slip_no)
    {
        try {
            

            // Retrieve records for a specific user_id and slip_no with the user's name
            $records = LotteryThreeDigitPivot::with('user')
                ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
                ->where('lotto_three_digit_pivot.user_id', $user_id)
                ->where('lottos.slip_no', $slip_no)
                ->select('lotto_three_digit_pivot.*', 'lottos.slip_no', 'users.name as user_name')
                ->join('users', 'lotto_three_digit_pivot.user_id', '=', 'users.id')
                ->get();

            // Check if records are found
            if ($records->isEmpty()) {
                return response()->json(['error' => 'No records found'], 404);
            }

            // Calculate the total sub_amount for the specific user_id and slip_no
            $total_sub_amount = $records->sum('sub_amount');

            // Return the view with records and total sub_amount
            return view('admin.three_d.history.show', [
                'records' => $records,
                'total_sub_amount' => $total_sub_amount,
                'slip_no' => $slip_no,
                'user_id' => $user_id,
            ]);

        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error retrieving records for user_id: '.$user_id.' and slip_no: '.$slip_no.'. Error: '.$e->getMessage());

            // Error message
            return response()->json(['error' => 'Failed to retrieve records'], 500);
        }
    }
}
