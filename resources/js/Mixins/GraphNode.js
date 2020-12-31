import gql from "graphql-tag";

export default {
    props: {
        node: {
            type: [Object, String],
            default: null,
        }
    },
    apollo: {
        node() {

            return {
                query: gql`
                    query ${this.$options.name}($id: ID!) {
                        node(id: $id) {
                            id
                            ...${this.$options.name}
                        }
                    }
                    ${this.fragment}
                `,
                skip() {
                    return typeof this.node !== 'string';
                },
                variables() {
                    return {
                        id: this.nodeId,
                    }
                },
                update(data) {

                }
            }
        }
    },
    computed: {
        nodeId() {
            if(typeof this.node === 'string') {
                return this.node;
            } else if(typeof this.node === 'object' && this.node !== null) {
                return this.node.id;
            }
            return undefined;
        }
    }
}
