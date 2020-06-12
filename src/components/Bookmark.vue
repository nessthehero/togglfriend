<template>
	<div :class="bookmarkClasses">
		<div class="bookmark__inner">
			<span class="bookmark__client">{{ bookmark.client_name }}</span>
			<span class="bookmark__desc">{{ bookmark.desc }} {{ bookmark.key }}</span>
			<span class="bookmark__project">{{ bookmark.project_name }}</span>
		</div>
		<button v-if="!startFlag" class="bookmark__action bookmark__play" @click="runBookmark(id)"><span>&#9658;</span></button>
		<button v-if="!startFlag" class="bookmark__action bookmark__del" @click="deleteBookmark(id, bookmark.desc)"><span>&#10799;</span></button>
	</div>
</template>

<script>
	import {EventBus} from '@/EventBus.js';
	import firebase from '../Firebase.js';
	import axios from 'axios';
	import { config } from '../togglfriend.config.js';

	const dataStartTimerProject = config.endpoints.dataStartTimerProject;

	export default {
		name: 'Bookmark',
		props: ['id', 'bookmark'],
		data: function () {
			return {
				bookmarkClasses: {
					'bookmark': true,
					'bookmark--disabled': this.startFlag
				},
				bookmarks: firebase.firestore().collection('bookmarks'),
				startFlag: false
			};
		},
		created: function () {

			EventBus.$on('start-new', function () {
				this.startFlag = true;
			}.bind(this));

			EventBus.$on('timer-ready', function () {
				this.startFlag = false;
			}.bind(this));

		},
		computed: {},
		methods: {
			runBookmark: function (id) {
				let doc = this.bookmarks.doc(id);

				doc.get().then(function (dd) {
					if (dd.exists) {
						let data = dd.data();

						let desc = encodeURIComponent(data.desc);
						let pid = data.project_id;

						let dataurl = dataStartTimerProject + '?pid=' + pid + '&description=' + desc;

						axios.get(dataurl).then(function (response) {
							console.log(response);
						});

						EventBus.$emit('start-new');
					} else {
						console.error('no document', id);
					}
				}.bind(this)).catch(function (error) {
					console.error('Error retrieving document: ', error);
				});
			},
			deleteBookmark: function (id, desc) {
				if (confirm('Delete "' + desc + '"?')) {
					let doc = this.bookmarks.doc(id);

					doc.delete().then(function (dd) {
						console.info('Document deleted', id);
					}).catch(function (error) {
						console.error('Error removing document: ', error);
					}).finally(function () {
						EventBus.$emit('bookmark-add');
					}.bind(this));
				}
			}
		}
	};
</script>

<style lang="scss" scoped>
	@import "./src/_settings";
	@import "./src/scss/_theme";

	.bookmark {
		$this: &;
		border-color: $medium-gray;
		border-style: solid;
		margin-bottom: 4px;
		padding: 4px 48px 4px 8px;
		position: relative;
		margin-left: 12px;

		@include breakpoint(medium) {
			margin-bottom: 1rem;
		}

		&--disabled {
			pointer-events: none;
			background-color: #e1e1e1;
		}

		&--add {
			border-width: 2px 2px 2px 0;
		}

		&--remove {
			border-width: 2px 0 2px 2px;
		}

		&__client,
		&__desc,
		&__project {
			display: block;
		}

		&__client {
			font-size: 14px;
			font-weight: 700;
		}

		&__desc {
			font-size: 12px;
		}

		&__project {
			font-size: 12px;
			font-style: italic;
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
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			cursor: pointer;
			overflow: hidden;

			span {
				line-height: 0;
			}
		}

		&__play {
			font-size: 24px;
			line-height: 1;
			padding: 4px 0 0;
			position: absolute;
			color: white;
			background-color: #136F63;
			bottom: 0;
			right: 0;
			width: 40px;
			height: 100%;
		}

		&__del {
			font-size: 28px;
			line-height: 1;
			color: white;
			background-color: #69140E;
			padding: 0 4px 8px;
			position: absolute;
			bottom: 0;
			left: -23px;
			width: 20px;
			height: 20px;
		}
	}
</style>
