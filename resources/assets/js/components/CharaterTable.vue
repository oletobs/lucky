<template>
        <table class="character-list-table">
            <colgroup :span="header.cols" v-for="header in headers" class="right-border"></colgroup>
            <thead>
                <tr class="table-overhead">
                    <th v-for="header in headers" :colspan="header.cols">{{ header.name }}</th>
                    <th colspan="2" v-if="update"></th>
                </tr>
                <tr class="table-head">
                    <th v-for="col in columns" v-on:click="sort(col.id)" class="sort-container">
                        {{ col.name }}
                        <i class="fa fa-sort-asc sort-icon-asc" :class="{ 'sort-active': (col.id == sortCol) && !sortDesc }" aria-hidden="true"></i>
                        <i class="fa fa-sort-desc sort-icon-desc" :class="{ 'sort-active': (col.id == sortCol) && sortDesc }" aria-hidden="true"></i>
                    <th colspan="2" v-if="update">Updated</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="char in characters" :class="{ 'update-table-item-success': char.update.updated }">
                    <td v-for="col in columns" :class="[col.applyClass ? slugify(char[col.class_name]) : '', (col.id == sortCol) ? 'sort-active-column' : '']">{{ getPropByString(char,col.id) }}</td>

                    <template v-if="update">
                        <td>{{ updatedAt(char) }}</td>
                        <td>
                            <button class="list-button" v-on:click="updateCharacter(char)" :disabled="disableUpdate(char)">
                                <i class="fa fa-refresh fa-lg" v-bind:class="{ 'fa-spin': char.update.updating }" title="Update Character"></i>
                            </button>
                        </td>
                    </template>
                </tr>
            <tr v-if="characters.length == 0"><td :colspan="columns.length">No Characters Found.</td></tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import Guild from '../models/Guild';
    import Character from '../models/Character';
    import moment from 'moment-timezone';

    export default {
        props: [
            'headers', 'columns', 'characters', 'update', 'sortCol', 'sortDesc'
        ],

        watch: {
            '$route': 'routeChanged'
        },

        computed: {

        },

        created() {

        },

        methods: {
            routeChanged() {

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

            sort(colId) {
                this.$emit('sort', colId);
            },

            updateCharacter(char) {
                this.$emit('update', char);
            },

            updatedAt(char) {
                if(char.updated_at) {
                    return moment(moment.tz(char.updated_at, 'UTC')).fromNow();
                } else {
                    return 'Press Update!';
                }

            },

            disableUpdate(char) {
                return moment().diff(moment(moment.tz(char.updated_at, 'UTC')),'s') < 120;
            },

            slugify(str) {
                return str.replace(/\s+/g, '-').toLowerCase();
            },
        }
    }
</script>