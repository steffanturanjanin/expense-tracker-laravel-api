<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\Category as CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return response()->json(CategoryResource::collection($user->categories));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
           'name' => 'required|max:20',
            'icon' => 'required'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->icon = $request->icon;
        $category->user_id = $request->user()->id;

        if (Category::where('name', $request->name)->where('user_id', $request->user()->id)->first()) {
            return response()->json(["categoryName" => "This category name already exists"], 409);
        }

        $category->save();

        return response()->json(new CategoryResource($category));
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::where('id', $id)->where('user_id', Auth::user()->id)->first();

        if (!$category) {
            return response()->json("Category not found", 404);
        }

        return response()->json(new CategoryResource($category));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $isSuccessful = $category->delete();
        if ($isSuccessful)
        {
            return response()->json(new CategoryResource($category));
        } else {
            return response()->json(["error" => "Deletion failed."], 404);
        }
    }
}
