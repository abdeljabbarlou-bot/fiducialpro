@extends('errors.layout', ['icon' => 'fa-screwdriver-wrench', 'accent' => 'sky'])

@section('title', 'Maintenance en cours')
@section('code', '503')
@section('heading', 'Application en maintenance')
@section('message')
    L'application est actuellement en maintenance programmée. Nous serons de retour très prochainement.
@endsection
