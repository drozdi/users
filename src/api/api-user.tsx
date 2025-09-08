import { api } from './api'

export async function requestUserList(file: string) {
	const res = await api.get('?list_users=1&file=' + file)
	return res.data
}
