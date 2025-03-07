@extends('hudsyn.layouts.app')

@section('content')
    <h1>Add New Setting</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.settings.store') }}">
        @csrf
        <div>
            <label>Key:</label>
            <input type="text" name="key" value="{{ old('key') }}" required>
        </div>
        <div>
            <label>Value:</label>
            <input type="text" name="value" value="{{ old('value') }}" required>
        </div>
        <button type="submit">Add Setting</button>
    </form>
@endsection
