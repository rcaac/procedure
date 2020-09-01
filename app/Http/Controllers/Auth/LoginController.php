<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(){
        return view('auth.login');
    }

    public function login(Request $request){

        $request->session()->put('dependency_id', $request->dependency_id);

        $this->validate($request,[
            'user' => 'required',
            'password' => 'required',
            'entity_id' => 'required|integer|not_in:0',
            'dependency_id' => 'required|integer|not_in:0'
        ], array(
            'entity_id.not_in'     => 'Elije una entidad',
            'dependency_id.not_in' => 'Elije una dependencia',
            'password.required'    => 'Escriba su contraseña',
            'user.required'        => 'Escriba su usuario',
        ));

        if (Auth::attempt(['user' => $request->user, 'password' => $request->password])) {
            return response()->json(['dependency_id' => $request->dependency_id]);
        }else {
            $errors = ['validates' => trans('auth.failed')];
            return response()->json($errors, 422);
        }
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/login');
    }
}
