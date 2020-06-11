var path = require('path');
var childProcess = require('child_process');
var phantomjs = require('phantomjs-prebuilt');
var binPath = phantomjs.path;
var dotenv = require('dotenv').config({
	path: path.join(__dirname, '/../.env')
});

console.log(path.join(__dirname, '/../.env'));

console.log(dotenv.parsed);

var childArgs = [
	path.join(__dirname, 'RetrieveAvailableJobs.js'),
	process.env.EMAIL,
	process.env.PASSWORD,
	process.env.CAPTAIN_URL,
	process.env.EXPORT_PATH
];

console.log(binPath);

childProcess.execFile(binPath, childArgs, function(err, stdout, stderr) {

	console.log(err);

	console.log(stdout);

	console.log(stderr);

});
