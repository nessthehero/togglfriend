var steps = [];
var testindex = 0;
var loadInProgress = false;//This is set to true when a page is still loading
var json = '';

/*********SETTINGS*********************/
var webPage = require('webpage');
var system = require('system');
var page = webPage.create();
page.settings.userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36';
page.settings.javascriptEnabled = true;
page.settings.loadImages = false;//Script is much faster with this field set to false
phantom.cookiesEnabled = true;
phantom.javascriptEnabled = true;
/*********SETTINGS END*****************/

var creds = {
	username: '',
	password: '',
	captain: '',
	exportdir: ''
};

if (typeof system.args != 'undefined') {

	creds.username = system.args[1];
	creds.password = system.args[2];
	creds.captain = system.args[3];
	creds.exportdir = system.args[4];

}

console.log('All settings loaded, start with execution');
page.onConsoleMessage = function (msg) {
	console.log(msg);
};
/**********DEFINE STEPS THAT FANTOM SHOULD DO***********************/
steps = [

	//Step 1 - Open Amazon home page
	function (creds) {
		console.log('Step 1 - Open Captains Log');

		page.open(creds.captain, function (status) {

		});
	},
	//Step 2 - Populate and submit the login form
	function (creds) {
		console.log('Step 2 - Populate and submit the login form');

		page.evaluate(function (creds) {
			console.log(creds.username);
			console.log(creds.password);

			document.getElementById('Email').value = creds.username;
			document.getElementById('Password').value = creds.password;
			document.getElementById('loginForm').children[0].submit();
		}, creds);
	},
	function (creds) {
		console.log('Step 3 - Load JSON');
		page.open('https://captainslog.barkleyus.com/Timekeeping/RetrieveAvailableJobs', function (status) {
			var jsonSource = page.plainText;
			json = jsonSource;
		});
	},
	function (creds) {

		console.log('Step 4 - Write JSON');

		var regex = /Result/;
		console.log(regex.test(json));

		var test = regex.test(json);
		// console.log(json.includes('{"Result"'));

		// console.log(valid);

		if (test) {
			var fs = require('fs');
			fs.write(creds.exportdir, json, 'w');
		} else {
			console.error('JSON was not requested correctly. Check your credentials.');
		}

	}
];
/**********END STEPS THAT FANTOM SHOULD DO***********************/

//Execute steps one by one
interval = setInterval(executeRequestsStepByStep.bind(null, creds), 50);

console.log('before', creds);

function executeRequestsStepByStep(creds) {
	if (loadInProgress == false && typeof steps[testindex] == 'function') {
		steps[testindex](creds);
		testindex++;
	}
	if (typeof steps[testindex] != 'function') {
		console.log('task complete!');
		phantom.exit();
	}
}

/**
 * These listeners are very important in order to phantom work properly. Using these listeners, we control loadInProgress marker which controls, weather a page is fully loaded.
 * Without this, we will get content of the page, even a page is not fully loaded.
 */
page.onLoadStarted = function () {
	loadInProgress = true;
	console.log('Loading started');
};
page.onLoadFinished = function () {
	loadInProgress = false;
	console.log('Loading finished');
};
page.onConsoleMessage = function (msg) {
	console.log(msg);
};

