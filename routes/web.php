<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\TwoD\SlipController;
use App\Http\Controllers\Admin\TwoDLimitController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Home\TransferLogController;
use App\Http\Controllers\Admin\FillBalanceController;
use App\Http\Controllers\Admin\ThreeDLimitController;
use App\Http\Controllers\Admin\TwoDLotteryController;
use App\Http\Controllers\Home\CashInRequestController;
use App\Http\Controllers\Home\CashOutRequestController;
use App\Http\Controllers\Admin\TwoD\DataLejarController;
use App\Http\Controllers\Admin\TwoD\TwoDLagarController;
use App\Http\Controllers\Admin\FillBalanceReplyController;
use App\Http\Controllers\User\Threed\ThreeDPlayController;
use App\Http\Controllers\Admin\ThreeD\ThreeDCloseController;
use App\Http\Controllers\Admin\ThreeD\ThreeDLegarController;
use App\Http\Controllers\Admin\TwoD\CloseTwoDigitController;
use App\Http\Controllers\Admin\TwoD\NetComeIncomeController;
use App\Http\Controllers\Admin\TwoD\TwoGameResultController;
use App\Http\Controllers\Admin\ThreeD\ThreeDWinnerController;
use App\Http\Controllers\Admin\TwoD\HeadDigitCloseController;
use App\Http\Controllers\Admin\ThreeD\OneWeekRecordController;
use App\Http\Controllers\Admin\ThreeD\ThreeDOpenCloseController;
use App\Http\Controllers\Admin\ThreeD\AllWinnerHistoryController;

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [App\Http\Controllers\User\WelcomeController::class, 'index'])->name('welcome');

//auth routes
Route::get('/login', [App\Http\Controllers\User\WelcomeController::class, 'userLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\User\WelcomeController::class, 'login'])->name('login');
Route::post('/register', [App\Http\Controllers\User\WelcomeController::class, 'register'])->name('register');
Route::get('/register', [App\Http\Controllers\User\WelcomeController::class, 'userRegister'])->name('register');
//auth routes

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'App\Http\Controllers\Admin', 'middleware' => ['auth']], function () {
    // Permissions
    Route::delete('permissions/destroy', [PermissionController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', PermissionController::class);
    // Roles
    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);
    // Users
    Route::delete('users/destroy', [UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::resource('users', UsersController::class);
    Route::get('/active-users', [UsersController::class, 'ActiveUserindex']);
    Route::post('/user/change-pwd', [UsersController::class, 'UserPwdChange'])->name('pwdChange');
    Route::get('/two-d-users', [App\Http\Controllers\Admin\TwoUsersController::class, 'index'])->name('two-d-users-index');
    // details route
    Route::get('/two-d-users/{id}', [App\Http\Controllers\Admin\TwoUsersController::class, 'show'])->name('two-d-users-details');

    Route::post('/net-income/update', [NetComeIncomeController::class, 'updateNetIncome'])->name('net-income.update');
    Route::post('/net-win/with-draw', [NetComeIncomeController::class, 'updateWinWithdraw'])->name('net-win-withdraw.update');

    //Banners
    Route::resource('banners', BannerController::class);
    Route::resource('text', BannerTextController::class);
    Route::resource('games', GameController::class);
    Route::resource('/promotions', PromotionController::class);
    Route::resource('/banks', BankController::class);
    //commissions route
    Route::resource('/commissions', CommissionController::class);
    // Two Digit Limit
    Route::resource('/two-digit-limit', TwoDLimitController::class);
    // three Ditgit Limit
    Route::resource('/three-digit-limit', ThreeDLimitController::class);
    // two digit close
    Route::resource('two-digit-close', CloseTwoDigitController::class);
    // morning - lajar
    Route::get('/morning-lajar', [TwoDLagarController::class, 'showData'])->name('morning-lajar');
    // two digit data
    Route::get('/two-digit-lejar-data', [DataLejarController::class, 'showData'])->name('two-digit-lejar-data');

    // morning - lajar
    Route::get('/evening-lajar', [TwoDLagarController::class, 'showDataEvening'])->name('evening-lajar');
    // two digit data
    Route::get('/evening-two-digit-lejar-data', [DataLejarController::class, 'showDataEvening'])->name('evening-two-digit-lejar-data');
    // three digit close
    Route::resource('three-digit-close', ThreeDCloseController::class);
    // three digit legar
    Route::get('/three-digit-lejar', [ThreeDLegarController::class, 'showData'])->name('three-digit-lejar');
    // display limit
    Route::get('/three-d-display-limit-amount', [App\Http\Controllers\Admin\ThreeDLimitController::class, 'overLimit'])->name('three-d-display-limit-amount');
    Route::get('/three-d-same-id-display-limit-amount', [App\Http\Controllers\Admin\ThreeDLimitController::class, 'SameThreeDigitIDoverLimit'])->name('three-d-display-same-id-limit-amount');
    // head digit close
    Route::resource('head-digit-close', HeadDigitCloseController::class);
    //cash in lists
    Route::get('/cashIn', [CashInRequestController::class, 'index'])->name('cashIn');
    Route::get('/cashIn/{id}', [CashInRequestController::class, 'show'])->name('cashIn.show');
    Route::post('/cashIn/accept/{id}', [CashInRequestController::class, 'accept'])->name('acceptCashIn');
    Route::post('/cashIn/reject/{id}', [CashInRequestController::class, 'reject'])->name('rejectCashIn');
    Route::post('/transfer/{id}', [CashInRequestController::class, 'transfer']);
    //cash out lists
    Route::get('/cashOut', [CashOutRequestController::class, 'index'])->name('cashOut');
    Route::get('/cashOut/{id}', [CashOutRequestController::class, 'show'])->name('cashOut.show');
    Route::post('/cashOut/accept/{id}', [CashOutRequestController::class, 'accept'])->name('acceptCashOut');
    Route::post('/cashOut/reject/{id}', [CashOutRequestController::class, 'reject'])->name('rejectCashOut');
    // Route::post('/withdraw/{id}', [CashOutRequestController::class, "withdraw"]);
    //transfer logs lists
    Route::get('/transferlogs', [TransferLogController::class, 'index'])->name('transferLog');

    Route::resource('profiles', ProfileController::class);
    // user profile route get method
    Route::put('/change-password', [ProfileController::class, 'newPassword'])->name('changePassword');
    Route::put('/super-admin-update-balance/{id}', [App\Http\Controllers\Admin\ProfileController::class, 'AdminUpdateBalance'])->name('admin-update-balance');
    // PhoneAddressChange route with auth id route with put method
    Route::put('/change-phone-address', [ProfileController::class, 'PhoneAddressChange'])->name('changePhoneAddress');
    Route::put('/change-kpay-no', [ProfileController::class, 'KpayNoChange'])->name('changeKpayNo');
    Route::put('/change-join-date', [ProfileController::class, 'JoinDate'])->name('addJoinDate');
    Route::get('profile/fill_money', [ProfileController::class, 'fillmoney']);
    // kpay fill money get route
    Route::get('profile/kpay_fill_money', [ProfileController::class, 'index'])->name('kpay_fill_money');
    Route::resource('fill-balance-replies', FillBalanceReplyController::class);
    Route::get('/daily-income-json', [App\Http\Controllers\Admin\DailyTwodIncomeOutComeController::class, 'getTotalAmounts'])->name('dailyIncomeJson');
    Route::get('/with-draw-view', [App\Http\Controllers\Admin\WithDrawViewController::class, 'index'])->name('withdrawViewGet');
    Route::get('/with-draw-details/{id}', [App\Http\Controllers\Admin\WithDrawViewController::class, 'show'])->name('withdrawViewDetails');
    // withdraw update route
    Route::put('/with-draw-update/{id}', [App\Http\Controllers\Admin\WithDrawViewController::class, 'update'])->name('withdrawViewUpdate');
    Route::get('/daily-with-name-income-json', [App\Http\Controllers\Admin\DailyTwodIncomeOutComeController::class, 'getTotalAmountsDaily'])->name('getTotalAmountsDaily');
    // week name route
    Route::get('/weekly-income-json', [App\Http\Controllers\Admin\DailyTwodIncomeOutComeController::class, 'getTotalAmountsWeekly'])->name('getTotalAmountsWeekly');
    // month name route
    Route::get('/month-with-name-income-json', [App\Http\Controllers\Admin\DailyTwodIncomeOutComeController::class, 'getTotalAmountsMonthly'])->name('getTotalAmountsMonthly');
    // year name route
    Route::get('/yearly-income-json', [App\Http\Controllers\Admin\DailyTwodIncomeOutComeController::class, 'getTotalAmountsYearly'])->name('getTotalAmountsYearly');
    Route::get('/get-two-d-session-reset', [App\Http\Controllers\Admin\SessionResetControlller::class, 'index'])->name('SessionResetIndex');
    Route::post('/two-d-session-reset', [App\Http\Controllers\Admin\SessionResetControlller::class, 'SessionReset'])->name('SessionReset');
    Route::get('/close-two-d', [App\Http\Controllers\Admin\CloseTwodController::class, 'index'])->name('CloseTwoD');

    Route::post('/update-open-close-two-d/{id}', [App\Http\Controllers\Admin\CloseTwodController::class, 'closeTwoD'])->name('OpenCloseTwoD');
    Route::post('/update-open-close-three-d/{id}', [ThreeDOpenCloseController::class, 'closeThreeD'])->name('OpenCloseThreeD');

    // 3d prize number create
    Route::get('/three-d-prize-number-create', [App\Http\Controllers\Admin\ThreeD\ThreeDPrizeNumberCreateController::class, 'index'])->name('three-d-prize-number-create');
    // store_permutations
    Route::post('/store-permutations', [App\Http\Controllers\Admin\ThreeD\ThreeDPrizeNumberCreateController::class, 'PermutationStore'])->name('storePermutations');
    //deletePermutation
    Route::delete('/delete-permutation/{id}', [App\Http\Controllers\Admin\ThreeD\ThreeDPrizeNumberCreateController::class, 'deletePermutation'])->name('deletePermutation');
    Route::post('/three-d-prize-number-create', [App\Http\Controllers\Admin\ThreeD\ThreeDPrizeNumberCreateController::class, 'store'])->name('three-d-prize-number-create.store');

    // 3d history
    // Route::get('/three-d-history', [App\Http\Controllers\Admin\ThreeD\ThreeDOneWeekHistoryController::class, 'GetAllThreeDUserData'])->name('three-d-history');
    Route::get('/3d-one-week-records', [OneWeekRecordController::class, 'showRecordsForOneWeek'])->name('oneWeekRec');

    Route::get('/3d-all-history', [OneWeekRecordController::class, 'showRecords'])->name('AllHistory');

    Route::get('/3d-one-week-slip', [OneWeekRecordController::class, 'index'])->name('OneWeekSlipIndex');
    Route::get('/3d-oneweek-slip-no/{userId}/{slipNo}', [OneWeekRecordController::class, 'show'])->name('OneWeekSlipDetail');

    Route::get('/3d-slip-history', [OneWeekRecordController::class, 'indexAllSlip'])->name('SlipHistoryIndex');
    Route::get('/3d-slip-no-history/{userId}/{slipNo}', [OneWeekRecordController::class, 'showAllSlip'])->name('SlipHistoryShow');

    // three d list index
    Route::get('/three-d-list-index', [App\Http\Controllers\Admin\ThreeD\ThreeDListController::class, 'GetAllThreeDData'])->name('threedlist-index');

    // 3d winner list
    Route::get('/three-d-winner', [App\Http\Controllers\Admin\ThreeD\ThreeDWinnerController::class, 'index'])->name('three-d-winner');
    Route::post('/three-d-reset', [App\Http\Controllers\Admin\ThreeD\ThreeDResetController::class, 'ThreeDReset'])->name('ThreeDReset');
    Route::resource('twod-records', TwoDLotteryController::class);
    // Route::get('/two-d-morning-winner', [App\Http\Controllers\Admin\TwoDMorningWinnerController::class, 'MorningWinHistoryForAdmin'])->name('morningWinner');

    Route::get('/two-d-all-winner', [App\Http\Controllers\Admin\TwoD\AllLotteryWinPrizeSentController::class, 'TwoAllWinHistoryForAdmin']);
    Route::post('/permutation-reset', [App\Http\Controllers\Admin\ThreeD\PermutationResetController::class, 'PermutationReset'])->name('PermutationReset');

    // three digit history conclude
    Route::get('/three-digit-history-conclude', [App\Http\Controllers\Admin\ThreeD\ThreeDRecordHistoryController::class, 'OnceWeekThreedigitHistoryConclude'])->name('ThreeDigitHistoryConclude');
    // three digit one month history conclude
    Route::get('/three-digit-one-month-history-conclude', [App\Http\Controllers\Admin\ThreeD\ThreeDRecordHistoryController::class, 'OnceMonthThreedigitHistoryConclude'])->name('ThreeDigitOneMonthHistoryConclude');
    // three d winners history
    // Route::get('/three-d-win-history', [App\Http\Controllers\Admin\ThreeD\ThreeDWinnerController::class, 'FirstPrizeWinner'])->name('ThreeDWinnersHistory');


    Route::get('/3d-first-winner', [ThreeDWinnerController::class, 'ThreeDFirstWinner'])->name('WinnerFirst');
    Route::get('/3d-second-winner', [ThreeDWinnerController::class, 'ThreeDSecondWinner'])->name('WinnerSecond');
    Route::get('/3d-third-winner', [ThreeDWinnerController::class, 'ThreeDThirdWinner'])->name('WinnerThird');

    Route::get('/3d-all-first-winner', [AllWinnerHistoryController::class, 'ThreeDFirstWinner'])->name('WinnerFirst');
    Route::get('/3d-all-second-winner', [AllWinnerHistoryController::class, 'ThreeDSecondWinner'])->name('WinnerSecond');
    Route::get('/3d-all-third-winner', [AllWinnerHistoryController::class, 'ThreeDThirdWinner'])->name('WinnerThird');


    // three d permutation winners history
    Route::get('/permutation-winners-history', [App\Http\Controllers\Admin\ThreeD\PermutationWinnerController::class, 'PermutationWinners'])->name('PermutationWinnersHistory');
    // greater than less than winner prize
    Route::resource('winner-prize', App\Http\Controllers\Admin\ThreeD\GreatherThanLessThanWinnerPrizeController::class);
    // three d permutation winner prize
    Route::get('/prize-winners', [App\Http\Controllers\Admin\ThreeD\GreatherThanLessThanWinnerPrizeController::class, 'ThirdPrizeWinner'])->name('getPrizeWinnersHistory');
    // two d winner history
    Route::get('/evening-two-d-win-history', [App\Http\Controllers\Admin\TwoD\TwoDMorningWinnerController::class, 'EveningWinHistoryForAdmin'])->name('Eveninig_winHistory');
    Route::get('/admin-two-d-winners-history-group-by-session', [App\Http\Controllers\Admin\TwoDWinnerHistoryController::class, 'getWinnersHistoryForAdminGroupBySession'])->name('winnerHistoryForAdminSession');

    // two d commission route
    Route::get('/two-d-commission', [App\Http\Controllers\Admin\Commission\TwoDCommissionController::class, 'getTwoDTotalAmountPerUser'])->name('two-d-commission');

    // show details
    Route::get('/two-d-commission-show/{id}', [App\Http\Controllers\Admin\Commission\TwoDCommissionController::class, 'show'])->name('two-d-commission-show');
    Route::put('/two-d-commission-update/{id}', [App\Http\Controllers\Admin\Commission\TwoDCommissionController::class, 'update'])->name('two-d-commission-update');
    // commission update
    Route::post('two-d-transfer-commission/{id}', [App\Http\Controllers\Admin\Commission\TwoDCommissionController::class, 'TwoDtransferCommission'])->name('two-d-transfer-commission');

    // three d commission route
    Route::get('/three-d-commission', [App\Http\Controllers\Admin\Commission\ThreeDCommissionController::class, 'getThreeDTotalAmountPerUser'])->name('three-d-commission');
    // show details
    Route::get('/three-d-commission-show/{id}', [App\Http\Controllers\Admin\Commission\ThreeDCommissionController::class, 'show'])->name('three-d-commission-show');
    // three_d_commission_update
    Route::put('/three-d-commission-update/{id}', [App\Http\Controllers\Admin\Commission\ThreeDCommissionController::class, 'update'])->name('three-d-commission-update');
    // transfer commission route
    Route::post('/three-d-transfer-commission/{id}', [App\Http\Controllers\Admin\Commission\ThreeDCommissionController::class, 'ThreeDtransferCommission'])->name('three-d-transfer-commission');

    // two d result date and result number
    Route::get('two-d-result-date', [App\Http\Controllers\Admin\TwoD\TwoGameResultController::class, 'index']);
     Route::get('two-d-more-setting', [App\Http\Controllers\Admin\TwoD\TwoGameResultController::class, 'getCurrentMonthResults']);

    Route::patch('/two-2-results/{id}/status', [App\Http\Controllers\Admin\TwoD\TwoGameResultController::class, 'updateStatus'])
        ->name('twodStatusOpenClose');
    Route::patch('/two-2-status/{id}/evening', [TwoGameResultController::class, 'updateStatusEvening'])->name('twodStatusOpenCloseEvening');

    Route::patch('/two-d-results/{id}/status', [App\Http\Controllers\Admin\TwoD\TwoGameResultController::class, 'updateResultNumber'])
        ->name('update_result_number');

    // get three d result date
    Route::get('three-d-result-date', [App\Http\Controllers\Admin\ThreeD\ResultDateController::class, 'index']);
    Route::get('three-d-more-setting', [App\Http\Controllers\Admin\ThreeD\ResultDateController::class, 'getCurrentMonthResultsSetting']);
    // result date update
    Route::patch('/lottery-results/{id}/status', [App\Http\Controllers\Admin\ThreeD\ResultDateController::class, 'updateStatus'])
        ->name('ThreedOpenClose');

    Route::patch('/three-d-admin-log/{id}/status', [App\Http\Controllers\Admin\ThreeD\ResultDateController::class, 'AdminLogThreeDOpenClose'])
        ->name('ThreeDAdminLogOpenClose');

    Route::patch('/three-d-user-log/{id}/status', [App\Http\Controllers\Admin\ThreeD\ResultDateController::class, 'UserLogThreeDOpenClose'])
        ->name('ThreeDUserLogOpenClose');
    Route::patch('/three-d-results/{id}/status', [App\Http\Controllers\Admin\ThreeD\ResultDateController::class, 'updateResultNumber'])
        ->name('UpdateResult_number');

    Route::get('/tow-d-morning-number', [App\Http\Controllers\Admin\TwoD\MorningLotteryAdminLogController::class, 'MorningAdminLogOpenData']);

    Route::get('/two-d-morning-winner', [App\Http\Controllers\Admin\TwoD\TwoDMorningWinnerController::class, 'MorningWinHistoryForAdmin'])->name('morningWinner');

    Route::get('/two-d-all-winner', [App\Http\Controllers\Admin\TwoD\AllLotteryWinPrizeSentController::class, 'TwoAllWinHistoryForAdmin']);

    Route::get('/two-d-evening-admin-log', [App\Http\Controllers\Admin\TwoD\EveningLotteryAdminLogController::class, 'showAdminLogOpenData'])->name('towDadminLog');

    Route::get('/2d-morning-slip', [SlipController::class, 'index'])->name('MorningSlipIndex');
    Route::get('/2d-morningslip/{userId}/{slipNo}', [SlipController::class, 'show'])->name('MorningSlipShow');

    Route::get('/2d-morning-all-slip', [SlipController::class, 'AllSlipForMorningindex'])->name('MorningAllSlipIndex');

    Route::get('/2d-morningallslip/{userId}/{slipNo}', [SlipController::class, 'MorningAllSlipshow'])->name('MorningAllSlipShow');

    Route::get('/2d-evening-slip', [SlipController::class, 'Eveningindex'])->name('EveningSlipIndex');
    Route::get('/2d-eveningslip/{userId}/{slipNo}', [SlipController::class, 'Eveningshow'])->name('EveningSlipShow');

    Route::get('/2d-evening-all-slip', [SlipController::class, 'AllSlipForEveningindex'])->name('EveningAllSlipIndex');
    Route::get('/2d-eveningallslip/{userId}/{slipNo}', [SlipController::class, 'EveningAllSlipshow'])->name('EveningAllSlipShow');

});

Route::group(['prefix' => 'user', 'as' => 'user.', 'namespace' => 'App\Http\Controllers\User', 'middleware' => ['auth']], function () {

    //profile management
    Route::put('editProfile/{profile}', [ProfileController::class, 'update'])->name('editProfile');
    Route::post('editInfo', [ProfileController::class, 'editInfo'])->name('editInfo');
    Route::post('changePassword', [ProfileController::class, 'changePassword'])->name('changePassword');
    //profile management
    // winner history route
    Route::get('/threed-winners-histories', [App\Http\Controllers\User\WinnerAuthUserDisplayController::class, 'displayWinners'])->name('threed_winners_histories');
    Route::get('/dashboard', [App\Http\Controllers\User\WelcomeController::class, 'dashboard'])->name('dashboard');

    Route::get('/two-d-play-index', [App\Http\Controllers\User\TwoD\PlayController::class, 'playindex'])->name('twod-play-index');
    // 9:00 am index
    // 12:00 pm index
    Route::get('/two-d-play-index-12pm', [App\Http\Controllers\User\PM12\TwodPlay12PMController::class, 'index'])->name('twod-play-index-12pm');
    // 12:00 pm confirm page
    Route::get('/two-d-play-12-1-morning-confirm', [App\Http\Controllers\User\PM12\TwodPlay12PMController::class, 'play_confirm'])->name('twod-play-confirm-12pm');
    // store
    Route::post('/two-d-play-index-12pm', [App\Http\Controllers\User\TwoD\PlayController::class, 'store'])->name('twod-play-12pm.store');

    // 2:00 pm index

    // 4:00 pm index
    Route::get('/two-d-play-session', [App\Http\Controllers\User\TwoD\PlayController::class, 'index'])->name('twod-play-session-time');
    // 2:00 pm confirm page
    Route::get('/two-d-play-confirm', [App\Http\Controllers\User\TwoD\PlayController::class, 'play_confirm'])->name('twod-play-confirm-4pm');
    // store
    Route::post('/two-d-play-4pm', [App\Http\Controllers\User\TwoD\PlayController::class, 'store'])->name('twod-playing-4pm.store');

    // qick play 9:00 am index
    Route::get('/two-d-quick-play-index', [App\Http\Controllers\User\TwoD\PlayController::class, 'Quickindex'])->name('twod-quick-play-index');

    Route::get('/two-d-play-quick-confirm', [App\Http\Controllers\User\TwoD\PlayController::class, 'quick_play_confirm'])->name('twod-play-confirm-quick');

    // money transfer
    Route::get('/wallet-deposite', [App\Http\Controllers\User\FillBalance\FillBalanceController::class, 'index'])->name('deposite-wallet');

    //deposit
    Route::get('/fill-balance', [App\Http\Controllers\User\FillBalance\FillBalanceController::class, 'topUpWallet'])->name('topUpWallet');
    Route::get('/fill-balance-top-up-submit/{id}', [App\Http\Controllers\User\FillBalance\FillBalanceController::class, 'topUpSubmit'])->name('topUpSubmit');
    Route::post('/deposit', [CashInRequestController::class, 'deposit'])->name('deposit');
    //deposit

    //withdraw
    Route::get('/withdraw-money', [App\Http\Controllers\User\FillBalance\FillBalanceController::class, 'withdrawBalance'])->name('withdrawBalance');
    Route::get('/withdraw/{id}', [App\Http\Controllers\User\FillBalance\FillBalanceController::class, 'withdrawBank'])->name('withdrawBank');
    Route::post('/withdraw', [CashOutRequestController::class, 'withdraw'])->name('withdraw');
    //withdraw

    //transferlog
    Route::get('/transferlogs', [TransferLogController::class, 'log'])->name('transferLog');

    // money transfer end

    // two d winner history
    Route::get('/two-d-winners-history', [App\Http\Controllers\User\WinHistory\TwoDWinnerHistoryController::class, 'winnerHistory'])->name('winnerHistory');

    // twod-dream-book
    Route::get('/two-d-dream-book', [App\Http\Controllers\User\Dream\TwodDreamBookController::class, 'index'])->name('two-d-dream-book-index');

    // three d
    Route::get('/three-d-play-index', [ThreeDPlayController::class, 'index'])->name('three-d-play-index');
    // three d choice play
    Route::get('/three-d-choice-play-index', [ThreeDPlayController::class, 'choiceplay'])->name('three-d-choice-play');
    // three d choice play confirm
    Route::get('/three-d-choice-play-confirm', [ThreeDPlayController::class, 'confirm_play'])->name('three-d-choice-play-confirm');
    // three d choice play store
    Route::post('/three-d-choice-play-store', [ThreeDPlayController::class, 'store'])->name('three-d-choice-play-store');
    // display three d play
    Route::get('/three-d-display', [ThreeDPlayController::class, 'getLottoDataForCurrentMonth'])->name('display');
    // three d dream book
    Route::get('/three-d-dream-book', [App\Http\Controllers\User\Threed\ThreeDreamBookController::class, 'index'])->name('three-d-dream-book-index');
    // three d winner history
    Route::get('/three-d-winners-history', [App\Http\Controllers\User\Threed\ThreedWinnerHistoryController::class, 'index'])->name('three-d-winners-history');

    

});

Route::get('/register', [App\Http\Controllers\User\WelcomeController::class, 'userRegister'])->name('register');
Route::get('/service', [App\Http\Controllers\User\WelcomeController::class, 'servicePage'])->name('service');
Route::get('/twoDPrize', [App\Http\Controllers\User\WelcomeController::class, 'twoDPrize'])->name('twoDPrize');
Route::get('/twod-live', [App\Http\Controllers\User\WelcomeController::class, 'twodLive']);
Route::get('/twod-calendar', [App\Http\Controllers\User\WelcomeController::class, 'twodCalendar']);
Route::get('/twod-holiday', [App\Http\Controllers\User\WelcomeController::class, 'twodHoliday']);
Route::get('/comment', [App\Http\Controllers\User\WelcomeController::class, 'comment']);
Route::get('/inviteCode', [App\Http\Controllers\User\WelcomeController::class, 'inviteCode']);
Route::get('/changePassword', [App\Http\Controllers\User\WelcomeController::class, 'changePassword']);
// promotion route
Route::get('/promotion', [App\Http\Controllers\User\WelcomeController::class, 'promotion']);
// promotion detail
Route::get('/promotion-detail/{id}', [App\Http\Controllers\User\WelcomeController::class, 'promotionDetail'])->name('promotionDetail');
// Route::get('/promotion-detail/{id}', [App\Http\Controllers\User\WelcomeController::class, 'promotionDetail'])->name('promotionDetail');

// Route::get('/myBank', [App\Http\Controllers\User\WelcomeController::class, 'myBank']);

// Route::get('/3d', [App\Http\Controllers\User\WelcomeController::class, 'threeD']);
// Route::get('/3dBet', [App\Http\Controllers\User\WelcomeController::class, 'threedBet']);
Route::get('/3dHistory', [App\Http\Controllers\User\WelcomeController::class, 'threedHistory']);

//Route::get('/3dWinnerHistory', [App\Http\Controllers\User\WelcomeController::class, 'threedWinner']);

Route::get('/testing', [TestController::class, 'index']);