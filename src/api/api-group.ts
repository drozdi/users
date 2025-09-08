import { api } from './api'

export async function requestGroupList() {
	const res = await api.get('?list_groups=1')
	return res.data
}
