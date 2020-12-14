<template>
    <div>
        <JetActionSection>
            <template #title>
                Personal Access Tokens
            </template>

            <template #description>
                Tokens waarmee je snel de API kan benaderen, zonder lastige OAuth2 protocollen te volgen. Dit is vooral
                handig als je snel iets wilt testen.
            </template>

            <template #content>

                <div v-if="personalAccessTokensLoading" class="text-gray-400">Bezig met laden van je Personal Access Tokens...</div>
                <div v-else class="space-y-6">
                    <template v-if="personalAccessTokens.length > 0">
                        <div class="flex items-center justify-between" v-for="token in personalAccessTokens" :key="token.id">
                            <div>{{ token.name }}</div>

                            <div class="flex items-center">
                                <div class="text-sm text-gray-400" v-if="token.created_at">
                                    Aangemaakt {{ fromNow(token.created_at) }}
                                </div>

                                <button class="cursor-pointer ml-6 text-sm text-gray-400 underline focus:outline-none"
                                        @click="prepareDeletingToken(token)">
                                    Verwijderen
                                </button>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex items-center text-gray-400">Je hebt op dit moment geen Personal Access Tokens...</div>
                    </template>

                    <div class="flex items-center justify-between">
                        <div>&nbsp;</div>

                        <div class="flex items-center">
                            <jet-button class="cursor-pointer ml-6"
                                    @click.native="openCreateTokenDialog">
                                Nieuw
                            </jet-button>
                        </div>
                    </div>
                </div>
            </template>
        </JetActionSection>
        <!-- Dialog Modals -->

        <!-- Create token dialog -->
        <jet-dialog-modal :show="createTokenDialog" @close="dismissCreateTokenDialog">
            <template #title>
                Nieuwe Personal Access Token
            </template>

            <template #content>
                <template v-if="newAccessToken">
                    <div>De access token <strong>{{newPersonalAccessTokenName}}</strong> heeft als waarde:</div>
                    <div class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500" style="word-break: break-all;" v-if="newAccessToken">
                        {{ newAccessToken }}
                    </div>
                    <div>We laten je deze token maar &eacute;&eacute;n keer zien! Dus kopi&euml;er hem snel, voor je hem vergeet!</div>
                </template>
                <template v-else>
                    <div>
                        Kies een naam en scopes voor je nieuwe Personal Access Token.
                    </div>

                    <div class="col-span-12 sm:col-span-12">
                        <jet-label for="name" value="Naam" />
                        <jet-input id="name"
                                   type="text"
                                   class="mt-1 block w-full"
                                   v-model="newPersonalAccessTokenName"
                                   autofocus
                                   :disabled="busy"
                        />
                        <jet-input-error :message="newPersonalAccessTokenError" class="mt-2" />
                    </div>

                    <div class="col-span-12 sm:col-span-12 mt-2">
                        <span class="block font-medium text-sm text-gray-700">Scopes</span>
                        <div v-for="scope in scopes" :key="scope.id">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       class="form-checkbox"
                                       :value="scope.id"
                                       v-model="newPersonalAccessTokenScopes"
                                />
                                <span class="ml-2 text-sm text-gray-600">{{ scope.description }}</span>
                            </label>
                        </div>
                    </div>

                    <pre>
                        New name: {{newPersonalAccessTokenName}}
                        Scopes: {{newPersonalAccessTokenScopes}}
                    </pre>

                </template>
            </template>

            <template #footer>
                <template v-if="newAccessToken">
                    <jet-button @click.native="dismissCreateTokenDialog">
                        OK
                    </jet-button>
                </template>
                <template v-else>
                    <jet-button @click.native="createPersonalAccessToken"
                                :class="{'opacity-25': busy}"
                                :disabled="busy">
                        Aanmaken
                    </jet-button>
                </template>
            </template>
        </jet-dialog-modal>

        <!-- Delete token confirmation -->
        <jet-dialog-modal :show="tokenToDelete" @close="tokenToDelete = null">
            <template #title>
                Personal Access Token Verwijderen
            </template>

            <template #content>
                Weet je zeker dat je de personal access token <strong v-if="tokenToDelete">{{tokenToDelete.name}}</strong> wilt verwijderen?
            </template>

            <template #footer>
                <jet-secondary-button v-if="!personalAccessTokenDeleting"
                                      @click.native="tokenToDelete = null"
                                      :disabled="personalAccessTokenDeleting">
                    Annuleren
                </jet-secondary-button>
                <jet-danger-button @click.native="deletePersonalAccessToken(tokenToDelete.id)"
                                   :class="{'opacity-25': personalAccessTokenDeleting}"
                                   :disabled="personalAccessTokenDeleting"
                >
                    Verwijderen
                </jet-danger-button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script>
    import FormSection from "../../Jetstream/FormSection";
    import SectionBorder from "../../Jetstream/SectionBorder";
    import JetActionSection from '../../Jetstream/ActionSection';
    import JetLabel from "../../Jetstream/Label";
    import JetInput from "../../Jetstream/Input";
    import JetButton from "../../Jetstream/Button";
    import JetDangerButton from "../../Jetstream/Button";
    import JetSecondaryButton from "../../Jetstream/Button";
    import JetInputError from "../../Jetstream/InputError";
    import JetDialogModal from "../../Jetstream/DialogModal";
    import moment from 'moment';
    import Label from "@/Jetstream/Label";
    import Input from "@/Jetstream/Input";
    export default {
        name: "PersonalAccessTokenSection",
        components: {
            Input,
            Label,
            JetLabel,
            FormSection,
            SectionBorder,
            JetInput,
            JetInputError,
            JetButton,
            JetActionSection,
            JetDialogModal,
            JetDangerButton,
            JetSecondaryButton,
        },

        data() {
            return {
                scopesLoading: false,
                scopes: [],
                personalAccessTokensLoading: false,
                createTokenDialog: false,
                personalAccessTokens: [],
                tokenToDelete: null,
                personalAccessTokenCreating: false,
                personalAccessTokenDeleting: false,
                newAccessToken: null,
                newPersonalAccessTokenName: null,
                newPersonalAccessTokenScopes: [],
                newPersonalAccessTokenError: null,
            }
        },

        mounted() {
            this.loadScopes();
            this.loadPersonalAccessTokens();
        },

        computed: {
            busy() {
                return !!this.tokenToDelete
                    || this.personalAccessTokenCreating
                    || this.personalAccessTokenDeleting
                    || this.personalAccessTokensLoading;
            }
        },

        methods: {
            async loadScopes() {
                this.scopesLoading = true;
                try {
                    const response = await axios.get('/oauth/scopes');
                    this.scopes = response.data;
                } finally {
                    this.scopesLoading = false;
                }
            },
            async loadPersonalAccessTokens() {
                this.personalAccessTokensLoading = true;
                try {
                    const response = await axios.get('/oauth/personal-access-tokens');
                    this.personalAccessTokens = response.data;
                } finally {
                    this.personalAccessTokensLoading = false;
                }
            },
            async createPersonalAccessToken() {
                console.log('Creating new personal access token');
                this.personalAccessTokenCreating = true;
                try {
                    const response = await axios.post('/oauth/personal-access-tokens', {
                        name: this.newPersonalAccessTokenName,
                        scopes: this.newPersonalAccessTokenScopes || [],
                    });
                    console.log(response);
                    this.newAccessToken = response.data.accessToken;
                    this.personalAccessTokens.push(response.data.token);
                } finally {
                    this.personalAccessTokenCreating = false;
                }
            },
            prepareDeletingToken(token) {
                if(!this.busy) {
                    this.tokenToDelete = token;
                }
            },
            openCreateTokenDialog() {
                console.log('openen');
                if(!this.busy) {
                    this.createTokenDialog = true;
                    this.newPersonalAccessTokenName = null;
                    this.newPersonalAccessTokenScopes = [];
                }
            },
            dismissCreateTokenDialog() {
                this.createTokenDialog = false;
                this.newAccessToken = null;
                this.newPersonalAccessTokenName = null;
                this.newPersonalAccessTokenScopes = [];
            },
            async deletePersonalAccessToken() {
                if(!this.tokenToDelete || this.personalAccessTokenDeleting) {
                    return
                }
                this.personalAccessTokenDeleting = true;
                const tokenId = this.tokenToDelete.id;
                try {
                    const response = await axios.delete('/oauth/personal-access-tokens/' + tokenId);
                    if(response.status === 204) {
                        const index = this.personalAccessTokens.findIndex((token) => token.id === tokenId);
                        this.personalAccessTokens.splice(index, 1);
                    }
                } finally {
                    this.personalAccessTokenDeleting = false;
                    this.tokenToDelete = null;
                }
            },

            fromNow(timestamp) {
                return moment(timestamp).local().fromNow()
            },
        }
    }
</script>

<style scoped>

</style>
