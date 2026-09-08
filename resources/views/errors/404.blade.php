@extends('errors.layout', ['icon' => 'fa-map-signs', 'accent' => 'amber'])

@section('title', 'Page introuvable')
@section('code', '404')
@section('heading', 'Page introuvable')
@section('message')
    La page ou la ressource que vous recherchez n'existe pas, a été déplacée ou supprimée.
@endsection
