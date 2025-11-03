@extends('errors::layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Akses Dilarang'))
@section('description', 'Anda tidak memiliki izin untuk mengakses sumber daya ini.')
