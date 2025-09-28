@extends('layouts.superadmin')

@section('title', 'Edit Profil')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4>Edit Profil</h4>
</div>

<div class="table-container">
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
