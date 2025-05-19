@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Welcome to Admin Dashboard</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h5>Products Management</h5>
                                    <p class="fs-4">
                                        <i class="fas fa-box me-2"></i>
                                        {{ isset($totalProducts) ? $totalProducts : '-' }} Total Products
                                    </p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="text-white stretched-link" href="{{ route('admin.products.index') }}">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h5>Users Management</h5>
                                    <p class="fs-4">
                                        <i class="fas fa-users me-2"></i>
                                        {{ isset($totalUsers) ? $totalUsers : '-' }} Registered Users
                                    </p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="text-white stretched-link" href="{{ route('admin.users.index') }}">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>

                        @if(isset($featuredProducts))
                        <div class="col-lg-4">
                            <div class="card bg-warning text-dark mb-4">
                                <div class="card-body">
                                    <h5>Featured Products</h5>
                                    <p class="fs-4">
                                        <i class="fas fa-star me-2"></i>
                                        {{ $featuredProducts }} Featured Items
                                    </p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="text-dark stretched-link" href="{{ route('admin.products.index') }}">View Details</a>
                                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-lg w-100">
                                                <i class="fas fa-plus me-2"></i> Add New Product
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-lg w-100">
                                                <i class="fas fa-user-plus me-2"></i> Add New User
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
