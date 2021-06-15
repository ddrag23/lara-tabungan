<?php

namespace App\Http\Controllers;

use App\Models\Savings;
use App\Models\Withdraw;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->successResponse(Withdraw::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'nominal' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('Masukkan data dengan benar', $validator->errors());
        }
        $saving = new Savings();
        $find = $saving->where('user_id', auth()->user()->id);
        $saldo = $find->sum('saldo');
        if ($saldo == 0 ) {
            return response(['message' => 'Saldo yang anda miliki telah habis']);
        }
        $withdraw =  $saldo - $request->nominal ;
        DB::beginTransaction();
        try {
            Withdraw::create($request->all());
            $find->update(['saldo' => $withdraw]);
            DB::commit();
            return $this->successResponse($request->all(), 'Data berhasil disimpan');
        } catch (Exception $e) {
            DB::rollBack();
            return response(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function show(Withdraw $withdraw)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function destroy(Withdraw $withdraw)
    {
        $withdraw->delete();
        return $this->successResponse(message:"Data penarikan saldo berhasil dihapus");
    }
}
