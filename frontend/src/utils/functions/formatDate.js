export function formatDate(dateString) {
	const months = [
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

	const dateParts = dateString.split("-");
	const year = dateParts[0];
	const month = parseInt(dateParts[1], 10) - 1;
	const day = parseInt(dateParts[2], 10);

	return `${day} ${months[month]} ${year}`;
}
