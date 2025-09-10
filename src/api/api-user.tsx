import { api } from './api'

export async function requestUsersList(file: string) {
	const res = await api.get('?list_users=1&file=' + file)
	return res.data
}
export async function requestUsersSave(
	file: string,
	data: Record<string, any>
) {
	const res = await api.post('?save_users=1&file=' + file, {
		json: data,
	})
	return res.data
}
