@extends('errors.layout', ['icon' => 'fa-ban', 'accent' => 'amber'])

@section('title', 'Méthode non autorisée')
@section('code', '405')
@section('heading', 'Méthode non autorisée')
@section('message')
    Cette action ne peut pas être effectuée de cette manière. Il s'agit probablement d'un lien obsolète ou d'un favori mal enregistré.
@endsection
