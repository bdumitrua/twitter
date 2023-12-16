import { TweetAuthor } from "@/types/tweet/tweet";
import { calculateTimePassed } from "@/utils/functions/calculateTimePassed";
import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";

interface AuthorProps {
	author: TweetAuthor;
	created_at: string;
}

const Author: React.FC<AuthorProps> = ({ author, created_at }) => {
	return (
		<div className={styles["tweet__user-info"]}>
			<Link
				to={`/profile/${author.id}`}
				className={styles["tweet__username"]}
			>
				{author.name}
			</Link>
			<Link
				to={`/profile/${author.id}`}
				className={styles["tweet__nickname"]}
			>
				@{author.link}
			</Link>
			<span className={styles["tweet__hours"]}>
				· {calculateTimePassed(created_at)}
			</span>
		</div>
	);
};

export default Author;
