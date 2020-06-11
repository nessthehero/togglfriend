<template>
	<div :class="jobClasses">
		<div class="job__inner">
			<div class="job__label">
				<span class="job__code">{{ job.codecomp }}</span>
				<span class="job__client">{{ job.client }}</span>
			</div>
			<div class="job__description">{{ job.desc }}</div>
			<div class="job__component">({{ job.component }})</div>
		</div>
		<button :class="jobActionClasses" @click="doAction()">{{ job.actionDisplay }}</button>
	</div>
</template>

<script>
	export default {
		name: 'Job',
		props: ['job', 'action'],
		methods: {
			doAction: function () {
				this.$emit('job-action');
			}
		},
		computed: {
			actionDisplay: function () {
				let char = this.action;
				switch (this.action) {
					case 'add':
						char = '<<';
						break;
					case 'remove':
						char = '>>';
						break;
					default:
						break;
				}
				return char;
			}
		},
		data: function () {
			return {
				jobClasses: [
					'job',
					'job--' + this.action
				],
				jobActionClasses: [
					'job__action',
					'job__action--' + this.action
				]
			};
		}
	};
</script>

<style lang="scss" scoped>
	@import "./src/_settings";
	@import "./src/scss/_theme";

	.job {
		$this: &;
		border-color: $medium-gray;
		border-style: solid;
		margin-bottom: 1rem;
		padding: 8px;
		position: relative;

		&--add {
			border-width: 2px 2px 2px 0;
		}

		&--remove {
			border-width: 2px 0 2px 2px;
		}

		&__label {
			font-size: 15px;
			font-weight: 700;
		}

		&__inner {
			#{$this}--add & {
				padding-left: 20px;
			}

			#{$this}--remove & {
				padding-right: 20px;
			}
		}

		&__code {
			padding-right: 4px;
		}

		&__description {
			font-size: 15px;
			line-height: 1;
			margin-bottom: 0.5rem;
		}

		&__component {
			font-size: 12px;
		}

		&__action {
			cursor: pointer;
			font-size: 0;
			height: 100%;
			position: absolute;
			top: 0;
			width: 20px;
			overflow: hidden;

			&:before {
				display: block;
				font-size: 40px;
				position: absolute;
				top: calc(50% - 16px);
				width: 20px;
			}
		}

		&__action--add {
			background-color: $dodgerblue;
			color: $white;
			left: 0;

			&:before {
				color: $white;
				content: '\2329';
				left: -18px;
			}
		}

		&__action--remove {
			background-color: $roman;
			color: $black;
			right: 0;

			&:before {
				color: $white;
				content: '\232A';
				right: -3px;
			}
		}
	}
</style>
