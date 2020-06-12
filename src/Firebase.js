import * as firebase from 'firebase';
import config from './togglfriend.config.js';

const settings = {};

firebase.initializeApp(config.firebase);

firebase.firestore().settings(settings);

export default firebase;
