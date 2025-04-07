<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();

            return $this->response(
                true,
                'Categories fetched successfully',
                $categories,
                'categories'
            );
        } catch (Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());

            return $this->response(
                false,
                'Error fetching categories',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
