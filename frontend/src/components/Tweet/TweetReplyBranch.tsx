import dots from "@/assets/images/Tweet/dots.svg";
import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/TweetThread.module.scss";

const TweetReplyBranch: React.FC = () => {
	return (
		<div className={styles["tweet__reply-branch"]}>
			<div className={styles["tweet__reply-branch-dots"]}>
				<img src={dots} alt="" />
			</div>
			<Link to="/tweet" className={styles["tweet__show-thread"]}>
				len more reply
			</Link>
		</div>
	);
};

export default TweetReplyBranch;
