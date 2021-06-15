<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'success' => true,
            'data' => User::paginate()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'username' => 'required|unique:users,username,'.$request->id,
            'email' => 'required',
            'alamat' => 'required',
            'notelp' => 'required|numeric',
            'jenis_kelamin' => 'required'
        ];
        if ($request->has('password') && !empty($request->password)) {
            $rules['password'] = 'confirmed';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'message' => 'Masukkan Data Dengan Benar']);
        }
        $req = $request->all();
        $user = new User();
        // dd($role);
        if(!empty($request->id)){
            $password = !empty($request->password) ? Hash::make($request->password) : $user->find($req['id'])->password;
        }else{
            $password = Hash::make("12345");
        }
        $req['password'] = $password;
        User::updateOrCreate(['id' => $request->id],$req);
        $message = !is_null($request->id) ? 'diperbarui' : 'ditambah';
        return response()->json(['success' => $request->all(),'message' => "Data berhasil {$message}"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response([
            'success' => true,
            'data' => User::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response([
            'success' => true,
            'message' => 'Data user berhasil dihapus'
        ]);
    }
}
