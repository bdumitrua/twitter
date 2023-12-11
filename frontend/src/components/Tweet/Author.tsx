import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";

interface Author {
	author: {
		name: string;
		link: string;
	};
	createdAt: string;
}

const Author: React.FC<Author> = ({ author }) => {
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
				{/* Сделать функцию подсчета прошедшего времени с момента выхода поста*/}
			</span>
		</div>
	);
};

export default Author;
