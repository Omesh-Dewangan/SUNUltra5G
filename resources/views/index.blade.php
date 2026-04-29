@extends('layouts.app')

@section('title', 'Welcome to Ultra5G')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div class="status-badge status-online">System Ready</div>
        @auth
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn" style="background-color: var(--secondary-color); padding: 8px 16px; font-size: 13px;">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn" style="padding: 8px 16px; font-size: 13px;">Login</a>
        @endauth
    </div>

    @auth
        <h2>Welcome back, {{ Auth::user()->name }}!</h2>
        <p>You are successfully logged into the <strong>Ultra5G</strong> secure dashboard.</p>
    @else
        <h2>Welcome to your new Laravel Application</h2>
        <p>Your environment is fully configured with <strong>MySQL</strong> and <strong>jQuery</strong>.</p>
    @endauth
    
    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
    
    <h3>AJAX & jQuery Demo</h3>
    <p>Click the button below to test the AJAX functionality.</p>
    
    <button id="test-ajax" class="btn">Test AJAX Connection</button>
    
    <div id="ajax-response"></div>
</div>

<div class="card" style="margin-top: 20px;">
    <h3>Database Connection Info</h3>
    <ul>
        <li><strong>Connection:</strong> {{ config('database.default') }}</li>
        <li><strong>Database:</strong> {{ config('database.connections.' . config('database.default') . '.database') }}</li>
    </ul>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#test-ajax').on('click', function() {
            const btn = $(this);
            btn.text('Connecting...').prop('disabled', true);
            
            $.ajax({
                url: '/test-connection',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#ajax-response')
                        .removeClass('status-error')
                        .addClass('status-online')
                        .css({
                            'display': 'block',
                            'background-color': '#d4edda',
                            'color': '#155724',
                            'border': '1px solid #c3e6cb'
                        })
                        .html('<strong>Success!</strong> ' + response.message);
                },
                error: function() {
                    $('#ajax-response')
                        .css({
                            'display': 'block',
                            'background-color': '#f8d7da',
                            'color': '#721c24',
                            'border': '1px solid #f5c6cb'
                        })
                        .html('<strong>Error!</strong> Could not connect to the server.');
                },
                complete: function() {
                    btn.text('Test AJAX Connection').prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
