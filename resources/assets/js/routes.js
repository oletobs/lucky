import VueRouter from 'vue-router';

let routes = [
    {
        path: '/',
        name: 'guilds',
        component: require('./components/GuildTable.vue')
    },
    {
        path: '/guilds/:region/:server/:guild',
        name: 'guild',
        redirect: '/guilds/:region/:server/:guild/stats',
    },
    {
        path: '/guilds/:region/:server/:guild/:view',
        name: 'guild_view',
        component: require('./components/Guild.vue')
    },
    {
        path: '/*',
        redirect: '/'
    }
];

export default new VueRouter({
    routes
});