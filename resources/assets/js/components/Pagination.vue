<template>
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm">
            <li class="page-item" :class="{ 'disabled': currentPage <= 1 }">
                <a aria-label="Previous" href="#" @click.prevent.stop="prev" class="page-link">
                    <span aria-hidden="true"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>
                </a>
            </li>
            <li v-for="value in shownPages" class="page-item page-item-fixed" :class="{ 'active': value == currentPage }">
                <a v-if="value != currentPage" href="#" @click.prevent="goTo(value)" class="page-link">{{ value }}</a>
                <span v-else class="page-link">{{ value }}<span class="sr-only">(current)</span></span>
            </li>
            <li class="page-item" :class="{ 'disabled': currentPage >= pageCount }">
                <a class="page-link" href="#" :disabled="currentPage >= pageCount" aria-label="Next" @click.prevent="next">
                    <span aria-hidden="true"><i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        props: [
            'currentPage', 'pageCount', 'paginationCount'
        ],

        computed: {
            shownPages() {
                if (this.pageCount <= this.paginationCount) return this.pageCount;
                let offSet = this.currentPage - Math.floor(this.paginationCount/2);
                let begin = 1;
                let range = [];
                if (offSet > 1) {
                    begin = offSet;
                    if((offSet + this.paginationCount) > this.pageCount){
                        begin = this.pageCount - this.paginationCount + 1;
                    }
                }

                for(let i = 0; i < this.paginationCount; i++) {
                    range[i] = begin++
                }
                return range;
            }
        },

        methods: {
            prev() {
                this.$emit('prev');
            },

            next() {
                this.$emit('next');
            },

            goTo(n) {
                this.$emit('go-to',n);
            }
        }
    }
</script>