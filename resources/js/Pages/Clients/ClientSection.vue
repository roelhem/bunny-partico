<template>
    <div>
        <jet-action-section>
            <template #title>
                OAuth2 Clients
            </template>

            <template #description>
                Zie hier al jouw OAuth2 Clients.
            </template>

            <template #content>
                <template v-if="loadingClients">
                    <div class="text-gray-400">Bezig met laden van de clients</div>
                </template>
                <template v-else-if="clients.length > 0">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between" v-for="client in clients" :key="client.id">
                            <div>
                                {{client.name}}
                            </div>

                            <div class="flex items-center">
                                <div class="text-sm text-gray-400" v-if="client.created_at">
                                    Aangemaakt {{ fromNow(client.created_at) }}
                                </div>

                                <button class="cursor-pointer ml-6 text-sm text-gray-400 underline focus:outline-none"
                                        @click="openClientInfo(client)">
                                    Info
                                </button>

                                <button class="cursor-pointer ml-6 text-sm text-gray-400 underline focus:outline-none"
                                        @click="prepareDeleteClient(client)">
                                    Verwijderen
                                </button>
                            </div>

                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="text-gray-400">Je hebt op dit moment geen actieve OAuth2 Clients.</div>
                </template>
            </template>
        </jet-action-section>

        <jet-form-section class="mt-6">
            <template #title>
                Nieuwe Client Toevoegen
            </template>

            <template #description>
                Voeg een nieuwe OAuth2 client toe om je eigen project te laten communiceren met de server.
            </template>

            <template #form>
                <!-- Client Name -->
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="name" value="Naam" />
                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="newClient.name" placeholder="Naam van jouw applicatie" autofocus />
<!--                    <jet-input-error :message="createApiTokenForm.error('name')" class="mt-2" />-->
                </div>

                <!-- Client Redirect URL -->
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="callbackUrl" value="Redirect URL" />
                    <jet-input id="callbackUrl" type="text" class="mt-1 block w-full" placeholder="http://localhost" v-model="newClient.redirect" />
                    <!--                    <jet-input-error :message="createApiTokenForm.error('name')" class="mt-2" />-->
                </div>
            </template>

            <template #actions>
                <jet-button @click.native="createNewClient"
                            :disabled="busy"
                            :class="{'opacity-25': busy}"
                >
                    Toevoegen
                </jet-button>
            </template>
        </jet-form-section>

        <jet-dialog-modal :show="shownClient" @close="shownClient = null">
            <template #title>
                Client Info
            </template>

            <template #content>
                <table class="table-auto">
                    <tbody v-if="shownClient">
                        <tr>
                            <th class="pr-4 text-left">Naam</th>
                            <td>{{shownClient.name}}</td>
                        </tr>
                        <tr>
                            <th class="pr-4 text-left">Redirect URL</th>
                            <td>{{shownClient.redirect}}</td>
                        </tr>
                        <tr>
                            <th class="pr-4 text-left">Client ID</th>
                            <td><code>{{shownClient.id}}</code></td>
                        </tr>
                        <tr>
                            <th class="pr-4 text-left">Client Secret</th>
                            <td><code class="text-gray-200 bg-gray-200">{{shownClient.secret}}</code></td>
                        </tr>
                    </tbody>
                </table>
            </template>

            <template #footer>
                <jet-button @click.native="shownClient = null">
                    OK
                </jet-button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal :show="deletingClient" @close="deletingClient = null">
            <template #title>
                Client Verwijderen
            </template>

            <template #content>
                <div>
                    Weet je zeker dat je de client <strong>{{deletingClient ? deletingClient.name : 'unknown'}}</strong> wilt verwijderen?
                </div>
                <table>
                    <tbody v-if="deletingClient">
                    <tr>
                        <th>Client ID</th>
                        <td><code>{{deletingClient.id}}</code></td>
                    </tr>
                    <tr>
                        <th>Redirect URL</th>
                        <td>{{deletingClient.redirect}}</td>
                    </tr>
                    </tbody>
                </table>
            </template>

            <template #footer>
                <jet-danger-button @click.native="deletingClient = null">
                    Annuleren
                </jet-danger-button>
                <jet-danger-button @click.native="deleteClient">
                    Verwijderen
                </jet-danger-button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script>
    import JetFormSection from "../../Jetstream/FormSection";
    import JetSectionBorder from "../../Jetstream/SectionBorder";
    import JetActionSection from '../../Jetstream/ActionSection';
    import JetLabel from "../../Jetstream/Label";
    import JetInput from "../../Jetstream/Input";
    import JetButton from "../../Jetstream/Button";
    import JetDangerButton from "../../Jetstream/Button";
    import JetSecondaryButton from "../../Jetstream/Button";
    import JetInputError from "../../Jetstream/InputError";
    import JetDialogModal from "../../Jetstream/DialogModal";
    import moment from "moment";

    export default {
        name: "ClientSection",
        components: {
            JetFormSection,
            JetSectionBorder,
            JetActionSection,
            JetLabel,
            JetInputError,
            JetInput,
            JetButton,
            JetDangerButton,
            JetSecondaryButton,
            JetDialogModal,
        },
        data() {
            return {
                clients: [],
                loadingClients: false,
                newClient: {
                    name: null,
                    redirect: null,
                },
                creatingNewClient: false,
                shownClient: null,
                deletingClient: null,
            }
        },
        computed: {
            busy() {
                return this.creatingNewClient;
            }
        },
        methods: {
            async getClients() {
                this.loadingClients = true;
                try {
                    const response = await axios.get('/oauth/clients');
                    this.clients = response.data;
                    console.log(response);
                } finally {
                    this.loadingClients = false;
                }
            },
            async createNewClient() {
                if(this.busy || !this.newClient.name) {
                    return;
                }
                this.creatingNewClient = true;
                try {
                    const response = await axios.post('/oauth/clients', {
                        name: this.newClient.name,
                        redirect: this.newClient.redirect || 'http://localhost',
                    });
                    this.clients.push(response.data);
                    this.newClient.name = null;
                    this.newClient.redirect = null;
                } finally {
                    this.creatingNewClient = false;
                }
            },
            openClientInfo(client) {
                this.shownClient = client
            },
            async deleteClient() {
                const clientId = this.deletingClient.id;
                try {
                    await axios.delete('/oauth/clients/' + clientId);
                    const index = this.clients.findIndex((client) => client.id === clientId);
                    this.clients.splice(index, 1);
                } finally {
                    this.deletingClient = null;
                }
            },
            prepareDeleteClient(client) {
                this.deletingClient = client
            },
            fromNow(timestamp) {
                return moment(timestamp).local().fromNow()
            },
        },
        mounted() {
            this.getClients();
        }
    }
</script>

<style scoped>

</style>
