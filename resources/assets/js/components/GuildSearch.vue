<template>
        <div class="guild-search">
            <div class="col-xs-6">
                <input type="text" id="guild-search-name" class="form-control" placeholder="Guild Name" v-model="guildName">
            </div>
            <div class="col-xs-2">
                <select class="form-control" id="guild-search-region" v-model="regionSelected">
                    <option v-for="region in regions" v-bind:value="region">{{ region.short }}</option>
                </select>
            </div>
            <div class="col-xs-3">
                <select class="form-control" id="guild-search-server" v-model="serverSelected">
                    <option v-for="server in regionSelected.servers" v-bind:value="server">{{ server.name }}</option>
                </select>
            </div>
            <div class="col-xs-1">
                <button class="btn btn-default" v-on:click="findGuild">
                    <i class="fa fa-search fa-lg" aria-hidden="true"></i>
                </button>
            </div>
        </div>
</template>

<script>
    export default {

        data() {
            return {
                regions: regions,
                regionSelected: regions[0],
                serverSelected: regions[0].servers[0],
                guildName: null
            }
        },

        methods: {
            findGuild() {
                this.$router.push({ name: 'guild', params: {
                    guild: this.guildName.replace(/\s+/g, '-').toLowerCase(),
                    region: this.regionSelected.short,
                    server: this.serverSelected.name.toLowerCase()
                }})
            }
        }
    }
</script>