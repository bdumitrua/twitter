import { ActionButtonsData } from "@/types/tweet/tweet";
import comment from "../../assets/images/Tweet/comment.svg";
import makeRepost from "../../assets/images/Tweet/makeRepost.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import unpaintedLike from "../../assets/images/Tweet/unpaintedLike.svg";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";

const ActionButtons: React.FC<ActionButtonsData> = ({ counters, actions }) => {
	//TODO: Actions

	return (
		<div className={styles["tweet__counters"]}>
			<a className={styles["tweet__counter"]} href="#/">
				<img
					className={styles["tweet__counter-logo"]}
					src={comment}
					alt=""
				/>
				{counters.replies.count}
			</a>
			<a className={styles["tweet__counter"]} href="#/">
				<img
					className={styles["tweet__counter-logo"]}
					src={retweet}
					alt=""
				/>
				{counters.reposts.count + counters.quotes.count}
			</a>
			<a className={styles["tweet__counter"]} href="#/">
				<img
					className={styles["tweet__counter-logo"]}
					src={unpaintedLike}
					alt=""
				/>
				{counters.likes.count}
			</a>
			<a className={styles["tweet__counter"]} href="#/">
				<img
					className={styles["tweet__conter-logo"]}
					src={makeRepost}
					alt=""
				/>
			</a>
		</div>
	);
};

export default ActionButtons;
