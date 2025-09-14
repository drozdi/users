import { api } from './api'

export async function requestLogin(data: Record<string, string>) {
	const res = await api.post('?login=1', data)
	return res.data
}
