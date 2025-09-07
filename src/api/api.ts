import axios from 'axios'

export const api = axios.create({
	baseURL: 'http://localhost:8181/server.php',
	headers: {
		'Content-Type': 'application/json',
	},
})
