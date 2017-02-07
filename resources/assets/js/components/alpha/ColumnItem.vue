<template>
    <td v-if="!col.slot" :class="[col.applyClass ? sluggedClassName : '', (col.id == sortCol) ? 'sort-active-column' : '']">{{ columnData }}</td>
    <td v-else><slot :name="col.slotName" :item="columnData"></slot></td>
</template>

<script>
    export default {
        props: [
            'char', 'col', 'sortCol'
        ],

        computed: {
            columnData() {
                let propString = this.col.id;
                let obj = this.char;

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

            sluggedClassName() {
                return this.char.class_name.replace(/\s+/g, '-').toLowerCase();
            }
        },

        methods: {

        }
    }
</script>