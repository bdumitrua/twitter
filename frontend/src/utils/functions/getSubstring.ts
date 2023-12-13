export function getSubstring(
	path: string,
	separator: string,
	numberFromLast?: number
): number {
	const splittedString: string[] = path.split(separator);
	const lastEntry: number =
		+splittedString[
			splittedString.length - (numberFromLast ? numberFromLast : 1)
		];
	return lastEntry;
}
