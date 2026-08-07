@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Add Category Form -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Add New Category</div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., Groceries, Salary" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Color Tag</label>
                            <input type="color" name="color" class="form-control form-control-color" value="#0d6efd">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Save Category</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category List -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Your Categories</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($categories as $category)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge me-2" style="background-color: {{ $category->color }}">&nbsp;</span>
                                    <strong>{{ $category->name }}</strong>
                                </div>
                                <span class="badge bg-secondary rounded-pill">{{ ucfirst($category->type) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-3">No categories added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
