@extends('layout.base')

@section('content')
    <div id="app">
        <div class="guild-search-container">
            <section>
                <guild-search></guild-search>
            </section>
        </div>

        <div class="wow-data-container">
            <section>
                <router-view v-on:error="childError" v-on:loaded="childLoaded" v-on:loading="childLoading"></router-view>

                <div v-show="loading" class="loader">
                    <div class="message-wrapper">
                        <div class="loading-message" v-text="loadingMessage"></div>
                        <img src="{{ asset('ring.svg') }}">
                    </div>
                </div>

                <div v-if="error" class="error">
                    <div class="message-wrapper">
                        <div class="error-message" v-text="errorMessage"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script>var regions = {!! $regions !!}</script>
    @parent
@stop