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
    public function index()
    {
        return $this->successResponse(Deposit::paginate());
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
            $find = $saving->where('user_id',auth()->user()->id);
            $deposit = $request->nominal + $find->sum('saldo');
            Deposit::create($request->all());
            $find->update(['saldo' => $deposit]);
            DB::commit();
            return $this->successResponse($request->all(),'Data berhasil disimpan');
        } catch (Exception $e) {
            DB::rollBack();
            return response(['error' => $e->getMessage()],400);
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
        $deposit->delete();
        $this->successResponse(message:'Data berhasil dihapus');
    }
}
