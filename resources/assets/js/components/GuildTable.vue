<template>
        <table class="table table-sm table-responsive">
            <colgroup>
                <col class="guild-name">
            </colgroup>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Region</th>
                    <th>Server</th>
                    <th>Members @ 110</th>
                    <th>Last Updated</th>
                    <th class="text-right">Update</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="guild in guilds">
                    <td><router-link :to="{ name: 'guild', params: {
                        guild: guild.name.replace(/\s+/g, '-').toLowerCase(),
                        region: guild.server.region.short,
                        server: guild.server.name.toLowerCase()
                    }}">{{ guild.name }}</router-link></td>
                    <td>{{ guild.server.region.name }}</td>
                    <td>{{ guild.server.name }}</td>
                    <td>{{ guild.members_count }}</td>
                    <td>{{ updatedAt(guild) }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" v-on:click="updateGuild(guild)">
                            <i class="fa fa-refresh" v-bind:class="{ 'fa-spin': guild.updating }" title="Update Guild"></i> {{ guild.message }}
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
</template>

<script>
    import Guild from '../models/Guild';
    import moment from 'moment-timezone';

    export default {

        data() {
            return {
                guilds: [],
            }
        },

        created() {
            this.fetchGuilds();
        },

        methods: {
            fetchGuilds() {
                this.$emit('loading');
                Guild.all()
                    .then(response => {
                        response.data.data.forEach(g => {
                            g.updateDisabled = true;
                            g.updating = false;
                            g.message = 'Update';
                        });
                        this.guilds = response.data.data; // Paginator, which is why it is data.data
                        this.$emit('loaded');
                    })
                    .catch(error => {
                        this.$emit('error');
                    });

            },

            updateGuild(guild) {
                guild.updating = true;
                Guild.update(guild.server.region.short,guild.server.name.toLowerCase(),guild.slug)
                    .then(response => {
                        guild.members_count = response.data.members_count;
                        guild.updated_at = response.data.updated_at;
                        guild.updating = false;
                    })
                    .catch(error => {
                        guild.message = 'Failed';
                        guild.updating = false;
                    });
            },

            updatedAt(guild) {
                return moment(moment.tz(guild.updated_at, 'UTC')).fromNow();
            }
        }
    }
</script>
