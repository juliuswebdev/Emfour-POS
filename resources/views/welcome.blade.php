@extends('layouts.home')
@section('title', config('app.name', 'ultimatePOS'))

@section('content')
    <style type="text/css">
        .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
                margin-top: 10%;
            }
        .title {
                font-size: 58px;
            }
        .tagline {
                font-size:25px;
                font-weight: 300;
                text-align: center;
            }

        @media only screen and (max-width: 600px) {
            .title{
                font-size: 36px;
                text-align: center;
            }
            .tagline {
                font-size:18px;
            }
        }

        #footer-navigation{
            position: absolute;
            bottom:0px;
        }
    </style>
    <div>
        <div class="title flex-center" style="font-weight: 600 !important;">
            Welcome to Maxximu Software
        </div>
        {{-- <p class="tagline">
            {{ env('APP_TITLE', '') }}
        </p> --}}
    </div>
@endsection
            