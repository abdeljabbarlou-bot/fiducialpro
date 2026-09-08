@extends('errors.layout', ['icon' => 'fa-server', 'accent' => 'rose'])

@section('title', 'Erreur serveur')
@section('code', '500')
@section('heading', 'Erreur interne du serveur')
@section('message')
    Une erreur inattendue est survenue de notre côté. Notre équipe technique a été notifiée. Veuillez réessayer dans quelques instants.
@endsection
