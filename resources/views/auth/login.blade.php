<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In – StockMaster IMS</title>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: radial-gradient(circle at top right, #1e1b4b, #090d16 60%);
            padding: 20px;
        }
        .login-card {
            background: #111827;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 460px;
            padding: 36px 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .role-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: var(--bg-surface-elevated);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.84rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .role-btn:hover {
            border-color: var(--primary);
            background: #1f2d45;
            transform: translateX(4px);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Logo Header -->
        <div style="text-align: center; margin-bottom: 28px;">
            <div class="brand-icon" style="margin: 0 auto 14px; width: 48px; height: 48px;">
                <i data-lucide="boxes" style="width: 26px; height: 26px;"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">StockMaster IMS</h2>
            <p style="color: var(--text-secondary); font-size: 0.88rem; margin-top: 4px;">Role-Based Inventory Access Control</p>
        </div>

        @if(session('error'))
            <div class="alert-toast alert-error" style="margin-bottom: 20px;">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="alert-toast alert-success" style="margin-bottom: 20px;">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-toast alert-error" style="margin-bottom: 20px;">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Credentials Form -->
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email', 'admin@inventory.local') }}" class="form-control" required placeholder="name@company.com">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" value="password123" class="form-control" required placeholder="••••••••">
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 0.85rem;">
                <label style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary); cursor: pointer;">
                    <input type="checkbox" name="remember" checked style="accent-color: var(--primary);">
                    <span>Remember me</span>
                </label>
                <span style="color: var(--text-muted); font-size: 0.78rem;">Default: password123</span>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px;">
                <i data-lucide="log-in" style="width: 16px; height: 16px;"></i>
                <span>Sign In to Dashboard</span>
            </button>
        </form>

        <!-- 1-Click Role Switcher Demo -->
        <div style="margin-top: 28px; padding-top: 22px; border-top: 1px solid var(--border-subtle);">
            <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); font-weight: 700; margin-bottom: 12px; text-align: center;">
                Fast 1-Click Demo Login (Select Role)
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="{{ route('login.quick', 'admin') }}" class="role-btn">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.1rem;">👑</span>
                        <div>
                            <div>System Administrator</div>
                            <span style="font-size: 0.72rem; color: var(--text-muted);">Full access, deletes, user management</span>
                        </div>
                    </div>
                    <span class="badge badge-danger">ADMIN</span>
                </a>

                <a href="{{ route('login.quick', 'manager') }}" class="role-btn">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.1rem;">💼</span>
                        <div>
                            <div>Warehouse Manager</div>
                            <span style="font-size: 0.72rem; color: var(--text-muted);">Manage products, suppliers, stock audits</span>
                        </div>
                    </div>
                    <span class="badge badge-info">MANAGER</span>
                </a>

                <a href="{{ route('login.quick', 'staff') }}" class="role-btn">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.1rem;">📦</span>
                        <div>
                            <div>Inventory Staff</div>
                            <span style="font-size: 0.72rem; color: var(--text-muted);">View catalog & record Stock In / Out</span>
                        </div>
                    </div>
                    <span class="badge badge-success">STAFF</span>
                </a>

                <a href="{{ route('login.quick', 'auditor') }}" class="role-btn">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.1rem;">👁️</span>
                        <div>
                            <div>Auditor / Viewer</div>
                            <span style="font-size: 0.72rem; color: var(--text-muted);">Read-only catalog, charts & CSV exports</span>
                        </div>
                    </div>
                    <span class="badge badge-warning">AUDITOR</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
