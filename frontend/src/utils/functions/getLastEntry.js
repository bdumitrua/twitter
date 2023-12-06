export function getLastEntry(path, separator) {
	const splittedString = path.split(separator);
	const lastEntry = splittedString[splittedString.length - 1];
	return +lastEntry;
}
