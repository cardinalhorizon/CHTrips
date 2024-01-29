@extends('chtrips::layouts.frontend')

@section('title', 'Trips')

@section('content')
  <h1>Create Trip</h1>
  <div class="row">
    <div class="col-4">
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
          <div>Airports</div>
          <div id="airport_container">

          </div>
          <a href="#" class="btn btn-success" id="btnAddAirport">Add</a>

        </div>

        <div class="clear mt-4" style="margin-top: 10px;">
          {{ Form::submit("Create Trip", ['class' => 'btn btn-outline-primary']) }}&nbsp;
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
<div class="my-2 d-flex flex-row" id="airport-${id}">
              <select class="custom-select airport_search my-auto" name="airports[]" style="width: 100%"></select>
              <a href="#" class="btn btn-danger btn-sm ml-2 delete" onclick="delInput(${id})">X</a>
            </div>
`;
    }
    $(document).ready(function () {

      $("#btnAddAirport").on("click", () => {
        let date = new Date();
        $("#airport_container").append(selectInput(date.getTime()))
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
