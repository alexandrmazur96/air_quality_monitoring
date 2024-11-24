@extends('layouts.default')

@section('content')
    <city-details :city="{{ json_encode($city) }}" :measurements="{{ json_encode($measurements) }}" :aqis="{{ json_encode($aqis) }}" />
@endsection
