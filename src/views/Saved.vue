<template>
	<div class="page">
		<div class="row">
			<div class="columns medium-5 medium-push-7 bookmarklist">
				<bookmark v-for="(item, index) in bookmarkSort"
						  :key="item.id"
						  :id="item.id"
						  :bookmark="item.data"
				></bookmark>
			</div>
			<div class="columns medium-7 medium-pull-5">
				<create-bookmarks></create-bookmarks>
			</div>
		</div>
		<timer></timer>
	</div>
</template>

<script>
	import {EventBus} from '../EventBus.js';
	import firebase from '../Firebase.js';
	import config from '../togglfriend.config.js';

	import Timer from '../components/Timer';
	import Bookmark from '../components/Bookmark';
	import CreateBookmark from '../components/CreateBookmark';

	const dataStartTimerProject = config.endpoints.dataStartTimerProject;

	export default {
		name: 'Saved',
		components: {
			'timer': Timer,
			'bookmark': Bookmark,
			'create-bookmarks': CreateBookmark
		},
		data: function () {
			return {
				marks: [],
				bookmarkList: [],
				user: {
					loggedIn: false,
					id: '',
					data: {}
				},
				bookmarks: firebase.firestore().collection(config.fireDatabase)
			};
		},
		created: function () {
			EventBus.$on('bookmark-add', this.updateBookmarkList);
		},
		mounted: function () {
			firebase.auth().setPersistence(firebase.auth.Auth.Persistence.SESSION);
			firebase.auth().onAuthStateChanged(user => {
				if (user) {
					this.user.loggedIn = true;
					this.user.id = user.uid;
					this.user.data = user;
					this.updateBookmarkList();
				} else {
					this.user.loggedIn = false;
					this.user.id = '';
					this.user.data = {};
					this.updateBookmarkList();
				}
			});
		},
		computed: {

			bookmarkSort() {
				return this.bookmarkList.sort(function (a, b) {
					if (a.data.client_name < b.data.client_name) return -1;
					if (a.data.client_name > b.data.client_name) return 1;

					if (a.data.project_name < b.data.project_name) return -1;
					if (a.data.project_name > b.data.project_name) return 1;

					return 0;
				});
			}

		},
		methods: {

			updateBookmarkList() {
				this.bookmarkList = [];

				console.log(this.user.id);

				this.bookmarks.where('user', '==', this.user.id).get()
					.then(m => {
						m.forEach(mm => {
							this.bookmarkList.push(
								{
									'id': mm.id,
									'data': mm.data()
								});
						});
					});
			}
		}
	};
</script>

<style lang="scss">
	.page {
		margin: 1rem 0 200px;
	}
</style>
