import { api } from './api'

export async function requestUserList() {
	return await api.get('?list_users=1')
}
