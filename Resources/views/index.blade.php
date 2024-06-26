@extends('chtrips::layouts.frontend')

@section('title', 'CHTrips')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div style="float:right;">
        <a class="btn btn-outline-info pull-right btn-lg"
           style="margin-top: -10px;margin-bottom: 5px"
           href="{{ route('chtrips.create') }}">Create New Trip</a>
      </div>
      <h2>My Trips</h2>
      @include('flash::message')
      @include('chtrips::trips_table')
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-center">
  {{-- }}{{ $trips->links('pagination.default') }} --}}
</div>
</div>
@endsection
