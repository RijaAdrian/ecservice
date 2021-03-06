@extends('template.admin')

@section('content')

    <div class="content-wrapper">

        @include('admin.content-header', [

            'title'=>'Commune',
            'link'=>[

                ['name'=>'Acceuil','url'=>route('admin.index')],

                ['name'=>'Commune']
            ]

        ])

        @include('admin.map-template', [
            'title'=>'Commune',
            'function'=>'Town',
            'action'=>action('Back\TerritoryController@createTown'),
            'placeholder'=>'Commune',
            'parent'=>$parent,
            'appart'=>'District'
        ])

    </div>




    @endsection
