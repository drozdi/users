import { createContext, useContext } from 'react'

const RouterContext = createContext({})

export function useRouterApp() {
	return useContext(RouterContext)
}

export function ProviderRouterApp({
	children,
	value,
}: {
	children: React.ReactNode
	value: Record<string, any>
}) {
	return (
		<RouterContext.Provider value={value}>{children}</RouterContext.Provider>
	)
}
