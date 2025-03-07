@extends('hudsyn.layouts.app')

@section('content')
    <h1>Create New Layout</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.layouts.store') }}">
        @csrf
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div>
            <label>Header File:</label>
            <input type="text" name="header_file" value="{{ old('header_file') }}" required>
        </div>
        <div>
            <label>Footer File:</label>
            <input type="text" name="footer_file" value="{{ old('footer_file') }}" required>
        </div>
        <button type="submit">Create Layout</button>
    </form>
@endsection
