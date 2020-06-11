<template>
	<div id="client-projects" v-if="clients.length > 0">
		<form role="form">
			<fieldset>
				<legend class="hide">
					Add client
				</legend>
				<div class="input-group">
					<input name="newclient" type="text" class="input-group-field" v-model="newClient" @disabled="creatingClient"/>
					<div class="input-group-button">
						<button class="button" @click="createClient" :disabled="creatingClient">Create</button>
					</div>
				</div>
			</fieldset>
		</form>

		<form role="form">
			<fieldset>
				<legend class="hide">
					Add jobs as project to Client
				</legend>
				<select name="clientid" v-model="selectedClient" @change="selectClient">
					<option value="">
						-- Choose a client --
					</option>
					<option v-for="(item, index) in clients" :value="item.id">
						{{ item.name }}
					</option>
				</select>
			</fieldset>
		</form>

		<ul class="no-style project-list" v-if="projects && projects.length &gt; 0">
			<li class="project callout small secondary" v-for="(item, index) in projects">
				{{ item.name }}
			</li>
		</ul>

		<div v-else-if="selectedClient == ''" class="callout warning">
			<div v-if="loadingProjects == true">
				<p>Loading...</p>
			</div>
			<div v-else>
				<p>Select a client to see existing projects</p>
			</div>
		</div>
		<div v-else class="callout warning">
			<div v-if="loadingProjects == true">
				<p>Loading...</p>
			</div>
			<div v-else>
				<p>No existing projects for this client</p>
			</div>
		</div>
	</div>
	<div v-else :class="{ callout: true, alert: loadError, secondary: !loadError }">
		<p v-if="loadError">Unable to load any clients</p>
		<p v-else>Loading...</p>
	</div>
</template>

<script>
	import {EventBus} from '@/EventBus.js';
	import axios from 'axios';

	const endpointCreateClient = 'http://local.tog/php/CreateClient.php';
	const dataClients = 'http://local.tog/php/GetClients.php';
	const dataClientProjects = 'http://local.tog/php/GetClientProjects.php';

	export default {
		name: 'ClientProjects',
		data: function () {
			return {
				newClient: '',
				selectedClient: '',
				clients: [],
				creatingClient: false,
				projects: [],
				projectNames: [],
				loadingProjects: false,
				loadError: false
			};
		},
		created() {
			this.populateClients();

			EventBus.$on('loadProjects', this.loadClientProjects);
		},
		methods: {

			populateClients() {

				axios.get(dataClients).then(function (response) {
					this.clients = response.data;
				}.bind(this)).catch(function (error) {
					this.loadError = true;
					console.error(error);
				}.bind(this));

			},

			createClient: function (event) {

				event.preventDefault();

				let ep = endpointCreateClient + '?name=' + escape(this.newClient);

				this.creatingClient = true;

				axios.get(ep).then(function (response) {
					this.creatingClient = false;
					this.newClient = '';
					this.populateClients();
				}.bind(this)).catch(function (error) {
					console.error(error);
				});

			},

			selectClient() {
				this.loadClientProjects(false);
				EventBus.$emit('selectClient', this.selectedClient);
			},

			loadClientProjects(cacheBreaker) {

				this.clearLocalProjects();

				if (this.selectedClient !== '') {

					this.loadingProjects = true;

					let dataurl = dataClientProjects + '?client=' + this.selectedClient;

					if (cacheBreaker) {
						dataurl += '&cb=' + Math.random().toString(36).substr(2, 5);
					}

					axios.get(dataurl).then(function (response) {
						this.projects = response.data;

						for (let i in this.projects) {
							if (this.projects.hasOwnProperty(i)) {
								this.projectNames.push(this.projects[i].name);
							}
						}

						// EventBus.$emit('projectsUpdated', this.projectNames);
					}.bind(this)).finally(function () {
						this.loadingProjects = false;
					}.bind(this));

				}

			},

			clearLocalProjects() {

				this.projects = [];
				this.projectNames = [];

			}
		}
	};
</script>

<style scoped>

</style>
