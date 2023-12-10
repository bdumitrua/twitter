export function getLastEntry(path: string, separator: string): number {
	const splittedString: string[] = path.split(separator);
	const lastEntry: number = +splittedString[splittedString.length - 1];
	return lastEntry;
}
