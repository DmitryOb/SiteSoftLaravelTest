<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticable;
use Illuminate\Auth\UserInterface;
Use Redirect;

class UserController extends Controller
{
	public function store(request $request){
		$name = $request->input('username');
		$pass = $request->input('password');
		$query = 'INSERT INTO users(id, name, password) values(null, "'.$name.'","'.$pass.'")';
		DB::insert($query);
		return view('reg_success');
	}
	public function logs(request $request){
		$name = $request->input('username');
		$pass = $request->input('password');
		$query = 'SELECT ID FROM users WHERE name="'.$name.'" AND password="'.$pass.'"';
		$data = DB::select($query);
		if (count($data)>0) {
			foreach ($data as $user){
				$id = $user->ID;
				Auth::loginUsingId($id);
				return view('index');
			}
		} else {
			return view('login', ['error'=>'loginerror']);
		}
	}
	public function logout(){
		Auth::logout();
		return Redirect::back();
	}
	public function ajaxcheck(request $request){
		$name = $request->input('username');
		$pass = $request->input('password');
		$query = 'SELECT * FROM users WHERE name="'.$name.'" AND password="'.$pass.'"';
		$data = DB::select($query);
		if (count($data)>0) {
			foreach ($data as $user){
				return 'true';
			}
		} else {
			return 'false';
		}
	}
	public function ajaxcheckPrivate(request $request){
		$userID = $request->input('userID');
		$inputPass = $request->input('inputPass');
		$query = 'SELECT * FROM users WHERE id="'.$userID.'" AND password="'.$inputPass.'"';
		$data = DB::select($query);
		if (count($data)>0) {
			foreach ($data as $user){
				return $user->password;
			}
		} else {
			return 0;
		}
	}
}
