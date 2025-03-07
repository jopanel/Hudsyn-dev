@extends('hudsyn.layouts.app')

@section('content')
    <h1>Edit Setting</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.settings.update', $setting->id) }}">
        @csrf
        @method('PUT')
        <div>
            <label>Key:</label>
            <input type="text" name="key" value="{{ old('key', $setting->key) }}" required>
        </div>
        <div>
            <label>Value:</label>
            <input type="text" name="value" value="{{ old('value', $setting->value) }}" required>
        </div>
        <button type="submit">Update Setting</button>
    </form>
@endsection
