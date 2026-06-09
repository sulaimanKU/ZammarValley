@extends('layouts.index')

@push('styles')
<style>
    .bg-info-subtle { background-color: #e0f7fa; }
    .bg-success-subtle { background-color: #e8f5e9; }
    .bg-danger-subtle { background-color: #ffebee; }
    .table thead th { font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px; }
    .dropdown-item:active { background-color: #f8f9fa; color: inherit; }
    .mr-3 { margin-right: 1rem; }
    .avatar-letter { width: 48px; height: 48px; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded-circle p-3 mr-3">
                        <i class="fa-solid fa-users fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75 text-white">Total Users</h6>
                        <h4 class="mb-0 fw-bold">{{ $users->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded-circle p-3 mr-3">
                        <i class="fa-solid fa-circle-dot fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75 text-white">Online</h6>
                        <h4 class="mb-0 fw-bold">{{ $onlineCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded-circle p-3 mr-3">
                        <i class="fa-solid fa-circle-xmark fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75 text-white">Offline</h6>
                        <h4 class="mb-0 fw-bold">{{ $offlineCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-shield-halved me-2 text-primary"></i>System Access Control</h5>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        {{-- <form action="" method="GET" class="d-none d-md-flex">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-0 shadow-none" placeholder="Search users..." value="{{ request('search') }}">
                            </div>
                        </form> --}}
                        {{-- Changed from .store to .create for the link --}}
                        <a href="{{ route('add.user') }}" class="btn btn-primary rounded-3 px-4 shadow-sm">
                            <i class="fa-solid fa-plus me-2"></i>Create User
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">User Details</th>
                            <th>Role</th> {{-- New Column --}}
                            <th>Status</th>
                            <th>Verification</th>
                            <th>Joined</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        @if($user->profile_image)
                                            <img src="{{ asset('storage/user_image/'.$user->profile_image) }}" class="rounded-circle border border-2 border-white shadow-sm" width="48" height="48" style="object-fit: cover;">
                                        @else
                                            <div class="bg-info-subtle text-info fw-bold rounded-circle border avatar-letter">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="position-absolute bottom-0 end-0 p-1 {{ in_array($user->id, $onlineUserIds) ? 'bg-success' : 'bg-secondary' }} border border-white rounded-circle"
                              title="{{ in_array($user->id, $onlineUserIds) ? 'Online' : 'Offline' }}"></span>
                                    </div>
                                    <div class="ms-3">
                                        <div class="fw-bold text-dark mb-0">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{-- Checks if user has roles using Spatie or custom relation --}}
                                @if($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-light text-dark border font-monospace">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted x-small">No Role</span>
                                @endif
                            </td>
                            <td>
                                @php $isOnline = in_array($user->id, $onlineUserIds); @endphp
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge {{ $user->status == 1 ? 'bg-success-subtle text-success border-success' : 'bg-danger-subtle text-danger border-danger' }} border px-2 py-1">
                                        {{ $user->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="badge {{ $isOnline ? 'bg-success text-white' : 'bg-secondary-subtle text-secondary border-secondary' }} border px-2 py-1" style="font-size:10px;">
                                        <i class="fa-solid fa-circle" style="font-size:7px;"></i>
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="text-primary small"><i class="fa-solid fa-circle-check"></i> Verified</span>
                                @else
                                    <span class="text-warning small"><i class="fa-solid fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-none border" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                        <li><a class="dropdown-item py-2" href="{{ route('users.edit', $user->id) }}"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger">
                                                    <i class="fa-solid fa-trash-can me-2"></i>Delete User
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
