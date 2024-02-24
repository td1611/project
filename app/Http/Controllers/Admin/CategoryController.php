<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\CategoryRequest;
use App\Helpers\ResponseHelper;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected $responseHelper, $category;

    public function __construct(ResponseHelper $responseHelper, Category $category)
    {
        $this->responseHelper = $responseHelper;
        $this->category = $category;
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        return Inertia::render('Admin/Category/Index');
    }

    function getCategories(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $query = $this->category->filter($request->all());

        if ($request->status == 'trashed') {
            $query->onlyTrashed();
        }

        if (!($this->requestColumnAndOrder($request))) {
            $query->latest('title');
        }
        // sort Column
        $this->sortData($request, $query);
        $categories = $query->paginate($limit);
        return CategoryResource::collection($categories);
    }

    function sortData($request, $query)
    {
        if ($this->requestColumnAndOrder($request)) {
            $validColumns = ['id', 'title', 'created_at', 'deleted_at'];
            if (in_array($request->sortColumn, $validColumns)) {
                return  $query->orderBy($request->sortColumn, $request->order);
            }
        }
    }

    function requestColumnAndOrder($request) {
       return  $request->has('sortColumn') && $request->has('order');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('Admin/Category/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $validatedData['slug'] = Str::slug($validatedData['title']);
            // store
            $category = $this->category->create($validatedData);
            return $this->responseHelper->sendSuccessResponse(CategoryResource::make($category), ('Success Create'));
        } catch (\Exception $e) {
            return $this->responseHelper->sendErrorResponse(('Something went wrong.'), $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = $this->category->findOrFail($id);
            $data = new CategoryResource($category);
            return $this->responseHelper->sendSuccessResponse($data,);
        } catch (ModelNotFoundException $e) {
            return $this->responseHelper->sendErrorResponse(('Data not found'), $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $category = $this->category->findOrFail($id);
            $category = new CategoryResource($category);
            return Inertia::render('Admin/Category/Edit', [
                'category' => $category
            ]);
        } catch (ModelNotFoundException $e) {

            return $this->responseHelper->sendErrorResponse(('Data not found'), $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $category = $this->category->find($id);
        if (!$category) {
            return  $this->responseHelper->sendErrorResponse(__('Data not found'), "update category error", 404);
        }
        $validatedData =  $request->validated();
        try {
            $validatedData['slug'] = Str::slug($validatedData['title']);
            // update
            $category->update($validatedData);

            return  $this->responseHelper->sendSuccessResponse(CategoryResource::make($category), __('Update Success'));
        } catch (\Exception $e) {
            return  $this->responseHelper->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = $this->category->findOrFail($id);
            $category->delete();
            $this->responseHelper->sendSuccessResponse([], ('Success Delete'));
        } catch (ModelNotFoundException $e) {
            return $this->responseHelper->sendErrorResponse(('Data not found'), $e->getMessage());
        }
    }

    /**
     * restore 
     */
    function restore($id)
    {
        try {
            $category = $this->findOnlyTrash($id);
            // restore
            $category->restore();
            return response()->json([
                'message' => 'Restored Successfully',
                'status' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 404,
            ]);
        }
    }
    function forceDelete($id)
    {
        try {
            $category = $this->findOnlyTrash($id);
            // forceDelete
            $category->forceDelete();
            return response()->json([
                'message' => 'Data permanently deleted Successfully',
                'status' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 404,
            ]);
        }
    }

    function findOnlyTrash($id)
    {
        $category = $this->category->onlyTrashed()->find($id);
        if (!$category) {
            throw new ModelNotFoundException('Data not found ');
        }
        return $category;
    }
}
