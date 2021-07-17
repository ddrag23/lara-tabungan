<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index($id)
    {
        $user = User::find($id);
        // return response($user);
        $photo = !empty($user->photo)
            ? asset('public/storage/' . $user->photo)
            : asset('public/image/person1.png');
        return response()->json($photo);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'photo' => 'mimes:jpg,jpeg,png,svg',
            'email' =>
                'required|email|unique:users,email,' . auth()->user()->id,
            // 'notelp' => 'required|numeric',
            // 'alamat' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Masukkan Data Dengan Benar',
            ]);
        } else {
            $attr = $request->all();
            $user = User::where('id', auth()->user()->id)->first();
            if (!empty($request->file('photo'))) {
                Storage::delete($user->photo);
            }
            $attr['photo'] = !empty($request->file('photo'))
                ? $request->file('photo')->store('image/photo')
                : $user->photo;
            User::where('id', auth()->user()->id)->update($attr);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimasukkan',
                'data' => User::where('id', auth()->user()->id)->first(),
            ]);
        }
    }

    public function storeChangePassword(Request $request)
    {
        $userSession = auth()->user();
        $validator = Validator::make($request->all(), [
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($userSession) {
                    if (!Hash::check($value, $userSession->password)) {
                        $fail('Password Lama Tidak Cocok');
                    }
                },
            ],
            'new_password' => 'required|same:password_confirmation',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'message' => 'Masukkan Data Dengan Benar',
            ]);
        } else {
            User::where('id', $userSession->id)->update([
                'password' => Hash::make($request->new_password),
            ]);
            return response()->json([
                'success' => $request->all(),
                'message' => 'Data berhasil dimasukkan',
            ]);
        }
    }
}
