import { TweetTypes } from "@/types/tweet";
import comment from "../../assets/images/Tweet/comment.svg";
import makeRepost from "../../assets/images/Tweet/makeRepost.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import unpaintedLike from "../../assets/images/Tweet/unpaintedLike.svg";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import { parseHashtags } from "../../utils/functions/parseHashtags";
import UserAvatar from "../UserAvatar/UserAvatar";
import UserAvatarPlug from "../UserAvatar/UserAvatarPlug";

const tweetText =
	"UXR/UX: You can only bring one item to a remote island to assist your research of native use of tools and usability. What do you bring? #TellMeAboutYou";

interface TweetTestProps {
	tweetData: TweetTypes;
}

const TweetTest: React.FC<TweetTestProps> = ({ tweetData }) => {
	return (
		<div className={styles["wrapper"]}>
			<div className={styles["tweet"]}>
				{/* <TweetAdditional /> */}
				<div className={styles["tweet__wrapper"]}>
					<div className={styles["tweet__image"]}>
						<div className={styles["tweet__author-image"]}>
							{tweetData.author.avatar ? (
								<UserAvatar
									userPhoto={tweetData.author.avatar}
									link="/profile"
								/>
							) : (
								<UserAvatarPlug
									userName={tweetData.author.name}
								/>
							)}
						</div>
						{tweetData.haveThread && (
							<div className={styles["tweet__line"]}></div>
						)}
					</div>
					<div className={styles["tweet__content"]}>
						<div className={styles["tweet__user-info"]}>
							<span className={styles["tweet__username"]}>
								{tweetData.author.name}
							</span>
							<span className={styles["tweet__nickname"]}>
								{tweetData.author.link}
							</span>
							<span className={styles["tweet__hours"]}>
								·12h{" "}
								{/* Сделать функцию подсчета прошедшего времени с момента выхода поста*/}
							</span>
						</div>
						<div className={styles["tweet__tweet-body"]}>
							<span className={styles["tweet__text"]}>
								{parseHashtags(tweetData.content.text, "feed")}
							</span>
						</div>
						<div className={styles["tweet__counters"]}>
							<a className={styles["tweet__counter"]} href="#/">
								<img
									className={styles["tweet__counter-logo"]}
									src={comment}
									alt=""
								/>
								{tweetData.counters.replies.count}
							</a>
							<a className={styles["tweet__counter"]} href="#/">
								<img
									className={styles["tweet__counter-logo"]}
									src={retweet}
									alt=""
								/>
								{tweetData.counters.reposts.count}
							</a>
							<a className={styles["tweet__counter"]} href="#/">
								<img
									className={styles["tweet__counter-logo"]}
									src={unpaintedLike}
									alt=""
								/>
								{tweetData.counters.likes.count}
							</a>
							<a className={styles["tweet__counter"]} href="#/">
								<img
									className={styles["tweet__conter-logo"]}
									src={makeRepost}
									alt=""
								/>
							</a>
						</div>
					</div>
				</div>
				{/* <TweetThread /> */}
			</div>
		</div>
	);
};

export default TweetTest;
