@extends('layouts.app')

@section('title', 'Index - EasyFolio Bootstrap Template')

@section('content')
  @include('partials.hero')
  @include('partials.about')
  @include('partials.skills')
  @include('partials.resume')
  @include('partials.portfolio')
  @include('partials.testimonials')
  @include('partials.services')
  @include('partials.faq')
  @include('partials.contact')
@endsection
