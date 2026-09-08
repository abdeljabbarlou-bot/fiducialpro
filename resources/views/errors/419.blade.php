@extends('errors.layout', ['icon' => 'fa-clock-rotate-left', 'accent' => 'amber'])

@section('title', 'Session expirée')
@section('code', '419')
@section('heading', 'Session expirée')
@section('message')
    Votre session a expiré par mesure de sécurité, probablement après une longue période d'inactivité. Veuillez vous reconnecter.
@endsection
