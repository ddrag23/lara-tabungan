<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Savings;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!is_null($request->search)) {
            // return response('ok');
            return response()->json(['data' => Deposit::with('user')->whereHas('user',function($query) use ($request){
                $query->where('name', 'LIKE', '%'.$request->search.'%');
            })->paginate(10)]);
        }
        return $this->successResponse(Deposit::with('user')->paginate(10));
    }

    public function countDeposit($userId)
    {
        return response()->json(Deposit::where('user_id',$userId)->sum('nominal'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'nominal' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('Masukkan data dengan benar',$validator->errors());
        }
        DB::beginTransaction();
        try {
            $saving = new Savings();
            $find = $saving->where('user_id',$request->user_id);
            $deposit = $request->nominal + $find->sum('saldo');
            Deposit::create($request->all());
            if ($find->get()->isEmpty()) {
                $saving->create([
                    'user_id' => $request->user_id,
                    'saldo' => $deposit
                ]);
            }else{
                $find->update(['saldo' => $deposit]);
            }
            DB::commit();
            return $this->successResponse($request->all(),'Data berhasil disimpan');
        } catch (Exception $e) {
            DB::rollBack();
            return response(['error' => $e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit)
    {
        $savings = Savings::where('user_id',$deposit->user_id)->first();
        Savings::where('user_id',$deposit->user_id)->update([
            'saldo' => $savings->saldo - $deposit->nominal
        ]);
        $deposit->delete();
        $this->successResponse(message:'Data berhasil dihapus');
    }
}
