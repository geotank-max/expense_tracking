<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Display list of categories & creation form
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return view('categories.index', compact('categories'));
    }

    // Save a new category to the database
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|in:income,expense',
            'color' => 'nullable|string|max:7', // Dynamic color code
        ]);

        Category::create([
            'user_id' => auth()->id(),
            'name'    => $request->name,
            'type'    => $request->type,
            'color'   => $request->color ?? '#6c757d',
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }
}
