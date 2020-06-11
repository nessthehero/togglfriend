<template>
	<div id="create-bookmark">
		<form role="form">
			<fieldset>
				<legend>
					Create a new bookmark
				</legend>
				<select name="clientid" v-model="selectedClient" @change="selectClient">
					<option value="">
						-- Choose a client --
					</option>
					<option v-for="(item, index) in clients" :value="item.id">
						{{ item.name }}
					</option>
				</select>
				<select name="projectname" v-model="selectedProject" v-if="projectNames.length > 0">
					<option value="">
						-- Choose a project --
					</option>
					<option v-for="(item, index) in projects" :value="item.id">
						{{ item.name }}
					</option>
				</select>
				<textarea v-model="newDescription" v-if="selectedProject != ''"></textarea>
				<button class="button" @click="createBookmark" :disabled="bookmarkIsValid">Create Bookmark</button>
			</fieldset>
		</form>
	</div>
</template>

<script>
	import {EventBus} from '../EventBus.js';
	import axios from 'axios';
	import firebase from '../Firebase.js';

	const dataClients = 'http://local.tog/php/GetClients.php';
	const dataClientProjects = 'http://local.tog/php/GetClientProjects.php';

	export default {
		name: 'CreateBookmark',
		data: function () {
			return {
				selectedClient: '',
				selectedProject: '',
				newDescription: '',
				projectNames: [],
				clients: [],
				projects: [],
				bookmarks: firebase.firestore().collection('bookmarks')
			};
		},
		created: function () {

			axios.get(dataClients).then(function (response) {
				this.clients = response.data;
			}.bind(this));

		},
		computed: {

			bookmarkIsValid() {
				return !(this.selectedClient !== '' && this.selectedProject !== '' && this.newDescription !== '');
			}

		},
		methods: {

			getClient(id) {
				return this.clients.filter(x => x.id === id);
			},

			getProject(id) {
				return this.projects.filter(x => x.id === id);
			},

			createBookmark(e) {
				e.preventDefault();

				let _client = this.getClient(this.selectedClient);
				let _project = this.getProject(this.selectedProject);

				console.log(_client);

				this.bookmarks
					.add({
						'client_id': _client[0].id,
						'client_name': _client[0].name,
						'project_id': _project[0].id,
						'project_name': _project[0].name,
						'desc': this.newDescription
					})
					.then(() => {
						console.log('Document successfully written!');

						this.selectedClient = '';
						this.selectedProject = '';
						this.newDescription = '';

						this.clearLocalProjects();

						EventBus.$emit('bookmark-add');
					})
					.catch((error) => {
						console.error('Error writing document: ', error);
					});
			},

			selectClient() {
				this.loadClientProjects(false);
			},

			loadClientProjects(cached) {
				this.clearLocalProjects();

				let dataurl = dataClientProjects + '?client=' + this.selectedClient;

				if (cached) {
					dataurl += '&cb=' + Math.random().toString(36).substr(2, 5);
				}

				axios.get(dataurl).then(function (response) {
					this.projects = response.data;

					for (let i in this.projects) {
						if (this.projects.hasOwnProperty(i)) {
							this.projectNames.push(this.projects[i].name);
						}
					}
				}.bind(this));
			},

			clearLocalProjects() {
				this.projects = [];
				this.projectNames = [];
			},

			findBy: function (list, value, column) {
				return list.filter(function (item) {
					return item[column].includes(value);
				});
			},

			findByBool: function (list, value, column) {
				return list.filter(function (item) {
					return item[column] === value;
				});
			}
		}
	};
</script>

<style scoped>

</style>
