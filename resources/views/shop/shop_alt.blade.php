@extends('layouts.template')

@section('title', 'Record')

@section('main')
    <h1>shop_alternative</h1>

    @foreach($genres as $genre)
            <h2>{{ $genre->name }}</h2>
            <ul>
    @foreach($records as $record)
        @if($genre->id == $record->genre_id)
            <li><p><a href="">{{ $record->artist }} - {{ $record->title }}</a> | Price: € {{ number_format($record->price,2) }} | Stock: {{ $record->stock }}</p></li>
        @endif
    @endforeach
        </ul>
    @endforeach

@endsection
