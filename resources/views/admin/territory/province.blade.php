@extends('template.admin')

@section('content')

    <div class="content-wrapper">

        @include('admin.content-header', [

            'title'=>'Province',
            'link'=>[

                ['name'=>'Acceuil','url'=>route('admin.index')],

                ['name'=>'Province']
            ]

        ])

        @include('admin.map-template', [
            'title'=>'Province',
            'function'=>'Province',
            'action'=>action('Back\TerritoryController@createProvince'),
            'placeholder'=>'Province',
            'parent'=>[]
        ])

    </div>



    @endsection
