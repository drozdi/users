import { useMemo, useState } from 'react'

interface IQuery {
	isLoading: boolean
	error: string
	request: Function
}

export function useQuery(
	handleRequest: Function,
	errorMes = 'Неизвестная ошибка'
): IQuery {
	const [isLoading, setLoading] = useState<boolean>(false)
	const [error, setError] = useState<string>('')
	const request = async (...args: unknown[]) => {
		setLoading(true)
		try {
			return await handleRequest(...args)
		} catch (error) {
			setError(error?.response?.data?.detail || error?.message || errorMes)
		} finally {
			setLoading(false)
		}
	}
	return {
		isLoading,
		error,
		request,
	}
}

export function useQueryLoading(...queries: IQuery[]): boolean {
	return useMemo<boolean>(
		() => queries.some(query => query.isLoading),
		[queries.map(query => query.isLoading)]
	)
}
export function useQueryError(...queries: IQuery[]): string {
	return useMemo<string>(
		() => queries.reduce((acc, query) => acc || query.error, ''),
		[queries.map(query => query.error)]
	)
}
