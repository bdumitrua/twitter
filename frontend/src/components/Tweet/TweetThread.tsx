import { Link } from "react-router-dom";
import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import styles from "../../assets/styles/components/Tweet/TweetThread.module.scss";

const TweetThread: React.FC = () => {
	return (
		<div className={styles["tweet__thread"]}>
			<img
				className={styles["tweet__thread-user-avatar"]}
				src={userPhoto}
				alt=""
			/>
			<Link to="/tweet" className={styles["tweet__show-thread"]}>
				Show this Thread
			</Link>
		</div>
	);
};

export default TweetThread;
