import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import styles from "../../assets/styles/components/Tweet/TweetThread.module.scss";

const TweetThread = () => {
	return (
		<div className={styles["tweet__thread"]}>
			<img
				className={styles["tweet__thread-user-avatar"]}
				src={userPhoto}
				alt=""
			/>
			<a className={styles["tweet__show-thread"]} href=" ">
				Show this Thread
			</a>
		</div>
	);
};

export default TweetThread;
