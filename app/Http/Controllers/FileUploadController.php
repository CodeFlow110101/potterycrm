<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    function store(Request $request)
    {
        $name = (string)Str::uuid() . $request->file('file')->getClientOriginalName();
        $path =  $request->file('file')->storeAs('files', $name, 'public');
        $name =  $request->file('file')->getClientOriginalName();

        return ['name' => $name, 'path' => $path];
    }
}
