<?php

namespace App\Http\Controllers;

use App\Models\Savings;
use Illuminate\Http\Request;

class SavingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return response()->json($request->search);
        if (!is_null($request->search)) {
            // return response('ok');
            return response()->json(['data' => Savings::with('user')->whereHas('user',function($query) use ($request){
                $query->where('name', 'LIKE', '%'.$request->search.'%');
            })->paginate(10)]);
        }
        return $this->successResponse(Savings::with('user')->paginate(10));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Savings  $savings
     * @return \Illuminate\Http\Response
     */
    public function show(Savings $savings)
    {
        return response()->json($savings);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Savings  $savings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Savings $savings)
    {
        $savings->delete();
        return $this->successResponse(message:'Data tabungan berhasil dihapus');
    }
}
