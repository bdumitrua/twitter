import styles from "../../assets/styles/global.scss";

export function parseHashtags(text, page) {
	const hashtagRegex = /#(\w+)/g;
	const parts = [];
	let lastIndex = 0;

	text.replace(hashtagRegex, (match, tag, index) => {
		parts.push(text.slice(lastIndex, index));
		parts.push(
			<a className={styles[`hashtag__${page}`]} href={tag} key={index}>
				#{tag}
			</a>
		);
		lastIndex = index + match.length;
	});

	parts.push(text.slice(lastIndex));

	return parts;
}