import { api } from './api'

export async function requestUserList() {
	return await api.get('?list_groups=1')
}
