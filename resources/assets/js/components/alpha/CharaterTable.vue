<template>
        <table class="table table-sm">
            <colgroup :span="header.cols" v-for="header in headers" class="right-border"></colgroup>
            <thead>
                <tr class="table-overhead" v-if="headers">
                    <th v-for="header in headers" :colspan="header.cols">{{ header.name }}</th>
                    <th colspan="2" v-if="update"></th>
                </tr>
                <tr class="table-head">
                    <th v-for="col in columns" v-on:click="sort(col)" :class="{ 'sort-container': !col.noSort }">
                        {{ col.name }}
                        <i v-if="!col.noSort" class="fa fa-sort-asc sort-icon-asc" :class="{ 'sort-active': (col.id == sortCol) && !sortDesc }" aria-hidden="true"></i>
                        <i v-if="!col.noSort" class="fa fa-sort-desc sort-icon-desc" :class="{ 'sort-active': (col.id == sortCol) && sortDesc }" aria-hidden="true"></i>
                    <th colspan="2" v-if="update">Updated</th>
                </tr>
            </thead>
            <tbody>
                <table-item v-for="char in characters" v-on:update="updateCharacter" :update="update" :char="char" :columns="columns" :class="{ 'update-table-item-success': char.update.updated }" :key="char.id">
                    <template slot="items" scope="props">
                        <slot name="items"></slot>
                    </template>
                </table-item>
                <tr v-if="characters.length == 0"><td :colspan="columns.length">No Characters Found.</td></tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import Guild from '../models/Guild';
    import Character from '../models/Character';
    import TableItem from '../components/TableItem';
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

        components: { TableItem },

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

            sort(col) {
                if(!col.noSort) {
                    this.$emit('sort', col.id);
                }
            },

            updateCharacter(char) {
                this.$emit('update', char);
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