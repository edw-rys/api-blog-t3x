<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
class UserController extends Controller
{
    public function test(Request $req){
        return "User c";
    }
    public function signup(Request $req){
        // Recoger los datos del usuario por post
        $json = $req->input('json',null);
        $params = json_decode($json);           //object
        $params_arr = json_decode($json,true); //array

        if(!empty($params) || !empty($params_arr)){
            // Limpiar datos
            $params_arr = array_map('trim',$params_arr);
            // Validar los datos
            $validate = 
                \Validator::make($params_arr , 
                [
                    'name'      =>  'required|alpha',
                    'surname'   =>  'required|alpha',
                    'email'     =>  'required|email|unique:users',
                    'password'  =>  'required'
                ]);
                if($validate->fails()){
                    $data = array(
                        'status'  => 'error',
                        'code'    => 404,
                        'message' => 'El usuario no se ha creado',
                        'errors'  => $validate->errors(),
                    );
                }else {
                    
                    
                    // Cifrar la contraseña
                    // $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost'=>4]);
                    $pwd = hash('sha256',$params->password);
                    
                    // Conprobar si el usuario existe
                    
                    // Crear el usuario
                    $user = new User();
                    $user->name = $params_arr['name'];
                    $user->surname = $params_arr['surname'];
                    $user->email = $params_arr['email'];
                    $user->password = $pwd;
                    $user->role = 'ROLE_USER';
                    $user->save();
                    // Convertir un array en datos json

                    $data = array(
                        'status'  => 'success',
                        'code'    => 200,
                        'message' => 'El usuario se ha creado',
                        'user'    => $user,
                    );
                }
        }else{
            $data = array(
                'status'  => 'error',
                'code'    => 400,
                'message' => 'Datos enviados no son correctos',
            );
        }
        return response()->json($data,$data['code']);  

    }
    public function login(Request $req){
        $jwtAuth = new \JwtAuth();
        $json = $req->input('json',null);
        $params = json_decode($json);           //object
        $params_arr = json_decode($json,true); //array
        $signup =[];
        if(!empty($params) || !empty($params_arr)){
            // Limpiar datos
            $params_arr = array_map('trim',$params_arr);
            // Validar los datos
            $validate = 
                \Validator::make($params_arr , 
                [
                    'email'     =>  'required|email',
                    'password'  =>  'required'
                ]);
            if($validate->fails()){
                $signup = array(
                    'code'    => 404,
                    'message' => 'El usuario no se ha podido identificar',
                    'errors'  => $validate->errors(),
                    'status'  => 'error',
                );
            }else{
                // $pwd = password_hash($password, PASSWORD_BCRYPT, ['cost'=>4]);
                $pwd = hash('sha256',$params->password);

                // Devolver token o datos
                $signup = $jwtAuth->signup($params->email, $pwd);
                if(!empty($params->gettoken)){
                    $signup = $jwtAuth->signup($params->email, $pwd,true);
                }
            }
        }
        return response()->json($signup,200);
    }
    public function update(Request $req){
        $token = $req->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if($checkToken){
            // update user
            // Recoger datos por post
            $json = $req->input('json',null);
            // var_dump($json);
            $params = json_decode($json);           //object
            $params_arr = json_decode($json,true); //array
            // Validar los datos
            if(!empty($params) || !empty($params_arr)){
                // Validar los datos
                $user = $jwtAuth->checkToken($token, true);
                $validate = 
                    \Validator::make($params_arr , 
                    [
                        'name'      =>  'required|alpha',
                        'surname'   =>  'required|alpha',
                        'email'     =>  'required|email|unique:users,'.$user->sub,
                    ]);
                
                // Quitar los campos que no se actualizan 
                unset($params_arr['id']);
                unset($params_arr['role']);
                unset($params_arr['password']);
                unset($params_arr['create_at']);
                // Actualizar en la bd
                
                $user_update = User::where('id', $user->sub)->update($params_arr);
                // Devolver el array
                $data = array(
                    'code'=>200,
                    'status'=>'success',
                    'message'=>'Ususario actualizado',
                    "user"=> $user_update,
                    "change"=>$params_arr
                );
            }else{
                $data = array(
                    'code'=>400,
                    'status'=>'error',
                    'message'=>'Datos incompletos'
                );
            }
            // var_dump($data);
            // die();
        }
        else{
            $data = array(
                'code'=>400,
                'status'=>'error',
                'message'=>'Usuario no está identificado correctamente'
            );
        }
        return response()->json($data , $data['code']);
    }
    public function upload(Request $req){
        $image = $req->file('file0');
        // Guardar imagen
        $validate = \Validator::make($req->all(),[
            'file0'=>'required|mimes:jpg,jpeg,png,gif'
        ]);
        if(!$image || $validate->fails()){
            $data = array(
                'code'=>400,
                'status'=>'error',
                'message'=>'Error al subir una imagen'
            );
        }else{
                $image_name = time().$image->getClientOriginalName();
                \Storage::disk('users')->put($image_name,\File::get($image));
                $data = array(
                    'image'=>$image_name,
                    'code'=>200,
                    'status'=>'success'
                );
        }
        
        return response()->json($data,$data['code']);
    }
    public function getImage($filename){
        $isset =\Storage::disk('users')->exists($filename); 
        if($isset){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        }else{
            $data = array(
                'code'=>400,
                'status'=>'error',
                'message'=>'Imagen no existe'
            );
            return response()->json($data,$data['code']);
        }
    }
    public function detail($id){
        $user = User::find($id);
        if(is_object($user)){
            $data = array(
                'code'   =>200,
                'status' =>'success',
                "user"   =>$user 
            );
        }else{
            $data = array(
                'code'=>400,
                'status'=>'error',
                'message'=>'Usuario no existe'
            );
        }
        return response()->json($data,$data['code']);
    }
}
