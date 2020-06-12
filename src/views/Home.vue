<template>
	<div class="page">
		<div class="row">
			<div class="columns medium-4">
				<client-projects></client-projects>
			</div>
			<div class="columns medium-4">

				<div v-if="creatingProjects" class="callout primary">
					<p>Saving projects... {{ projectQueue.length }} remaining</p>
				</div>

				<button @click="saveProjects" class="button expanded" :disabled="selectedJobs && selectedJobs.length == 0">Save Jobs</button>

				<ul class="job-list no-style">
					<li v-for="(item, index) in jobs" v-show="item.checked">
						<job :key="item.codecomp"
							 :job="item"
							 action="remove"
							 @job-action="toggleJob(item.codecomp)"
						></job>
					</li>
				</ul>

			</div>
			<div class="columns medium-4">

				<input type="text" v-model="jobQuery" name="jobQuery" placeholder="Search for a job by code/client"/>

				<ul class="job-list no-style" v-if="jobFilter.length > 0">
					<li v-for="(item, index) in jobFilter" v-show="!item.checked">
						<job :key="item.codecomp"
							 :job="item"
							 action="add"
							 @job-action="toggleJob(item.codecomp)"
						></job>
					</li>
				</ul>
				<div v-else :class="{ callout: true, alert: loadError, secondary: !loadError }">
					<p v-if="loadError">Unable to load any jobs</p>
					<p v-else>Loading...</p>
				</div>

			</div>
		</div>
		<timer></timer>
	</div>
</template>

<script>
	// @ is an alias to /src
	import {EventBus} from '@/EventBus.js';
	import axios from 'axios';
	import { config } from '../togglfriend.config.js';

	import Job from '@/components/Job.vue';
	import ClientProjects from '@/components/ClientProjects';
	import Timer from '@/components/Timer';

	const dataJobs = config.endpoints.dataJobs;
	const endpointCreateProject = config.endpoints.endpointCreateProject;

	export default {
		name: 'Home',
		components: {
			'job': Job,
			'client-projects': ClientProjects,
			'timer': Timer
		},
		data: function () {
			return {
				jobQuery: '',
				jobs: [],
				projectQueue: [],
				projectNames: [],
				creatingProjects: false,
				loadError: false
			};
		},
		created: function () {
			this.populateJobs();

			EventBus.$on('projectsUpdated', this.updateClientProjects);

			EventBus.$on('selectClient', this.selectClient);

			setInterval(function () {

				if (this.projectQueue.length > 0) {

					let item = this.projectQueue.shift();

					let name = encodeURIComponent(item.name);

					let ep = endpointCreateProject + '?client=' + item.client + '&name=' + name;

					axios.get(ep).then(function (response) {

						console.log('project creation response', response);

					}).catch(function (error) {
						console.error(error);
					});

				} else {

					if (this.creatingProjects) {

						this.creatingProjects = false;

						EventBus.$emit('loadProjects', true);

					}

				}

			}.bind(this), 1500);
		},
		computed: {

			jobFilter: function () {
				let filtered = this.findBy(this.jobs, this.jobQuery.toLowerCase(), 'searchKey');

				filtered = filtered.filter(function (job) {
					if (this.projectNames.indexOf(job.projectEntry) !== -1) {
						return 0;
					} else {
						return 1;
					}
				}.bind(this));

				return filtered;
			},

			selectedJobs: function () {
				let filtered = this.findByBool(this.jobs, true, 'checked');
				return filtered;
			}
		},
		methods: {
			selectClient(client) {
				this.selectedClient = client;
			},
			updateClientProjects(projectNames) {
				this.projectNames = projectNames;
			},
			toggleJob(id) {
				let indice = this.jobs.find(function (item) {
					return item.codecomp === id;
				});

				indice.checked = !indice.checked;
			},
			populateJobs() {

				axios.get(dataJobs).then(function (response) {

					let results = response.data.Result;

					for (let i in results) {
						if (results.hasOwnProperty(i)) {
							this.jobs.push({
								id: results[i].JOB_NUMBER,
								checked: false,
								client: results[i].CL_NAME,
								code: results[i].CL_CODE.toUpperCase(),
								desc: results[i].JOB_DESC,
								component: results[i].JOB_COMP_DESC,
								cnum: results[i].JOB_COMPONENT_NBR,
								codecomp: results[i].JOB_NUMBER + '-' + results[i].JOB_COMPONENT_NBR,
								searchKey: results[i].JOB_NUMBER + '-' + results[i].JOB_COMPONENT_NBR + '__' + results[i].JOB_COMP_DESC.toLowerCase() + '__' + results[i].CL_NAME.toLowerCase() + '__' + results[i].CL_CODE.toLowerCase(),
								projectEntry: results[i].CL_CODE.toUpperCase() + ' ' + results[i].JOB_NUMBER + ' - ' + results[i].JOB_COMP_DESC + ' (' + results[i].JOB_DESC + ')'
							});
						}
					}

				}.bind(this)).catch(function (response) {
					console.error(response);
				});

			},

			saveProjects(event) {

				event.preventDefault();

				let jobs = this.findByBool(this.jobs, true, 'checked');

				this.creatingProjects = true;

				for (let i in jobs) {

					if (jobs.hasOwnProperty(i)) {

						jobs[i].checked = false;

						this.projectQueue.push({
							'client': this.selectedClient,
							'name': jobs[i].projectEntry
						});

					}

				}

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

<style lang="scss">
	.page {
		margin: 1rem 0;
	}
</style>
