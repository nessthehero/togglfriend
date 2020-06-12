/* Create a new project on Firebase and paste the credentials object here */

let config = {
	firebase: {
		apiKey: 'EXAMPLE',
		authDomain: 'EXAMPLE',
		databaseURL: 'EXAMPLE',
		projectId: 'EXAMPLE',
		storageBucket: 'EXAMPLE',
		messagingSenderId: 'EXAMPLE',
		appId: 'EXAMPLE'
	},
	fireDatabase: 'bookmarks',
	endpointBase: 'http://localhost'
};

config.endpoints = {
	dataClientProjects: config.endpointBase + '/php/GetClientProjects.php',
	dataClients: config.endpointBase + '/php/GetClients.php',
	dataJobs: config.endpointBase + '/data/RetrieveAvailableJobs.json',
	dataStartTimerProject: config.endpointBase + '/php/StartTimerProject.php',
	endpointCreateClient: config.endpointBase + '/php/CreateClient.php',
	endpointCreateProject: config.endpointBase + '/php/CreateProject.php',
	endpointRunningTimeEntry: config.endpointBase + '/php/RunningTimeEntry.php',
};

export default config;
