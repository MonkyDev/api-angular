<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\JwtAuth;
use App\Http\Requests;
use App\User;

class UserController extends Controller
{
    public function register(Request $request){
    	// Recoger post
    	$json = $request->input('json', null);    	
    	$params = json_decode($json);

    	$email = ( !is_null($json) && isset($params->email) ) ? $params->email : null;
    	$name = ( !is_null($json) && isset($params->name) ) ? $params->name : null;
    	$surname = ( !is_null($json) && isset($params->surname) ) ? $params->surname : null;
    	$role = 'ROLE_USER';
    	$password = ( !is_null($json) && isset($params->password) ) ? $params->password : null;

    	if ( !is_null($email) && !is_null($password) && !is_null($name) ) 
    	{
    		// Crear Usuario
    		$user = new User();
    		$user->email = $email;
    		$user->name = htmlentities($name);
    		$user->surname = htmlentities($surname);
    		$user->role = $role;

    		$pwd = hash('sha256', $password);
    		$user->password = $pwd;

    		//Comprobar usuario duplicado
    		$isset_user = User::where('email','=',$email)->first();

    		if ( count($isset_user) == 0 ) 
    		{
    			$user->save();
    			$data = array(
	    			'status' => 'success', 
	    			'code' => 200, 
	    			'message' => 'Usuario registrado correctamente'
    			);
    		}
    		else
    		{
    			$data = array(
	    			'status' => 'error', 
	    			'code' => 400, 
	    			'message' => 'Usuario duplicado, email ya registrado...'
    			);
    		}
    	}
    	else
    	{
    		$data = array(
    			'status' => 'error', 
    			'code' => 400, 
    			'message' => 'Usuario no creado'
    		);
    		
    	}
    	return response()->json($data, 200);
    }

    public function login(Request $request){
    	$jwtAuth = new JwtAuth();

      //recibir el POST
      $json = $request->input('json', null);
      $params = json_decode($json);

      $email =  (!is_null($json) && isset($params->email) ) ? $params->email : null;
      $password = (!is_null($json) && isset($params->password) ) ? $params->password : null;
      $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : true;

      //Cifrar la password
      $pwd = hash('sha256', $password);

      if ( !is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false') ) {
        $signup = $jwtAuth->signup($email, $pwd);

      } else if($getToken != null) {
        $signup = $jwtAuth->signup($email, $pwd, $getToken);
      
      } else {
        $signup = array(
          'status' => 'error', 
          'code' => 400, 
          'message' => 'Error tus datos por post...'
        );
      }



    return response()->json($signup, 200);
    }
}
