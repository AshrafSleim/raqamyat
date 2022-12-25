<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function getTransactions(TransactionRequest $request)
    {

        $fileDataW = file_get_contents($request->DataW);
        $fileDataX = file_get_contents($request->DataX);
        $fileDataY = file_get_contents($request->DataY);
        json_decode(file_get_contents($request->DataW), true);
        if (!Str::isJson($fileDataW) || !Str::isJson($fileDataX) || !Str::isJson($fileDataY)) {
            return response()->json(['status' => false, 'message' => "the 3 files must be json data",], 200);
        }

        $paidStatusArrey = ["done", "paid", 200, 1];
        $pendingStatusArrey = [0, "pending"];
        $rejectStatusArrey = ["reject", "no", 404, 1, 100];
        $allData = array_merge(
            json_decode($fileDataW, true),
            json_decode($fileDataX, true),
            json_decode($fileDataY, true)
        );
        $laravelcollection = collect($allData);
        if ($request->has('status') && $request->statusn != " ") {
            if ($request->status == "paid") {
                $laravelcollection = $laravelcollection->whereIn('status', $paidStatusArrey);
            } elseif ($request->status == "pending") {
                $laravelcollection = $laravelcollection->whereIn('status', $pendingStatusArrey);
            } else {
                $laravelcollection = $laravelcollection->whereIn('status', $rejectStatusArrey);
            }
        }
        if ($request->has("currency")) {
            $laravelcollection = $laravelcollection->where('currency', '=', $request->currency);
        }
        $laravelcollection = $laravelcollection->all();
        return response()->json(['status' => true, 'message' => "successful", "data" => $laravelcollection], 200);

    }
}
