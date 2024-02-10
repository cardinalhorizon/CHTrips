@extends('chtrips::layouts.frontend')

@section('title', 'Trips')

@section('content')
  <h1>Create Trip</h1>
  <div class="row">
    <div class="col-6">
      <div class="form-group search-form">
        {{ Form::open([
                'route' => 'chtrips.store',
                'method' => 'POST',
        ]) }}

        <div class="mt-1">
          <div class="form-group">
            <div>@lang('common.airline')</div>
            {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2 my-auto']) }}
          </div>
        </div>
        <div class="mt-4">
          <div>Start Airport</div>
          <select class="custom-select airport_search my-auto" name="airports[]" style="width: 100%"></select>
          <div class="mt-4">Legs</div>
          <div id="airport_container">

          </div>
          <a href="#" class="btn btn-success" id="btnAddAirport">+</a>
        </div>

        <div class="clear mt-4" style="margin-top: 10px;">
          {{ Form::submit("Create Trip", ['class' => 'btn btn-outline-primary']) }}&nbsp;
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-sm-12">
      <div class="card">
        <div class="card-header">Additional Settings</div>
        <div class="card-body">
          @ability('admin', 'admin-access')
          <div>Assign To User</div>
          <select class="form-control select2 my-auto" name="user_id">
            @foreach(\App\Models\User::all() as $user)
              <option value="{{$user->id}}" @if($user->id == Auth::user()->id) selected @endif>{{$user->ident}} | {{$user->name}}</option>
            @endforeach
          </select>
          @endability
          <div class="input-group input-group-sm mt-3">
            {{ Form::text('flight_number', null, [
                'placeholder' => "Flight Number (optional)",
                'class' => 'form-control',
            ]) }}
            &nbsp;
            {{ Form::text('route_code', null, [
                'placeholder' => __('pireps.codeoptional'),
                'class' => 'form-control',
            ]) }}

          </div>
          <div class="form-group mt-3">
            <label>Trip Name</label>
            <input type="text" class="form-control" name="name"/>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea type="textarea" class="form-control" name="description"></textarea>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@section('scripts')
  <script>
    function delInput(id) {
      $(`#airport-${id}`).remove();
    }
    function selectInput(id) {
      return `
<div class="my-2" id="airport-${id}">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col">
<div>Flight Type</div>
<select class="form-control select2" style="width: 100%" name="flight_type[]"><option value="C">Passenger (Charter)</option><option value="A">Cargo (Additional)</option><option value="E">VIP Flight</option><option value="G">Passenger (Additional)</option><option value="H">Cargo (Charter)</option><option value="I">Ambulance</option><option value="K">Training</option><option value="M">Mail Service</option><option value="O">Passenger (Special Charter)</option><option value="P">Positioning</option><option value="T">Technical Test</option><option value="W">Military</option><option value="X">Technical Stop</option></select>
</div>
<div class="col">
<div>Destination</div>
              <select class="custom-select airport_search my-auto" name="airports[]" style="width: 100%"></select>
</div>
</div>
</div>
              <a href="#" class="btn btn-danger btn-sm delete btn-block" onclick="delInput(${id})">X</a>
            </div>
`;
    }
    $(document).ready(function () {

      $("#btnAddAirport").on("click", () => {
        let date = new Date();
        $("#airport_container").append(selectInput(date.getTime()))
        $("select.select2").select2();
        $("select.airport_search").select2({
          ajax: {
            url: '{{ Config::get("app.url") }}/api/airports/search',
            data: function (params) {
              const hubs_only = $(this).hasClass('hubs_only') ? 1 : 0;
              return {
                search: params.term,
                hubs: hubs_only,
                page: params.page || 1,
                orderBy: 'id',
                sortedBy: 'asc'
              }
            },
            processResults: function (data, params) {
              if (!data.data) { return [] }
              const results = data.data.map(apt => {
                return {
                  id: apt.id,
                  text: apt.description,
                }
              })

              const pagination = {
                more: data.meta.next_page !== null,
              }

              return {
                results,
                pagination,
              };
            },
            cache: true,
            dataType: 'json',
            delay: 250,
            minimumInputLength: 2,
          },
          width: 'resolve',
          placeholder: 'Type to search',
        });

      });
    });
  </script>
  @include('scripts.airport_search')

@endsection
