import styles from "../../assets/styles/global.scss";
import { Link } from "react-router-dom";

export function parseHashtags(text, page) {
	const hashtagRegex = /#(\w+)/g;
	const parts = [];
	let lastIndex = 0;

	text.replace(hashtagRegex, (match, tag, index) => {
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
	});

	parts.push(text.slice(lastIndex));

	return parts;
}
