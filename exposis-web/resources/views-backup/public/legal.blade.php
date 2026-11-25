@extends('layouts.public')
@section('title', 'Mentions légales | EXPOSIS')
@section('description', 'Mentions légales et données personnelles d’EXPOSIS.')
@section('content')
@php $todo = '<b style="color:#B84A00">[À compléter]</b>'; @endphp
<div class="ph"><div class="w"><h1>Mentions légales.</h1></div></div>
<section><div class="w" style="max-width:820px">
  <ul class="pri" style="margin-top:0">
    <li><b>Forme juridique</b><span>{!! $todo !!}</span></li><li><b>Adresse du siège</b><span>{!! $todo !!}</span></li>
    <li><b>Directeur de la publication</b><span>{!! $todo !!}</span></li><li><b>N° d’enregistrement</b><span>{!! $todo !!}</span></li>
    <li><b>Contact</b><span>contact-lab@exposis.net</span></li>
    <li><b>Données personnelles</b><span>Les données servent uniquement à traiter votre demande, conformément à la loi ivoirienne n° 2013-450 et sous le contrôle de l’ARTCI. Durée de conservation : {!! $todo !!}. Droits d’accès, de rectification et d’opposition : {!! $todo !!}.</span></li>
    <li><b>Propriété intellectuelle</b><span>Soumettre un projet ne transfère aucun droit.</span></li></ul>
</div></section>
@endsection
