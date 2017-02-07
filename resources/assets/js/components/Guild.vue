<template>
    <div v-if="!hidden" class="row">
        <div class="col">
            <div class="input-group input-group-sm">
                <div class="input-group-btn">
                    <router-link tag="button" class="btn btn-primary" :to="{ name: 'guilds' }"><i class="fa fa-home fa-lg" aria-hidden="true"></i></router-link>
                </div>
                <input type="text" id="character-list-filter" class="form-control" placeholder="Filter character by any data" v-model="search">
                <div class="input-group-btn">
                    <router-link active-class="nav-active-button" tag="button" class="btn btn-primary" :to="{ path: 'stats' }"><i class="fa fa-list fa-lg" aria-hidden="true"> </i> Stats</router-link>
                </div>
                <div class="input-group-btn">
                    <router-link active-class="nav-active-button" tag="button" class="btn btn-primary" :to="{ path: 'items' }"><i class="fa fa-gavel fa-lg" aria-hidden="true"></i> </i> Items</router-link>
                </div>
                <div class="input-group-btn">
                    <router-link active-class="nav-active-button" tag="button" class="btn btn-primary" :to="{ path: 'rankings' }"><i class="fa fa-line-chart fa-lg" aria-hidden="true"> </i> Rankings</router-link>
                </div>
            </div>
        </div>
        <div class="col col-auto">
            <pagination v-on:prev="currentPage--"
                        v-on:next="currentPage++"
                        v-on:go-to="currentPage = arguments[0]"
                        :current-page="currentPage"
                        :page-count="pageCount"
                        :pagination-count="5">
            </pagination>
        </div>
        <div class="col-12">
            <character-table v-on:update="updateCharacter"
                             v-on:sort="sort"
                             :sort-col="sortCol"
                             :sort-desc="sortDesc"
                             :update="true" :headers="view.headers"
                             :columns="view.columns"
                             :characters="paginatedMembers">
                <template slot="items" scope="props">
                    <td>
                        <a v-if="props.item" v-for="item in props.item" class="item-icon" target="_blank" :class="'item-quality-'+item.quality"  :href="'http://www.wowhead.com/item='+item.id+'&bonus='+item.bonus.join(':')">
                            <img :src="'http://media.blizzard.com/wow/icons/36/'+item.icon+'.jpg'" class="rounded-circle" alt="item icon">
                            <span class="item-icon-text">{{ item.item_level }}</span>
                        </a>
                    </td>
                </template>
            </character-table>

            <div class="row justify-content-end">
                <div class="col col-auto">
                    <pagination v-on:prev="currentPage--"
                                v-on:next="currentPage++"
                                v-on:go-to="currentPage = arguments[0]"
                                :current-page="currentPage"
                                :page-count="pageCount"
                                :pagination-count="5">
                    </pagination>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import Guild from '../models/Guild';
    import Character from '../models/Character';
    import CharacterTable from './CharaterTable';
    import Pagination from './Pagination';

    export default {
        data() {
            return {
                guild: null,
                view: '',
                hidden: true,
                search: '',
                sortCol: '',
                sortDesc: false,
                paginateCount: 20,
                currentPage: 1,
                views: {
                    stats: {
                        headers: [
                            { cols: 3, name: 'Character Information' },
                            { cols: 11, name: 'Mythic Dungeons' },
                            { cols: 3, name: 'M+ In Time' },
                            { cols: 5, name: 'Raid Bosses Downed' },
                            { cols: 1, name: 'WQs' },
                            { cols: 2, name: 'Artifact' },
                        ],
                        columns: [
                            { id:'name', name:'Name' },
                            { id:'class_name', name:'Class', applyClass: true, class_name: 'class_name' },
                            { id:'specc', name:'Spec' },
                            { id:'stats.kills.10880', name:'EoA' },
                            { id:'stats.kills.10883', name:'DHT' },
                            { id:'stats.kills.10886', name:'NL' },
                            { id:'stats.kills.10889', name:'HoV' },
                            { id:'stats.kills.10898', name:'VotW' },
                            { id:'stats.kills.10901', name:'BRH' },
                            { id:'stats.kills.10904', name:'MoS' },
                            { id:'stats.kills.10907', name:'Arc' },
                            { id:'stats.kills.10910', name:'CoS' },
                            { id:'stats.kills.11406', name:'Kara' },
                            { id:'stats.total_mythic_dungeons', name:'Total' },
                            { id:'stats.total_mythic_2', name:'M+2' },
                            { id:'stats.total_mythic_5', name:'M+5' },
                            { id:'stats.total_mythic_10', name:'M+10' },
                            { id:'stats.total_lfr_raid_bosses', name:'LFR' },
                            { id:'stats.total_normal_raid_bosses', name:'Normal' },
                            { id:'stats.total_heroic_raid_bosses', name:'Heroic' },
                            { id:'stats.total_mythic_raid_bosses', name:'Mythic' },
                            { id:'stats.total_raid_bosses', name:'Total' },
                            { id:'stats.total_wq', name:'Total' },
                            { id:'stats.total_ap', name:'Total AP', formatNumber: true },
                            { id:'stats.highest_artifact_ilvl', name:'Highest lvl' }
                        ]
                    },
                    rankings: {
                        headers: [
                            { cols: 3, name: 'Character Information' },
                            { cols: 3, name: 'Emerald Nightmare Average Rankings' },
                            { cols: 3, name: 'Trial of Valor Average Rankings' },
                            { cols: 3, name: 'Nighthold Average Rankings' },
                        ],
                        columns: [
                            { id:'name', name:'Name' },
                            { id:'class_name', name:'Class', applyClass: true, class_name: 'class_name' },
                            { id:'specc', name:'Spec' },
                            { id: 'average_rank_en_normal', name:'Normal' },
                            { id: 'average_rank_en_heroic', name:'Heroic' },
                            { id: 'average_rank_en_mythic', name:'Mythic' },
                            { id: 'average_rank_tov_normal', name:'Normal' },
                            { id: 'average_rank_tov_heroic', name:'Heroic' },
                            { id: 'average_rank_tov_mythic', name:'Mythic' },
                            { id: 'average_rank_nh_normal', name:'Normal' },
                            { id: 'average_rank_nh_heroic', name:'Heroic' },
                            { id: 'average_rank_nh_mythic', name:'Mythic' },
                        ]
                    },
                    items: {
                        headers: [
                            { cols: 3, name: 'Character Information' },
                            { cols: 3, name: 'Item Information' },
                            { cols: 4, name: 'Item Src: Raid' },
                            { cols: 3, name: 'Item Src: Dungeon' },
                            { cols: 4, name: 'Item Level Information' },
                        ],
                        columns: [
                            { id:'name', name:'Name' },
                            { id:'class_name', name:'Class', applyClass: true, class_name: 'class_name' },
                            { id:'specc', name:'Spec' },
                            { id:'stats.artifact', name:'Artifact', slot: true, slotName: 'items', noSort: true },
                            { id:'stats.relics', name:'Relics', slot: true, slotName: 'items', noSort: true },
                            { id:'stats.items', name:'Items', slot: true, slotName: 'items', noSort: true },
                            { id:'stats.equipped_raid_normal', name:'N' },
                            { id:'stats.equipped_raid_heroic', name:'H' },
                            { id:'stats.equipped_raid_mythic', name:'M' },
                            { id:'stats.equipped_raid_total', name:'Total' },
                            { id:'stats.equipped_dungeon_mythic_plus', name:'M/M+' },
                            { id:'stats.equipped_weekly_chest', name:'WC' },
                            { id:'stats.equipped_dungeon_total', name:'Total' },
                            { id:'stats.equipped_ilvl', name:'Eq' },
                            { id:'stats.average_ilvl', name:'Avg' },
                            { id:'stats.equipped_average_ilvl_diff', name:'Diff' },
                            { id:'stats.artifact_ilvl', name:'Artifact' },
                        ]
                    }
                }
            }
        },

        components: {
            CharacterTable, Pagination
        },

        created () {
            if(this.views[this.$route.params.view] !== undefined) {
                this.view = this.views[this.$route.params.view];
            }
            this.fetchGuild();
        },

        watch: {
            '$route': 'routeChanged',
            'search': 'test'
        },

        computed: {
            filteredMembers() {
                //console.clear();
                if(this.search == '') {
                    //console.log('no filter');
                    return this.guild.members;
                }
                return this.guild.members.filter(member => {
                    //console.log('filter');
                    for (let key in member) {
                        if (member.hasOwnProperty(key) && typeof(member[key]) == 'string') {
                            if(this.search.indexOf('=') == 0) {
                                if (member[key].toLowerCase() == this.search.substring(1).toLowerCase()) {
                                    return true;
                                }
                            } else {
                                if (member[key].toLowerCase().indexOf(this.search.toLowerCase()) > -1) {
                                    return true;
                                }
                            }
                        }
                    }
                });
            },

            paginatedMembers() {
                let begin = (this.currentPage-1) * this.paginateCount;
                let end = Math.min(begin+this.paginateCount, this.filteredMembers.length);
                return this.filteredMembers.slice(begin,end);
            },

            pageCount() {
                return Math.ceil(this.filteredMembers.length / this.paginateCount);
            }
        },

        methods: {
            routeChanged() {
                if(this.$route.params.guild !== this.guild.slug && this.$route.params.guild !== undefined) {
                    this.fetchGuild();
                }
                if(this.views[this.$route.params.view] !== undefined) {
                    this.view = this.views[this.$route.params.view];
                }
            },

            test() {
                console.log('yolo');
                if(this.currentPage > this.pageCount) {
                    this.currentPage = this.pageCount;
                } else if(this.currentPage < 1 && this.pageCount > 0) {
                    this.currentPage = 1;
                }
            },

            fetchGuild() {
                this.hidden = true;
                this.$emit('loading', 'Checking cache for guild');
                Guild.get(this.$route.params.region,this.$route.params.server,this.$route.params.guild)
                    .then(this.loadedGuild)
                    .catch(error => {
                        this.$emit('loading', 'Guild not in cache, fetching from blizzard');
                        Guild.getBlizz(this.$route.params.region,this.$route.params.server,this.$route.params.guild)
                            .then(this.loadedGuild)
                            .catch(error => {
                                this.$emit('error', 'Guild not found.')
                                this.$emit('loaded');
                            });
                });
            },

            loadedGuild(response) {
                response.data.members.forEach(c => {
                    this.$set(c, 'update', {
                        updated: false,
                        updating: false,
                        message: 'Update'
                    })
                });
                this.guild = response.data;
                this.$emit('loaded');
                this.hidden = false;
            },

            updateCharacter(char) {
                char.update.updating = true;
                Character.update(char.id)
                    .then(response => {
                        char.update.updated = true;
                        char.update.updating = false;
                        char.specc = response.data.specc;
                        char.stats = response.data.stats;
                        char.updated_at = response.data.updated_at;

                        setTimeout((function(){
                            char.update.updated = false;
                        }).bind(char), 1500);
                    })
                    .catch(error => {
                        char.update.updating = false;
                    });
            },

            sort(colId) {
                if(this.sortCol == colId) {
                    this.sortDesc = !this.sortDesc;
                } else {
                    this.sortDesc = false;
                    this.sortCol = colId;
                }

                this.guild.members.sort((a, b) => {
                    //console.log('sort');
                    if(this.sortDesc) {
                        let c = a;
                        a = b;
                        b = c;
                    }
                    let valueA = this.getPropByString(a,this.sortCol);
                    let valueB = this.getPropByString(b,this.sortCol);
                    if(valueA == undefined) {
                        valueA = -1;
                    }

                    if(valueB == undefined) {
                        valueB = -1;
                    }

                    if(typeof(valueA) == 'number' && typeof(valueB) == 'number') {
                        return valueA - valueB;
                    } else if(typeof(valueA) == 'string') {
                        var nameA = valueA.toLowerCase();
                        var nameB = valueB.toLowerCase();
                        if (nameA < nameB) {
                            return -1;
                        }
                        if (nameA > nameB) {
                            return 1;
                        }
                        return 0;
                    }
                });
            },

            getPropByString(obj, propString) {
                if (!propString)
                    return obj;

                var prop, props = propString.split('.');

                for (var i = 0, iLen = props.length - 1; i < iLen; i++) {
                    if(obj == null) {
                        return undefined;
                    }

                    prop = props[i];

                    var candidate = obj[prop];
                    if (candidate !== undefined) {
                        obj = candidate;
                    } else {
                        break;
                    }

                }
                if(obj == null) {
                    return undefined;
                }

                return obj[props[i]];
            },


        }
    }
</script>