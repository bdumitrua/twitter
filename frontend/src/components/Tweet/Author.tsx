import { Author } from "@/types/tweet/tweet";
import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";

interface AuthorProps {
	author: Author;
	created_at: string;
}

const Author: React.FC<AuthorProps> = ({ author }) => {
	return (
		<div className={styles["tweet__user-info"]}>
			<span className={styles["tweet__username"]}>{author.name}</span>
			<Link
				to={`/profile/${author.link}`}
				className={styles["tweet__nickname"]}
			>
				@{author.link}
			</Link>
			<span className={styles["tweet__hours"]}>
				·12h{" "}
				{/* TODO: Сделать функцию подсчета прошедшего времени с момента выхода поста*/}
			</span>
		</div>
	);
};

export default Author;
