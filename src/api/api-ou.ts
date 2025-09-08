import { api } from './api'

export async function requestOuList() {
	const res = await api.get('?list_ous=1')
	return res.data
}
