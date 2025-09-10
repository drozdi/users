import { AppShell, Group, ScrollArea } from '@mantine/core'
import { useMemo, useState } from 'react'
import { UserGroup } from './components/user-group'
import { UserList } from './components/user-list'
import { ProviderRouterApp } from './context/router-context'
import { TemplateSlot } from './context/template-context'

function App() {
	const [userListFlag, setUserListFlag] = useState<boolean>(false)
	const [userList, setUserList] = useState<string>('')
	const ctx = useMemo(
		() => ({
			userListFlag,
			setUserListFlag,
			userList,
			setUserList,
		}),
		[userListFlag, userList]
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
				padding='md'
			>
				<AppShell.Main>
					<UserList file={userList} />
				</AppShell.Main>
				<AppShell.Navbar component={ScrollArea}>
					<UserGroup />
				</AppShell.Navbar>
				<AppShell.Footer bg='cyan'>
					<Group
						component='footer'
						p='xs'
						align='start'
						justify='space-between'
					>
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
