import axios from 'axios'

// this should result in a singleton axios instance, which we need to avoid
// pinia stores using a different axios from the root app
const instance = axios.create({
  baseURL: '/api'
});

export default instance;
