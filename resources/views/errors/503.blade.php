@extends('errors::layout')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Layanan Tidak Tersedia'))
@section('description', 'Situs sedang dalam pemeliharaan. Silakan kembali lagi nanti.')
