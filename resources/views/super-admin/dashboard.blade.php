@extends('super-admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Tenants</h5>
                <h2 class="card-text">{{ $totalTenants }}</h2>
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-primary">View All</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Active Tenants</h5>
                <h2 class="card-text">{{ $activeTenants }}</h2>
                <a href="{{ route('super-admin.tenants.index', ['status' => 'active']) }}" class="btn btn-success">View Active</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Trial Tenants</h5>
                <h2 class="card-text">{{ $trialTenants }}</h2>
                <a href="{{ route('super-admin.tenants.index', ['status' => 'trial']) }}" class="btn btn-warning">View Trials</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Tenants</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Domain</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->domain }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'trial' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $tenant->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Statistics</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Total Users
                        <span class="badge bg-primary rounded-pill">{{ $totalUsers }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Total Roles
                        <span class="badge bg-primary rounded-pill">{{ $totalRoles }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Total Permissions
                        <span class="badge bg-primary rounded-pill">{{ $totalPermissions }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 