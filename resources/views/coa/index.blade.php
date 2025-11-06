@extends('layouts.admin')

@section('title', 'Chart of Accounts')

@section('page-title', 'Chart of Accounts')
@section('page-description', 'Manage your chart of accounts')

@section('content')
    @livewire('coa-management')
@endsection
