import { Link } from "react-router-dom";
import styles from "../../assets/styles/global.scss";

export function parseHashtags(text: string, page: string) {
	const hashtagRegex: RegExp = /#(\w+)/g;
	const parts: any[] = [];
	let lastIndex: number = 0;

	text.replace(
		hashtagRegex,
		(match: string, tag: any, index: number): any => {
			parts.push(text.slice(lastIndex, index));
			parts.push(
				<Link
					to={`${tag}`}
					className={styles[`hashtag__${page}`]}
					key={index}
				>
					#{tag}
				</Link>
			);
			lastIndex = index + match.length;
		}
	);

	parts.push(text.slice(lastIndex));

	return parts;
}
