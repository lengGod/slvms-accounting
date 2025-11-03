@extends('errors::layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Kesalahan Server Internal'))
@section('description', 'Terjadi kesalahan tak terduga di server. Kami sedang berupaya memperbaikinya.')
