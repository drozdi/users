import axios from 'axios'

export const api = axios.create({
	baseURL: 'http://localhost:8181/server.php',
	//withCredentials: true,
	headers: {
		'Content-Type': 'application/json',
		//'Access-Control-Allow-Origin': '*',
		//'Access-Control-Allow-Methods': 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
	},
})
