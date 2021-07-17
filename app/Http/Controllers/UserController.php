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
    public function index(Request $request)
    {
        if (!is_null($request->search)) {
            // return response('ok');
            return response()->json([
                'data' => User::where(
                    'name',
                    'LIKE',
                    '%' . $request->search . '%'
                )->paginate(10),
            ]);
        }
        return $this->successResponse(User::paginate(10));
    }

    public function allUser()
    {
        return response(User::get());
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
            'username' => 'required|unique:users,username,' . $request->id,
            'email' => 'required',
            'alamat' => 'required',
            'notelp' => 'required|numeric',
            'jenis_kelamin' => 'required',
        ];
        if ($request->has('password') && !empty($request->password)) {
            $rules['password'] = 'confirmed';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Masukkan Data Dengan Benar',
            ]);
        }
        $req = $request->all();
        $user = new User();
        // dd($role);
        if (!empty($request->id)) {
            $password = !is_null($request->password)
                ? Hash::make($request->password)
                : $user->find($req['id'])->password;
        } else {
            $password = Hash::make('12345');
        }
        $req['password'] = $password;
        User::updateOrCreate(['id' => $request->id], $req);
        $message = !is_null($request->id) ? 'diperbarui' : 'ditambah';
        return response()->json([
            'success' => 200,
            'message' => "Data berhasil {$message}",
            's' => $request->password,
        ]);
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
            'data' => User::find($id),
        ]);
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
            'message' => 'Data user berhasil dihapus',
        ]);
    }
}
