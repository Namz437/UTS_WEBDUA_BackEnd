<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Exception;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->default_response;
        try{
        $categories = Category::all();

        $response['success'] = true;
        $response['data'] = [
            'categories' => $categories,
        ];
    }catch(\Exception $e){
        $response['message'] = $e->getMessage();
    }
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $response = $this->default_response;
        try{
            $data = $request->validated();

            $category = new Category();
            $category->name = $data['name'];
            $category->description = $data['description'];
            $category->save();

            $response['success'] = true;
            $response['data'] = [
                'category' => $category,
            ];

            $response['message'] = 'Category game created successfully';
        }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $response = $this->default_response;

        try{
            $category = Category::find($id);

            $response['success'] = true;
            $response['message'] = "Get Category Game Sucsess";
            $response['data'] = [
                'category' => $category,
            ];
        }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     */

    // public function edit(Category $category)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, String $id)
    {
        $response = $this->default_response;

        try{
            $data = $request->validated();

            $category = Category::find($id);
            $category->name = $data['name'];
            $category->description = $data['description'];
            $category->save();

            $response['success'] = true;
            $response['data'] = [
                'category' => $category,
            ];
            $response['message'] = 'Category game updated successfully';
        }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        $response = $this->default_response;

        try{
            $categories = Category::find($id);

            if(!$categories) {
                throw new Exception('Category game not found');
            }

            $categories->delete();
            if ($categories->image && Storage::disk('public')->exists($categories->image)) {
                Storage::disk('public')->delete($categories->image);
            }

            $response['success'] = true;
            $response['message'] = 'Category game deleted successfully';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }
}
