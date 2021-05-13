<template>
    <v-select v-bind="$attrs"
              v-on="$listeners"
              :options="options"
              @open="onOpen"
              @close="onClose"
    >
        <template #list-footer="scope">
            <slot name="list-footer" v-bind="scope"></slot>
            <li v-show="hasMore" ref="loadItem" class="text-center text-gray-300">
                <slot name="loading-message">{{ loadingMessage }}</slot>
            </li>
        </template>
        <template #no-options="scope">
            <slot name="no-options" v-bind="scope">
                <template v-if="scope.loading || isLoading">
                    <slot name="loading-message">{{ loadingMessage }}</slot>
                </template>
                <template v-else>Niets gevonden...</template>
            </slot>
        </template>
        <template v-for="name in passedSlots" v-slot:[name]="data">
            <slot :name="name" v-bind="data" />
        </template>
    </v-select>
</template>

<script>
    import gql from 'graphql-tag';
    const ownSlots = ['loading-message', 'list-footer', 'no-option'];

    export default {
        name: "ConnectionSelect",
        props: {
            loadingMessage: {
                type: String,
                default: 'Laden...',
            },
            query: {
                type: Object,
                required: true,
            },
            connectionField: {
                type: String,
                default: 'connection',
            },
            connection: {
                type: Object
            }
        },
        data() {
            return {
                observer: null,
            }
        },
        fragment: gql`fragment ConnectionSelect on Connection {
            edges {
                node {
                    id
                }
            },
            pageInfo {
                hasNextPage
                endCursor
            }
        }`,
        computed: {
            isLoading() {
                return this.query.loading;
            },
            passedSlots() {
                return [...Object.keys(this.$slots), ...Object.keys(this.$scopedSlots)].filter((slot) => {
                    return !ownSlots.includes(slot);
                });
            },
            pageInfo() {
                if(this.connection && this.connection.edges) {
                    return this.connection.pageInfo;
                } else {
                    return {
                        hasNextPage: false,
                        endCursor: null,
                    }
                }
            },
            edges() {
                if(this.connection && this.connection.edges) {
                    return this.connection.edges;
                } else {
                    return [];
                }
            },
            hasMore() {
                return this.pageInfo.hasNextPage;
            },
            options() {
                return this.edges.map(edge => edge.node);
            },
        },
        methods: {
            async showMore() {
                // Do nothing while there are more items.
                if(!this.hasMore) {
                    return null;
                }

                // Do not load more when already loading.
                if(this.isLoading) {
                    return null;
                }

                // Get the FetchMore variables.
                const after = this.pageInfo.endCursor;

                // Call FetchMore on the query.
                return this.query.fetchMore({
                    variables: { ...this.query.variables, after},
                    updateQuery: (prev, {fetchMoreResult}) => {
                        const prevConn = prev[this.connectionField];
                        const nextConn = fetchMoreResult[this.connectionField];

                        return {
                            ...prev,
                            [this.connectionField]: {
                                ...prevConn,
                                edges: [...prevConn.edges, ...nextConn.edges],
                                pageInfo: {
                                    ...nextConn.pageInfo,
                                    hasPreviousPage: prevConn.pageInfo.hasPreviousPage,
                                    startCursor: prevConn.pageInfo.startCursor,
                                    count: prevConn.pageInfo.count + nextConn.pageInfo.count,
                                },
                            }
                        }
                    }
                })
            },
            async onOpen() {
                if(this.hasMore) {
                    await this.$nextTick();
                    this.observer.observe(this.$refs.loadItem);
                }
            },
            onClose() {
                this.observer.disconnect();
            },
            async infiniteScroll([{isIntersecting, target}]) {
                if (isIntersecting) {
                    const ul = target.offsetParent;
                    const scrollTop = target.offsetParent.scrollTop;
                    await this.showMore();
                    await this.$nextTick();
                    ul.scrollTop = scrollTop;
                }
            },
        },
        mounted() {
            console.log({slots: this.$slots, scopedSlots: this.$scopedSlots});
            this.observer = new IntersectionObserver(this.infiniteScroll);
        }
    }
</script>

<style scoped>

</style>
