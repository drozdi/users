import { api } from './api'

export async function requestListUserGroup() {
	const res = await api.get('?list_user_group=1')
	return res.data
}

export async function requestRemoveUserGroup(file: string) {
	const res = await api.delete('?remove_user_group=1&file=' + file)
	return res.data
}

export async function requestUpUserGroup(file: string) {
	const res = await api.patch('?up_user_group=1&file=' + file)
	return res.data
}

export async function requestAddUserGroup(name: string) {
	const res = await api.post('?add_user_group=1', {
		name,
	})
	return res.data
}
