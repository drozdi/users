import { AppShell, Group, ScrollArea } from '@mantine/core'
import { useMemo, useState } from 'react'
import { Login } from './components/login'
import { UserGroup } from './components/user-group'
import { UserList } from './components/user-list'
import { ProviderRouterApp } from './context/router-context'
import { TemplateSlot } from './context/template-context'

function App() {
	const [userListFlag, setUserListFlag] = useState<boolean>(false)
	const [userList, setUserList] = useState<string>('')
	const [isAuth, setIsAuth] = useState<boolean>(false)
	const ctx = useMemo(
		() => ({
			userListFlag,
			setUserListFlag,
			userList,
			setUserList,
			isAuth,
			setIsAuth,
		}),
		[userListFlag, userList, isAuth]
	)

	return (
		<ProviderRouterApp value={ctx}>
			<AppShell
				footer={{ height: 50 }}
				navbar={{
					width: 300,
					breakpoint: 0,
					collapsed: { desktop: Boolean(userList), mobile: Boolean(userList) },
				}}
				padding={isAuth ? 'md' : ''}
				disabled={!isAuth}
			>
				<AppShell.Main>{isAuth ? <UserList file={userList} /> : <Login />}</AppShell.Main>
				<AppShell.Navbar component={ScrollArea}>
					<UserGroup />
				</AppShell.Navbar>
				<AppShell.Footer bg='cyan'>
					<Group component='footer' p='xs' align='start' justify='space-between'>
						<TemplateSlot name='footer'>
							<div></div>
						</TemplateSlot>
					</Group>
				</AppShell.Footer>
			</AppShell>
		</ProviderRouterApp>
	)
}

export default App
