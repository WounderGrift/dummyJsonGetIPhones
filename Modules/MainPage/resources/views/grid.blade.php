@extends('main::layouts.main')
@section('content')
    <div class="review">
        <div class="container">
            <h2 class="title">{{ $title }}</h2>
            <input type="checkbox" id="nav-toggle" hidden>

            <div class="games-skeleton-list">
                @for ($key = 0; $key < 8; $key++)
                    @if ($key % 4 == 0)
                        <div class="row align-items-start">
                    @endif

                    <div class="col-md-4 sed-md">
                        <x-skeleton-loader style="width: 275px; height: 310px"></x-skeleton-loader>
                    </div>

                    @if (($key + 1) % 4 == 0 || $key == 7)
                        <div class="clearfix"></div>
                        </div>
                    @endif
                @endfor
            </div>
        </div>
    </div>

    @if (isset($jsFile))
        <script src="{{ asset($jsFile) }}?version={{config('app.version')}}"></script>
    @endif

@endsection
