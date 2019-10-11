<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;
class PostController extends Controller{
    //
    public function __construct(){
		$this->middleware('api.auth',[
			'except'=>
			[
				'index','show','getImage','getPostByUser','getPostByCategory'
			]
		]);
	}
	public function index(){
        $posts = Post::all()->load('category');
        return response()->json([
        	'code'=>200,
        	'status'=>'success',
        	'categories'=>$posts
        ],200);
    }
    public function show($id){
    	$post = Post::find($id)->load('category');
    	if(is_object($post)){
    		$data = array(
    			'code'=>200,
	        	'status'=>'success',
	        	'category'=>$post
    		);
    	}else{
    		$data = array(
    			'code'=>400,
	        	'status'=>'error',
	        	'message'=>'No existe'
    		);
    	}
    	return response()->json($data,$data['code']);
    }
    public function store(Request $req){
    	$json = $req->input('json',null);
        $params = json_decode($json);           //object
        $params_arr = json_decode($json,true); //array
        if(!empty($params_arr)){
    		$user=$this->getIdentity($req);
        	
        	$validate=
        	 \Validator::make($params_arr , 
                [
                    'title'      =>  'required',
                    'content' =>  'required',
                    'category_id' =>  'required',
                    'image'       =>  'required',
                ]);
        	if($validate->fails()){
                $data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'No se ha guardado el post',
                );
            }else {
            	$post = new Post;
            	$post->user_id = $user->sub;
            	$post->title= $params->title;
            	$post->category_id= $params->category_id;
            	$post->content= $params->content;
            	$post->image= $params->image;

            	$post->save();
            	$data = array(
                    'status'  => 'succes',
                    'code'    => 200,
                    'post'=> $post,
                );
            }
        }else{
        	$data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'Campos incorrectos',
                );
        }
    	return response()->json($data,$data['code']);
    }
    public function update($id, Request $req){
    	$json = $req->input('json',null);
        $params = json_decode($json);           //object
        $params_arr = json_decode($json,true); //array
        if(!empty($params_arr)){
        	$validate=
        	 \Validator::make($params_arr , 
                [
                    'title'      =>  'required',
                    'content' =>  'required',
                    'category_id' =>  'required',
                ]);
        	if($validate->fails()){
                $data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'No se ha guardado la categoría',
                );
            }else{
	        	unset($params_arr['id']);
	        	unset($params_arr['user_id']);
	        	unset($params_arr['create_at']);
	        	unset($params_arr['user']);
    			$user=$this->getIdentity($req);
    			$us_id = $user->sub;
    			// die();

    			$post = Post::where('id',$id)
    					->where('user_id',$user->sub)
    					->first();
    			if(!empty($post) && is_object($post)){
    				$post->updateOrCreate($params_arr);
					$data = array(
		                'status'  => 'succes',
		                'code'    => 200,
		                'post'=> $post,
		                'changes'=> $params_arr,
		            );
    			}else{
    				$data = array(
	                    'status'  => 'error',
	                    'code'    => 404,
	                    'message' => 'Datos incorrectos',
	                );
    			}
            }
            
        }else{
        	$data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'Campos incorrectos',
                );
        }
    	return response()->json($data,$data['code']);
    }
    public function destroy($id, Request $req){
    	// Conseguir usuario identificado
    	$user=$this->getIdentity($req);
    	
    	// Conseguir el post
    	$post = Post::where('id',$id)
    					->where('user_id',$user->sub)
    					->first();
    	if(!empty($post)){
	    	// Borrarlo
	    	$post->delete();
	    	// return
	    	$data = [
	    		'code'=>200,
	    		'status'=>'success',
	    		'post'=>$post,
	    	];
    	}else{
    		$data = [
	    		'code'=>404,
	    		'status'=>'error',
	    		'message'=>'No existe',
	    	];
    	}
    	return response()->json($data, $data['code']);
    }
    private function getIdentity($req)
    {
    	$jwtAuth = new JwtAuth();
    	$token   = $req->header('Authorization',null);
		$user    = $jwtAuth->checkToken($token, true);
		return $user;
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
                \Storage::disk('img')->put($image_name,\File::get($image));
            $data = array(
                'image'=>$image_name,
                'code'=>200,
                'status'=>'success'
            );
        }
        
        return response()->json($data,$data['code']);
    }
    public function getImage($filename){
        $isset =\Storage::disk('img')->exists($filename); 
        if($isset){
            $file = \Storage::disk('img')->get($filename);
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
    public function getPostByCategory($id){
    	$posts = Post::where('category_id',$id)->get();
    	return response()->json(
    		[
    			'status'=>200,
    			'posts'=>$posts
    		],200
    	);
    }
    public function getPostByUser($id){
    	$posts = Post::where('category_id',$id)->get();
    	return response()->json([
    			'status'=>200,
    			'posts'=>$posts
    		],200
    	);
    }
}
