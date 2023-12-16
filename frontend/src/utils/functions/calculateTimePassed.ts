export function calculateTimePassed(postTime: string) {
	const postDate = new Date(postTime);
	const now = new Date();
	const difference = now.getTime() - postDate.getTime();

	const seconds: number = Math.floor(difference / 1000);
	const minutes: number = Math.floor(seconds / 60);
	const hours: number = Math.floor(minutes / 60);
	const days: number = Math.floor(hours / 24);
	const weeks: number = Math.floor(days / 7);
	const months: number = Math.floor(days / 30);
	const years: number = Math.floor(days / 365);

	if (years >= 1) {
		return `${years}y`;
	} else if (months >= 1) {
		return `${months}M`;
	} else if (weeks >= 1) {
		return `${weeks}w`;
	} else if (days >= 1) {
		return `${days}d`;
	} else if (hours >= 1) {
		return `${hours}h`;
	} else if (minutes >= 1) {
		return `${minutes}m`;
	} else {
		return `${seconds}s`;
	}
}
