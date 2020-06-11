<template>
	<main class="container" id="app">
		<div class="top-bar">
			<div class="top-bar-left">
				<div id="nav">
					<ul class="dropdown menu" data-dropdown-menu>
						<li>
							<router-link to="/">Home</router-link>
						</li>
						<li>
							<router-link to="/saved">Saved Projects</router-link>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<router-view/>
	</main>
</template>

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
		background-color: #3F88C5;
	}

	#nav a {
		font-weight: bold;
		color: #ffffff;
	}

	#nav a.router-link-exact-active {
		color: #FFBA08;
		pointer-events: none;
	}
</style>
