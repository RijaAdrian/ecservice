@extends('template.admin')

@section('content')

    <div class="content-wrapper">

        @include('admin.content-header', [

            'title'=>ucfirst($place->nom),
            'link'=>[

                ['name'=>'Acceuil','url'=>route('admin.index')],
                ['name'=>'Region','url'=>route('territory.allCountry')]
                ['name'=>ucfirst($place->nom)]
            ]

        ])

        @include('admin.map-crud-template', [

            'title'=>ucfirst($place->nom),
            'action'=>action('Back\TerritoryController@updateCountry',$place->id),
            'placeholder'=>ucfirst($place->nom),
            'parent'=>$parent,
            'value'=>$place,
            'pl'=>$place->province_id,
            'appart'=>'Province'

        ])

    </div>

@endsection