export function formatDate(dateString: string | null): string {
	if (!dateString) {
		return "";
	}

	const months: string[] = [
		"янв.",
		"февр.",
		"марта",
		"апр.",
		"мая",
		"июня",
		"июля",
		"авг.",
		"сент.",
		"окт.",
		"нояб.",
		"дек.",
	];

	const dateParts: string[] | undefined = dateString?.split("-");
	const year: string = dateParts[0];
	const month: number = parseInt(dateParts[1], 10) - 1;
	const day: number = parseInt(dateParts[2], 10);

	return `${day} ${months[month]} ${year}`;
}
