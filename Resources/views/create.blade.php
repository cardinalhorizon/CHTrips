@extends('chtrips::layouts.frontend')

@section('title', 'Trips')

@section('content')
  <h1>Create Trip</h1>
  <div class="row">
    <div class="col-12">
      <div class="form-group search-form">
        {{ Form::open([
                'route' => 'chtrips.store',
                'method' => 'POST',
        ]) }}

        <div class="mt-1">
          <div class="form-group">
            <div>@lang('common.airline')</div>
            {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) }}
          </div>
        </div>
        <div class="mt-4">
          <div>Airport ICAOs (Separated by ',')</div>
          {{ Form::text('airports', null , ['class' => 'form-control']) }}
        </div>

        <div class="clear mt-4" style="margin-top: 10px;">
          {{ Form::submit("Create Trip", ['class' => 'btn btn-outline-primary']) }}&nbsp;
        </div>
      </div>
    </div>
  </div>
@endsection
