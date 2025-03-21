@extends('hudsyn.layouts.app')

@section('content')
    <h1>Social Media Authentication</h1>
    
    <div class="social-auth-section">
        <h2>Platform Authentication Status</h2>
        
        <!-- Twitter (X) Authentication -->
        <div class="platform-auth">
            <h3>Twitter (X)</h3>
            @php
                $twitterToken = \App\Hudsyn\Setting::where('key', 'social_x_user_token')->first();
                $twitterExpires = \App\Hudsyn\Setting::where('key', 'social_x_user_token_expires')->first();
                $isTwitterAuthenticated = $twitterToken && $twitterExpires && strtotime($twitterExpires->value) > time();
            @endphp
            @if($isTwitterAuthenticated)
                <span class="status authenticated">✓ Authenticated</span>
                <p>Token expires: {{ \Carbon\Carbon::parse($twitterExpires->value)->format('Y-m-d H:i:s') }}</p>
            @else
                <span class="status unauthenticated">✗ Not Authenticated</span>
            @endif
            <a href="{{ route('hudsyn.twitter.auth') }}" class="btn btn-primary">Authenticate with Twitter</a>
        </div>

        <!-- LinkedIn Authentication -->
        <div class="platform-auth">
            <h3>LinkedIn</h3>
            @php
                $linkedinToken = \App\Hudsyn\Setting::where('key', 'social_linkedin_user_token')->first();
                $linkedinExpires = \App\Hudsyn\Setting::where('key', 'social_linkedin_user_token_expires')->first();
                $isLinkedInAuthenticated = $linkedinToken && $linkedinExpires && strtotime($linkedinExpires->value) > time();
            @endphp
            @if($isLinkedInAuthenticated)
                <span class="status authenticated">✓ Authenticated</span>
                <p>Token expires: {{ \Carbon\Carbon::parse($linkedinExpires->value)->format('Y-m-d H:i:s') }}</p>
            @else
                <span class="status unauthenticated">✗ Not Authenticated</span>
            @endif
            <a href="{{ route('hudsyn.linkedin.auth') }}" class="btn btn-primary">Authenticate with LinkedIn</a>
        </div>

        <!-- Facebook Authentication -->
        <div class="platform-auth">
            <h3>Facebook</h3>
            @php
                $facebookToken = \App\Hudsyn\Setting::where('key', 'social_facebook_user_token')->first();
                $facebookExpires = \App\Hudsyn\Setting::where('key', 'social_facebook_user_token_expires')->first();
                $isFacebookAuthenticated = $facebookToken && $facebookExpires && strtotime($facebookExpires->value) > time();
            @endphp
            @if($isFacebookAuthenticated)
                <span class="status authenticated">✓ Authenticated</span>
                <p>Token expires: {{ \Carbon\Carbon::parse($facebookExpires->value)->format('Y-m-d H:i:s') }}</p>
            @else
                <span class="status unauthenticated">✗ Not Authenticated</span>
            @endif
            <a href="{{ route('hudsyn.facebook.auth') }}" class="btn btn-primary">Authenticate with Facebook</a>
        </div>

        <!-- Instagram Authentication -->
        <div class="platform-auth">
            <h3>Instagram</h3>
            @php
                $instagramToken = \App\Hudsyn\Setting::where('key', 'social_instagram_user_token')->first();
                $instagramExpires = \App\Hudsyn\Setting::where('key', 'social_instagram_user_token_expires')->first();
                $isInstagramAuthenticated = $instagramToken && $instagramExpires && strtotime($instagramExpires->value) > time();
            @endphp
            @if($isInstagramAuthenticated)
                <span class="status authenticated">✓ Authenticated</span>
                <p>Token expires: {{ \Carbon\Carbon::parse($instagramExpires->value)->format('Y-m-d H:i:s') }}</p>
            @else
                <span class="status unauthenticated">✗ Not Authenticated</span>
            @endif
            <a href="{{ route('hudsyn.instagram.auth') }}" class="btn btn-primary">Authenticate with Instagram</a>
        </div>
    </div>

    <h1>Global Settings</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('hudsyn.settings.create') }}" class="btn btn-secondary">Add New Setting</a>
    
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Key</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settings as $setting)
                <tr>
                    <td>{{ $setting->id }}</td>
                    <td>{{ $setting->key }}</td>
                    <td>{{ $setting->value }}</td>
                    <td>
                        <a href="{{ route('hudsyn.settings.edit', $setting->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('hudsyn.settings.destroy', $setting->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this setting?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <style>
        .social-auth-section {
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .platform-auth {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }
        .status.authenticated {
            background: #d4edda;
            color: #155724;
        }
        .status.unauthenticated {
            background: #f8d7da;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            background: #007bff;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #007bff;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .table th, .table td {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
        }
        .table th {
            background: #f8f9fa;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
@endsection
