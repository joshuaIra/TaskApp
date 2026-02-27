@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Create User</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @include('admin.users._form')
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection