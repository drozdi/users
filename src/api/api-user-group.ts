import { api } from './api'

export async function requestUserListGroup() {
	return await api.get('server.php', {
		params: {
			list_user_group: 1,
		},
	})
}
