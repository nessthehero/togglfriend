<template>
	<main class="container" id="app">
		<div class="top-bar">
			<div class="top-bar-left">
				<div id="nav">
					<ul class="dropdown menu" data-dropdown-menu>
						<li v-if="user.loggedIn">
							<router-link to="/">Home</router-link>
						</li>
						<li v-if="user.loggedIn">
							<router-link to="/saved">Saved Projects</router-link>
						</li>
						<li>
							<a @click="login" v-if="!user.loggedIn">Log in</a>
							<a @click="logout" v-else>Log out</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div v-if="user.loggedIn">
			<router-view/>
		</div>
		<div v-else>
			<div class="row column">
				<h1>Please log in to continue.</h1>
			</div>
		</div>
	</main>
</template>

<script>
	import {EventBus} from '@/EventBus.js';
	import firebase from './Firebase.js';
	import axios from 'axios';
	import config from './togglfriend.config.js';

	export default {
		name: 'App',
		data: function () {
			return {
				user: {
					loggedIn: false,
					id: '',
					data: {}
				}
			};
		},
		methods: {
			login() {
				const provider = new firebase.auth.GoogleAuthProvider();

				firebase.auth().signInWithPopup(provider).then((result) => {
					console.log(result);
				}).catch((err) => {
					console.error('Oh no! ', err.message);
				});
			},
			logout() {
				firebase.auth().signOut().then((res) => {
					this.user.loggedIn = false;
					this.user.data = {};
				});
			}
		},
		mounted: function () {
			firebase.auth().setPersistence(firebase.auth.Auth.Persistence.SESSION);
			firebase.auth().onAuthStateChanged(user => {
				if (user) {
					this.user.loggedIn = true;
					this.user.id = user.uid;
					this.user.data = user;
				} else {
					this.user.loggedIn = false;
					this.user.id = '';
					this.user.data = {};
				}
			});
		}
	};

</script>

<style lang="scss">
	@import "./_settings";
	@import "./scss/main";

	#app {
		/*font-family: Avenir, Helvetica, Arial, sans-serif;*/
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
		/*text-align: center;*/
		/*color: #2c3e50;*/
	}

	#nav {
		/*padding: 30px;*/
		background-color: #3f88c5;
	}

	#nav a,
	#nav button {
		font-weight: bold;
		color: #ffffff;
	}

	#nav a.router-link-exact-active {
		color: #ffba08;
		pointer-events: none;
	}
</style>
