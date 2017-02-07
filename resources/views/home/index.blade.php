@extends('layout.base')

@section('content')
    <div id="app">
        <guild-search></guild-search>

        <keep-alive>
            <router-view v-on:error="childError" v-on:loaded="childLoaded" v-on:loading="childLoading"></router-view>
        </keep-alive>

        <div v-show="loading" class="row">
            <div class="col text-center">
                <div class="loading-message" v-text="loadingMessage"></div>
                <img src="{{ asset('ring.svg') }}">
            </div>
        </div>

        <div v-if="error" class="row">
            <div class="col text-center">
                <div class="error-message" v-text="errorMessage"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>var regions = {!! $regions !!}</script>
    @parent
@stop