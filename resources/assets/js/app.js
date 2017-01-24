import './bootstrap';
import router from './routes';
import GuildSearch from './components/GuildSearch.vue';


new Vue({
    el: '#app',

    data: {
        loading: true,
        loadingMessage: null,
        error: false,
        errorMessage: null
    },

    components: { GuildSearch },

    methods: {
        childLoading(msg) {
            this.loading = true;
            this.error = false;
            this.loadingMessage = msg;
        },

        childLoaded() {
            this.loading = false;
        },

        childError(msg) {
            this.error = true;
            this.errorMessage = msg;
        },
    },

    router
});


