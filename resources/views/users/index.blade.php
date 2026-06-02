@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'Users')
@section('breadcrumb')
<li class="breadcrumb-item active">Users</li>
@endsection

@section('content')

{{-- Toast --}}
@if(session('toast_success'))
<div class="position-fixed top-0 end-0 p-3" style="z-index:9999">
    <div class="toast show align-items-center text-bg-success border-0 shadow" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('toast_success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

{{-- Header Row --}}
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <form method="GET" action="{{ route('users.index') }}" class="d-flex gap-2 flex-wrap">
        <div class="input-group" style="max-width:280px;">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}"
                   class="form-control border-start-0 ps-0"
                   placeholder="Search name or email…">
        </div>
        <select name="per_page" class="form-select" style="width:auto;" onchange="this.form.submit()">
            @foreach([10, 25, 50] as $n)
                <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} / page</option>
            @endforeach
        </select>
        <button class="btn btn-outline-secondary" type="submit">Search</button>
        @if($search)
            <a href="{{ route('users.index') }}" class="btn btn-outline-danger">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </form>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill me-1"></i> Add User
    </button>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4 text-muted">{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-primary bg-opacity-10 text-primary fw-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editUserModal">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteUserModal">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center px-4 py-3">
        <small class="text-muted">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
        </small>
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- ── Add User Modal ── --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-1"></i>Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit User Modal ── --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-fill me-2 text-primary"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">New Password <span class="text-muted fw-normal">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 characters">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Delete User Modal ── --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-trash3-fill me-2"></i>Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-1">Are you sure you want to delete</p>
                <p class="fw-bold fs-6" id="delete_user_name"></p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteUserForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash3 me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
    }
    .table > :not(caption) > * > * {
        padding-top: .85rem;
        padding-bottom: .85rem;
    }
</style>
@endsection

@section('scripts')
<script>
    // Edit modal — populate fields
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id    = this.dataset.id;
            const name  = this.dataset.name;
            const email = this.dataset.email;
            document.getElementById('edit_name').value  = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('editUserForm').action = `/users/${id}`;
        });
    });

    // Delete modal — set form action & user name
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const name = this.dataset.name;
            document.getElementById('delete_user_name').textContent = name;
            document.getElementById('deleteUserForm').action = `/users/${id}`;
        });
    });

    // Auto-dismiss toast
    const toastEl = document.querySelector('.toast');
    if (toastEl) setTimeout(() => bootstrap.Toast.getOrCreateInstance(toastEl).hide(), 3500);
</script>
@endsection