import { RutrackerApi } from '../../../../../public/lib/rutracker/index.js';

const rutracker = new RutrackerApi();
rutracker.login({ username: '', password: '' })
    .then(() => {
        console.log('Authorized');
    })
    .catch(err => console.error(err));
