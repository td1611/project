<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\CategoryRequest;
use App\Helpers\ResponseHelper;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

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

    public function index()
    {
        return Inertia::render('Admin/Category/Index');
    }

    function getCategories(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $categories =  $this->category->latest('id')->paginate($limit);
        return  CategoryResource::collection($categories)
            ->response();
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
            $validatedData['slug'] = $validatedData['title'];
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
