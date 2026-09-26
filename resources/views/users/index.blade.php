@extends('layouts.app')

@section('title', 'Team & User Roles Management')
@section('page_title', 'Access Control & User Roles')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
        <p style="color: var(--text-secondary); font-size: 0.95rem;">
            Assign roles and configure fine-grained permissions for your inventory team.
        </p>
        <button type="button" class="btn btn-primary btn-sm" onclick="openModal('addUserModal')">
            <i data-lucide="user-plus" style="width: 15px; height: 15px;"></i>
            <span>Add Team Member</span>
        </button>
    </div>

    <!-- Role Permissions Summary Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 12px;">
        <div class="kpi-card" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge badge-danger">ADMIN</span>
                <strong style="font-size: 0.85rem;">Administrator</strong>
            </div>
            <p style="font-size: 0.78rem; color: var(--text-secondary);">Full superuser rights: user management, catalog deletes, stock auditing & exports.</p>
        </div>

        <div class="kpi-card" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge badge-info">MANAGER</span>
                <strong style="font-size: 0.85rem;">Warehouse Manager</strong>
            </div>
            <p style="font-size: 0.78rem; color: var(--text-secondary);">Create/edit catalog items, suppliers, categories & perform stock adjustments.</p>
        </div>

        <div class="kpi-card" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge badge-success">STAFF</span>
                <strong style="font-size: 0.85rem;">Inventory Staff</strong>
            </div>
            <p style="font-size: 0.78rem; color: var(--text-secondary);">Daily operations: view catalog, record incoming stock & fulfill dispatch orders.</p>
        </div>

        <div class="kpi-card" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge badge-warning">AUDITOR</span>
                <strong style="font-size: 0.85rem;">Auditor / Viewer</strong>
            </div>
            <p style="font-size: 0.78rem; color: var(--text-secondary);">Read-only access: view inventory valuations, trend charts, and export CSV data.</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="panel">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email Address</th>
                        <th>Assigned Role</th>
                        <th>Privileges</th>
                        <th>Joined</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="brand-icon" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                        <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700;">{{ $user->name }}</div>
                                        @if(auth()->id() === $user->id)
                                            <span style="font-size: 0.72rem; color: var(--primary); font-weight: 600;">(Current Session)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role_badge_class }}">
                                    <span class="badge-dot"></span>
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.82rem;">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Remove user account {{ addslashes($user->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon" style="color: var(--danger);" title="Remove User">
                                                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-subtle);">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Add New Team Member</h3>
                <button type="button" class="btn-icon" onclick="closeModal('addUserModal')">&times;</button>
            </div>
            
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Johnathan Miller">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control" required placeholder="john@company.com">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Select System Role *</label>
                            <select name="role" class="form-control" required>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}">{{ strtoupper($key) }} – {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required placeholder="Min 6 characters">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
