@extends('errors.layout', ['icon' => 'fa-lock', 'accent' => 'rose'])

@section('title', 'Accès refusé')
@section('code', '403')
@section('heading', 'Accès refusé')
@section('message')
    Vous n'avez pas les permissions requises pour accéder à cette page ou effectuer cette action. Si vous pensez qu'il s'agit d'une erreur, contactez votre administrateur.
@endsection
