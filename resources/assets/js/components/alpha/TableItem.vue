<template>
    <tr>
        <column-item v-for="col in columns" :col="col" :char="char">
            <template slot="items" scope="props">
                <slot name="items"></slot>
            </template>
        </column-item>

        <template v-if="update">
            <td>{{ updatedAt }}</td>
            <td>
                <button class="btn btn-primary btn-sm float-right" v-on:click="updateCharacter(char)" :disabled="disableUpdate || char.update.updating">
                    <i class="fa fa-refresh fa-lg" v-bind:class="{ 'fa-spin': char.update.updating }" title="Update Character"></i>
                </button>
            </td>
        </template>
    </tr>
</template>

<script>
    import moment from 'moment-timezone';
    import ColumnItem from '../components/ColumnItem';

    export default {
        props: [
            'columns', 'char', 'update', 'sortCol'
        ],

        components: { ColumnItem },

        computed: {
            disableUpdate() {
                return moment().diff(moment(moment.tz(this.char.updated_at, 'UTC')),'s') < 120;
            },

            updatedAt() {
                if(this.char.updated_at) {
                    return moment(moment.tz(this.char.updated_at, 'UTC')).fromNow();
                } else {
                    return 'Press Update!';
                }
            }
        },

        methods: {
            updateCharacter(char) {
                this.$emit('update', char);
            },
        }
    }
</script>