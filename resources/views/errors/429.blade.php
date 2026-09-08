@extends('errors.layout', ['icon' => 'fa-gauge-high', 'accent' => 'amber'])

@section('title', 'Trop de requêtes')
@section('code', '429')
@section('heading', 'Trop de tentatives')
@section('message')
    Trop de requêtes ont été envoyées en peu de temps. Veuillez patienter quelques instants avant de réessayer.
@endsection
