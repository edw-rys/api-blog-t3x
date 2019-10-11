<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{
    public $key;
    public function __construct(){
        $this->key = 'KAYBNFGA_TOKEN_TNX_GENERATE_RANDOM';
    }
    public function signup($email, $password,$getToken=null) {
        // Buscar si existe el usuario con sus credenciales
        $user = User::where([
            'email'    => $email,
            'password' => $password
        ])->first();
        // Comprobar si son correctas
        $signup = is_object($user) ?true :false;

        // Generar el token con los datos del usuario
        if($signup){
            $token = array(
                'sub' => $user->id,
                'email'=> $user->email,
                'name'=> $user->name,
                'surname' => $user->surname,
                'iat'=> time(),
                'exp'=>time()+(7*24*60*60)
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decode = JWT::decode($jwt,$this->key, ['HS256']);
            if(is_null($getToken))
                return $jwt;
            else
                return $decode;

        }else{
            $data = array(
                'status'=>'error',
                'message'=>'Login incorrecto.'
            );
        }
        // Devolver los datos decodificados o el token, en función de un parámetro
        return $data;
    }
    public function checkToken($jwt , $getIdentity = false){
        $auth = false;
        try{
            $jwt = str_replace('"','',$jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth= false;
        }catch(\DomainException $ex){
            $auth= false;
        }
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        if($getIdentity)
            return $decoded;
        return $auth;
    }
}
