@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Ultra5G')

@section('content')
<!-- Page Header -->
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-dark);">Analytics Overview</h1>
    <p style="color: var(--text-muted); margin-top: 4px;">Welcome back to your administration panel.</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <span class="stat-label">Total LED Sales</span>
                <span class="stat-value">5,284</span>
            </div>
            <div style="padding: 10px; background: #e0f2fe; color: #0284c7; border-radius: 10px;">
                <i class="fas fa-lightbulb fa-lg"></i>
            </div>
        </div>
        <span style="color: #10b981; font-size: 13px; margin-top: 10px;">
            <i class="fas fa-arrow-up"></i> 18% vs last month
        </span>
    </div>
    <div class="stat-card">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <span class="stat-label">Wire Stock (m)</span>
                <span class="stat-value">12,450</span>
            </div>
            <div style="padding: 10px; background: #dcfce7; color: #16a34a; border-radius: 10px;">
                <i class="fas fa-drum-steelpan fa-lg"></i>
            </div>
        </div>
        <span style="color: #3b82f6; font-size: 13px; margin-top: 10px;">
            <i class="fas fa-info-circle"></i> Stock sufficient
        </span>
    </div>
    <div class="stat-card">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <span class="stat-label">Solar Orders</span>
                <span class="stat-value">124</span>
            </div>
            <div style="padding: 10px; background: #fef9c3; color: #ca8a04; border-radius: 10px;">
                <i class="fas fa-sun fa-lg"></i>
            </div>
        </div>
        <span style="color: #10b981; font-size: 13px; margin-top: 10px;">
            <i class="fas fa-check-circle"></i> 12 Pending delivery
        </span>
    </div>
    <div class="stat-card">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <span class="stat-label">Active Dealers</span>
                <span class="stat-value">86</span>
            </div>
            <div style="padding: 10px; background: #ede9fe; color: #7c3aed; border-radius: 10px;">
                <i class="fas fa-handshake fa-lg"></i>
            </div>
        </div>
        <span style="color: #3b82f6; font-size: 13px; margin-top: 10px;">
            <i class="fas fa-map-marker-alt"></i> Across Chhattisgarh
        </span>
    </div>
</div>

<!-- Data Sections -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">
    <!-- Recent Activity -->
    <div class="data-card" style="flex: 2;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="card-title" style="margin: 0;">Recent Orders</h3>
            <a href="#" style="font-size: 13px; color: var(--primary-blue); text-decoration: none; font-weight: 600;">View All Orders</a>
        </div>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 500px;">
                <thead>
                    <tr style="border-bottom: 1px solid #edf2f7; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">
                        <th style="padding: 12px 0;">Dealer / Product</th>
                        <th style="padding: 12px 0;">Quantity</th>
                        <th style="padding: 12px 0;">Status</th>
                        <th style="padding: 12px 0;">Date</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    <tr style="border-bottom: 1px solid #f9fafb;">
                        <td style="padding: 16px 0;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 30px; height: 30px; border-radius: 6px; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center; font-size: 11px;"><i class="fas fa-bolt"></i></div>
                                <div>
                                    <div style="font-weight: 600;">Raipur Electricals</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">9W LED Bulb (100pcs)</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 0; font-weight: 600;">100 Units</td>
                        <td style="padding: 16px 0;"><span style="padding: 4px 8px; background: #d1fae5; color: #065f46; border-radius: 6px; font-size: 11px; font-weight: 600;">Dispatched</span></td>
                        <td style="padding: 16px 0; color: var(--text-muted);">Today</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f9fafb;">
                        <td style="padding: 16px 0;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 30px; height: 30px; border-radius: 6px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 11px;"><i class="fas fa-drum-steelpan"></i></div>
                                <div>
                                    <div style="font-weight: 600;">Bhilai Cables Ltd</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">House Wiring Cable (500m)</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 0; font-weight: 600;">500 Meters</td>
                        <td style="padding: 16px 0;"><span style="padding: 4px 8px; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 11px; font-weight: 600;">Processing</span></td>
                        <td style="padding: 16px 0; color: var(--text-muted);">Yesterday</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notifications -->
    <div class="data-card" style="flex: 1;">
        <h3 class="card-title">System Activity</h3>
        <div style="display: flex; flex-direction: column; gap: 18px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-database" style="font-size: 14px;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 13px;">Database Optimized</div>
                    <div style="font-size: 11px; color: var(--text-muted);">2 hours ago</div>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-shield-alt" style="font-size: 14px;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 13px;">Security Scan Pass</div>
                    <div style="font-size: 11px; color: var(--text-muted);">5 hours ago</div>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #fff1f2; color: #e11d48; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 14px;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 13px;">High CPU Usage</div>
                    <div style="font-size: 11px; color: var(--text-muted);">10 hours ago</div>
                </div>
            </div>
        </div>
        <button style="width: 100%; margin-top: 25px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; color: var(--text-dark); font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
            Manage Notifications
        </button>
    </div>
</div>
@endsection
