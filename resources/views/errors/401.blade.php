@extends('errors::layout')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Anda tidak memiliki izin untuk mengakses halaman ini.'))
@section('description', 'Silakan hubungi administrator jika Anda yakin ini adalah kesalahan.')
