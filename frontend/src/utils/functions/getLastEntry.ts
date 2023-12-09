export function getLastEntry(path: string, separator: string): number {
	const splittedString = path.split(separator);
	const lastEntry = splittedString[splittedString.length - 1];
	return +lastEntry;
}
