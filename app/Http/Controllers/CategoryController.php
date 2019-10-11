<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\category;
class CategoryController extends Controller{
	public function __construct(){
		$this->middleware('api.auth',['except'=>['index','show']]);
	}
    public function index(){
        $categories = Category::all();
        return response()->json([
        	'code'=>200,
        	'status'=>'success',
        	'categories'=>$categories
        ]);
    }
    
    public function show($id){
    	$category = Category::find($id);
    	if(is_object($category)){
    		$data = array(
    			'code'=>200,
	        	'status'=>'success',
	        	'category'=>$category
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
        if(!empty($params) || !empty($params_arr)){
        	$validate=
        	 \Validator::make($params_arr , 
                [
                    'name'      =>  'required',
                ]);
        	if($validate->fails()){
                $data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'No se ha guardado la categoría',
                );
            }else {
            	$category = new Category;
            	$category->name = $params_arr['name'];
            	$category->save();
            	$data = array(
                    'status'  => 'succes',
                    'code'    => 200,
                    'category'=> $category,
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
        if(!empty($params) || !empty($params_arr)){
        	$validate=
        	 \Validator::make($params_arr , 
                [
                    'name'      =>  'required',
                ]);
        	unset($params_arr['id']);
        	unset($params_arr['create_at']);

    	    $category = Category::where('id',$id)->update($params_arr);
        	
        	$data = array(
                'status'  => 'succes',
                'code'    => 200,
                'category'=> $params_arr,
            );
            
        }else{
        	$data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'Campos incorrectos',
                );
        }
    	return response()->json($data,$data['code']);
    }
}
