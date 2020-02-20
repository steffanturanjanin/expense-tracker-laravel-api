<?php

namespace App\Http\Controllers;

use App\Category;
use App\Expense;
use App\Http\Resources\ExpenseCollection;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Expense as ExpenseResource;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $expenses = $user->expenses;
        return response()->json(ExpenseResource::collection($expenses));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param int $year
     * @param int $month
     * @return \Illuminate\Http\Response
     */
    public function getExpensesByMonth(Request $request, int $year, int $month)
    {
        $expenses = Expense::query()
            ->where('user_id', '=', $request->user()->id)
            ->whereYear('date', strval($year))
            ->whereMonth('date', strval($month))
            ->get();

        return response()->json(ExpenseResource::collection($expenses));
    }

    public function getDateExpensesByMonth(Request $request, int $year, int $month)
    {
        $monthName = DateTime::createFromFormat('!m', $month)->format('F');
        $numberOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $date = [$year => ['number' => $month, 'name' => $monthName, 'days' => []]];

        for ($i = 1; $i <= $numberOfDaysInMonth; $i++) {
            //$date[$year]['days'][$i]['expenses'] = [];
            $date[$year]['days'][] = ['expenses' => []];
        }

        $expenses = $expenses = DB::table('expenses')
            ->where('user_id', '=', $request->user()->id)
            ->whereYear('date', strval($year))
            ->whereMonth('date', strval($month))
            ->get();

        foreach ($expenses as $expense) {
            $dayNumber = Carbon::createFromFormat('Y-m-d H:i:s', $expense->date)->day;
            $date[$year]['days'][$dayNumber-1]['expenses'][] = $expense;
        }

        foreach ($date[$year]['days'] as &$day) {
            $day["expenses"] = ExpenseResource::collection($day["expenses"]);
        }

        return response()->json($date);
    }

    public function getDateExpensesByYearAndMonth(Request $request)
    {
        $user = $request->user();

        $timeline = $this->months($request);
        $expenses = $user->expenses;

        foreach ($expenses as $expense) {
            $date = new Carbon($expense->date);
            $year = $date->year;
            $month = $date->month;
            $timeline[$year][$month-1]['expenses'][] = $expense;
        }

        foreach ($timeline as &$year) {
            foreach ($year as &$month) {
                $month['expenses'] = ExpenseResource::collection($month['expenses']);
            }
        }

        return response()->json($timeline);
    }

    public function months(Request $request)
    {
        $user = $request->user();
        $startDate =  DB::table('expenses')->where('user_id', '=', $user->id)->min('date');
        $endDate = DB::table('expenses')->where('user_id', '=', $user->id)->max('date');

        $start = (new DateTime($startDate))->modify('first day of this month');
        $end = (new DateTime($endDate))->modify('first day of next month');

        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        $timeline = [];

        foreach ($period as $dt) {
            $timeline[intval($dt->format("Y"))][] = ['number' => intval($dt->format("m")), 'name' => ($dt->format("F")), 'expenses' => []];
        }

        return $timeline;
    }

    public function getMonths(Request $request)
    {
        $user = $request->user();
        $startDate =  DB::table('expenses')->where('user_id', '=', $user->id)->min('date');
        $endDate = DB::table('expenses')->where('user_id', '=', $user->id)->max('date');

        $start = (new DateTime($startDate))->modify('last day of this month');
        $end = (new DateTime($endDate))->modify('first day of next month');

        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        $months = [];

        foreach ($period as $dt) {
            $months[intval($dt->format("Y"))][] = ['number' => intval($dt->format("m")), 'name' => ($dt->format("F"))];
        }
        return response()->json($months);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | ExpenseResource
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => $request->type === 0 ? 'required' : '',
            'name' => 'required|min:6',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|numeric|min:0|max:1',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors(), 409);
        }

        $expense = new Expense();
        $expense->category_id = $request->type === 1 ? null : $request->category;
        $expense->user_id = $request->user()->id;
        $expense->name = $request->name;
        $expense->amount = $request->amount;
        $expense->type = $request->type;
        $expense->date = Carbon::now()->toDateTimeString();

        $expense->save();

        return response()->json(new ExpenseResource($expense));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $expense = Expense::findOrFail($id);
        if ($expense->user->id === Auth::id()) {
            $expense->delete();
            return response()->json(new ExpenseResource($expense));
        } else {
            return response()->json(['authorization' => 'You are not authorized to delete this entry'], 401);
        }
    }
}
