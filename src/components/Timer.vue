<template>
	<div class="the-timer">
		<div class="row">
			<div class="columns medium-6 the-timer__column">
				<span class="the-timer__timer">{{ timer }}</span>
			</div>
			<div class="columns medium-6 the-timer__column">
				<p class="the-timer__task">{{ activeTask }}</p>
				<p class="the-timer__project">{{ activeProject }}</p>
				<p class="the-timer__client">{{ activeClient }}</p>
			</div>
		</div>
	</div>
</template>

<script>
	import {EventBus} from '@/EventBus.js';
	import axios from 'axios';
	import config from '../togglfriend.config.js';

	const endpointRunningTimeEntry = config.endpoints.endpointRunningTimeEntry;

	export default {
		name: 'Timer',
		data: function () {
			return {
				timer: '',
				jobstart: 0,
				activeClient: '',
				activeProject: '',
				activeTask: ''
			};
		},
		created() {
			this.$options.interval = setInterval(this.updateTimer, 1000);

			this.getRunningTimeEntry();

			EventBus.$on('start-new', this.getRunningTimeEntry);
		},
		computed: {},
		methods: {
			updateTimer() {

				let now = new Date();

				let seconds = now.getTime();

				if (this.jobstart > 0) {

					this.timer = this.sec2time((seconds - this.jobstart) / 1000);

				}

			},

			sec2time(timeInSeconds) {
				let pad = function (num, size) {
						return ('000' + num).slice(size * -1);
					},
					time = parseFloat(timeInSeconds).toFixed(3),
					hours = Math.floor(time / 60 / 60),
					minutes = Math.floor(time / 60) % 60,
					seconds = Math.floor(time - minutes * 60);

				return pad(hours, 2) + ':' + pad(minutes, 2) + ':' + pad(seconds, 2);
			},

			getRunningTimeEntry() {

				let ep = endpointRunningTimeEntry;

				axios.get(ep).then(function (response) {

					if (typeof response.data.data.clean !== 'undefined') {

						if (typeof response.data.data.clean.start !== 'undefined') {

							let start = Date.parse(response.data.data.clean.start);
							let startDate = new Date(start);

							this.jobstart = startDate.getTime();

							this.activeClient = response.data.data.clean.client;
							this.activeProject = response.data.data.clean.project;
							this.activeTask = response.data.data.clean.task;

							this.updateTimer();

							EventBus.$emit('timer-ready');

						} else {
							this.resetActiveJob();
						}

					} else {
						this.resetActiveJob();
					}

					setTimeout(this.getRunningTimeEntry, 10000);

				}.bind(this));

			},

			resetActiveJob() {

				this.jobstart = 0;
				this.activeClient = '';
				this.activeProject = '';
				this.activeTask = '';

			}
		}
	};
</script>

<style scoped lang="scss">
	@import "./src/_settings";
	@import "./src/scss/_theme";

	.the-timer {
		$this: &;
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100vw;
		background: #032b43;
		color: white;
		padding: 8px 0;

		&__column {
			display: flex;
			flex-direction: column;
			height: 100%;
			justify-content: center;
		}

		&__timer {
			display: inline-block;
			flex: 1 0 auto;
			font-family: $press-start;
			font-size: 30px;
			height: 100%;
			justify-self: center;
			text-align: center;
		}

		p {
			margin: 0;
		}

		&__task {
			font-size: 22px;
			font-weight: 600;
		}

		&__project {
			font-style: italic;
		}

		&__client {
			font-style: italic;
		}
	}
</style>
