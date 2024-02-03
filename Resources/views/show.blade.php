@extends('chtrips::layouts.frontend')

@section('title', 'CHTrips')

@section('content')

  <h1>{{$name}}</h1>
  <p>{{$description}}</p>
  <div class="mb-4">
    <div class="text-center">Trip Progress: {{$progress}}%</div>
    <div class="progress">
      <div class="progress-bar" role="progressbar" style="width: {{$progress}}%" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>
  @include('pireps.table')
  @if($flight)
    <h3>Next Flight</h3>
    <div class="card">
      <div class="card-body" style="min-height: 0">
        <div class="row">
          <div class="col-sm-12">
            <div>{{ $flight->airline->name }} <span class="float-right">{{\App\Models\Enums\FlightType::label($flight->flight_type)}}</span></div>
            <div style="font-size: 32px; line-height: 32px; font-weight: 600">


              {{ $flight->ident }}
              @if(filled($flight->callsign) && !setting('simbrief.callsign', true))
                {{ '| '. $flight->atc }}
              @endif
              <span class="float-right">{{$flight->dpt_airport_id}}->{{$flight->arr_airport_id}}</span>
            </div>
          </div>
          <div class="col-sm-3 align-top text-right">
            {{--
            !!! NOTE !!!
             Don't remove the "save_flight" class, or the x-id attribute.
             It will break the AJAX to save/delete

             "x-saved-class" is the class to add/remove if the bid exists or not
             If you change it, remember to change it in the in-array line as well
            --}}

          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            {{--
            <span class="title">{{ strtoupper(__('flights.dep')) }}&nbsp;</span>
            {{ optional($flight->dpt_airport)->name ?? $flight->dpt_airport_id }}
            (<a href="{{route('frontend.airports.show', ['id' => $flight->dpt_airport_id])}}">{{$flight->dpt_airport_id}}</a>)
            @if($flight->dpt_time), {{ $flight->dpt_time }}@endif
            <br/>
            <span class="title">{{ strtoupper(__('flights.arr')) }}&nbsp;</span>
            {{ optional($flight->arr_airport)->name ?? $flight->arr_airport_id }}
            (<a href="{{route('frontend.airports.show', ['id' => $flight->arr_airport_id])}}">{{$flight->arr_airport_id}}</a>)
            @if($flight->arr_time), {{ $flight->arr_time }}@endif
            <br/>
            @if(filled($flight->callsign) && !setting('simbrief.callsign', true))
              <span class="title">{{ strtoupper(__('flights.callsign')) }}&nbsp;</span>
              {{ $flight->atc }}
              <br/>
            @endif
            @if($flight->distance)
              <span class="title">{{ strtoupper(__('common.distance')) }}&nbsp;</span>
              {{ $flight->distance }} {{ setting('units.distance') }}
              <br/>
            @endif
            @if($flight->level)
              <span class="title">{{ strtoupper(__('flights.level')) }}&nbsp;</span>
              {{ $flight->level }} {{ setting('units.altitude') }}
              <br/>
            @endif
            --}}
            @if($flight->subfleets)
              <span class="title">Subfleets: </span>
              @php
                $arr = [];
                foreach ($flight->subfleets as $sf) {
                    $tps = explode('-', $sf->type);
                    $type = last($tps);
                    $arr[] = "{$sf->type}";
                }
              @endphp
              {{implode(", ", $arr)}}
              <br/>
            @endif
          </div>
          <div class="col-sm-5">
            @if($flight->route)
              <span class="title">{{ strtoupper(__('flights.route')) }}&nbsp;</span>
              {{ $flight->route }}
            @endif
          </div>
        </div>
      </div>
      <div class="card-footer">
        <a class="btn btn-sm btn-outline-info" href="{{ route('frontend.flights.show', [$flight->id]) }}">More Info</a>
        @if ($acars_plugin)
          @if (isset($saved[$flight->id]))
            <a href="vmsacars:bid/{{ $saved[$flight->id] }}" class="btn btn-sm btn-outline-primary">Load in vmsACARS</a>
          @else
            <a href="vmsacars:flight/{{ $flight->id }}" class="btn btn-sm btn-outline-primary">Load in vmsACARS</a>
          @endif
        @endif
        <!-- Simbrief enabled -->
        @if ($simbrief !== false)
          <!-- If this flight has a briefing, show the link to view it-->
          @if ($flight->simbrief && $flight->simbrief->user_id === $user->id)
            <a href="{{ route('frontend.simbrief.briefing', $flight->simbrief->id) }}"
               class="btn btn-sm btn-outline-primary">
              View Simbrief Flight Plan
            </a>
          @else
            <!-- Show button if the bids-only is disable, or if bids-only is enabled, they've saved it -->
            @if ($simbrief_bids === false || ($simbrief_bids === true && isset($saved[$flight->id])))
              @php
                $aircraft_id = isset($saved[$flight->id]) ? App\Models\Bid::find($saved[$flight->id])->aircraft_id : null;
              @endphp
              <a href="{{ route('frontend.simbrief.generate') }}?flight_id={{ $flight->id }}@if($aircraft_id)&aircraft_id={{ $aircraft_id }} @endif"
                 class="btn btn-sm btn-outline-primary">
                Create Simbrief Flight Plan
              </a>
            @endif
          @endif
        @endif

        <div class="float-right">
          <a href="{{ route('frontend.pireps.create') }}?flight_id={{ $flight->id }}"
             class="btn btn-sm btn-outline-info">
            {{ __('pireps.newpirep') }}
          </a>
          @if (!setting('pilots.only_flights_from_current') || $flight->dpt_airport_id == $user->current_airport->icao)
            <button class="btn btn-sm save_flight
                           {{ isset($saved[$flight->id]) ? 'btn-success':'btn-outline-success' }}"
                    x-id="{{ $flight->id }}"
                    x-saved-class="btn-success"
                    x-not-saved-class="btn-outline-success"
                    type="button"
                    title="@lang('flights.addremovebid')">
              {{isset($saved[$flight->id]) ? "Remove Bid" : "Add Bid"}}
            </button>
          @endif
        </div>
      </div>
    </div>
  @endif
  @if($upcoming)
  <h3 class="mt-4">Following Flights</h3>
  @foreach($upcoming as $flight)
    <div class="card">
      <div class="card-body" style="min-height: 0">
        <div class="row">
          <div class="col-sm-12">
            <div>{{ $flight->airline->name }} <span class="float-right">{{\App\Models\Enums\FlightType::label($flight->flight_type)}}</span></div>
            <div style="font-size: 32px; line-height: 32px; font-weight: 600">


              {{ $flight->ident }}
              @if(filled($flight->callsign) && !setting('simbrief.callsign', true))
                {{ '| '. $flight->atc }}
              @endif
              <span class="float-right">{{$flight->dpt_airport_id}}->{{$flight->arr_airport_id}}</span>
            </div>
          </div>
          <div class="col-sm-3 align-top text-right">
            {{--
            !!! NOTE !!!
             Don't remove the "save_flight" class, or the x-id attribute.
             It will break the AJAX to save/delete

             "x-saved-class" is the class to add/remove if the bid exists or not
             If you change it, remember to change it in the in-array line as well
            --}}

          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            {{--
            <span class="title">{{ strtoupper(__('flights.dep')) }}&nbsp;</span>
            {{ optional($flight->dpt_airport)->name ?? $flight->dpt_airport_id }}
            (<a href="{{route('frontend.airports.show', ['id' => $flight->dpt_airport_id])}}">{{$flight->dpt_airport_id}}</a>)
            @if($flight->dpt_time), {{ $flight->dpt_time }}@endif
            <br/>
            <span class="title">{{ strtoupper(__('flights.arr')) }}&nbsp;</span>
            {{ optional($flight->arr_airport)->name ?? $flight->arr_airport_id }}
            (<a href="{{route('frontend.airports.show', ['id' => $flight->arr_airport_id])}}">{{$flight->arr_airport_id}}</a>)
            @if($flight->arr_time), {{ $flight->arr_time }}@endif
            <br/>
            @if(filled($flight->callsign) && !setting('simbrief.callsign', true))
              <span class="title">{{ strtoupper(__('flights.callsign')) }}&nbsp;</span>
              {{ $flight->atc }}
              <br/>
            @endif
            @if($flight->distance)
              <span class="title">{{ strtoupper(__('common.distance')) }}&nbsp;</span>
              {{ $flight->distance }} {{ setting('units.distance') }}
              <br/>
            @endif
            @if($flight->level)
              <span class="title">{{ strtoupper(__('flights.level')) }}&nbsp;</span>
              {{ $flight->level }} {{ setting('units.altitude') }}
              <br/>
            @endif
            --}}
            @if($flight->subfleets)
              <span class="title">Subfleets: </span>
              @php
                $arr = [];
                foreach ($flight->subfleets as $sf) {
                    $tps = explode('-', $sf->type);
                    $type = last($tps);
                    $arr[] = "{$sf->type}";
                }
              @endphp
              {{implode(", ", $arr)}}
              <br/>
            @endif
          </div>
          <div class="col-sm-5">
            @if($flight->route)
              <span class="title">{{ strtoupper(__('flights.route')) }}&nbsp;</span>
              {{ $flight->route }}
            @endif
          </div>
        </div>
      </div>
    </div>
  @endforeach
  @endif
  @if (setting('bids.block_aircraft', false))
    @include('flights.bids_aircraft')
  @endif
@endsection
@include('flights.scripts')
