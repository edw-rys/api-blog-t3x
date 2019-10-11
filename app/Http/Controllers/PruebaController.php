<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebaController extends Controller
{
    public function testORM()
    {
        // $posts = Post::all();
        // foreach ($posts as $post) {
        //     echo $post->title;
        //     echo "<p>{$post->user->name} - CTG: {$post->category->name}</p>";
        //     echo "<hr>";
        // }
        $categories = Category::all();
        foreach ($categories as $ctg) {
            echo "<p>{$ctg->name}</p>";
            echo "<hr>";
        }
        die();
    }
}
