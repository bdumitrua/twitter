import React from "react";

import { parseHashtags } from "../../utils/functions/parseHashtags";
import comment from "../../assets/images/Tweet/comment.svg";
import makeRepost from "../../assets/images/Tweet/makeRepost.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import unpaintedLike from "../../assets/images/Tweet/unpaintedLike.svg";
import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import TweetAdditional from "./TweetAdditional";
import TweetThread from "./TweetThread";

let tweetText =
	"UXR/UX: You can only bring one item to a remote island to assist your research of native use of tools and usability. What do you bring? #TellMeAboutYou";

const Tweet = (props) => {
	return (
		<div className={styles["wrapper"]}>
			<div className={styles["tweet"]}>
				<TweetAdditional />
				<div className={styles["tweet__wrapper"]}>
					<div className={styles["tweet__image"]}>
						<img
							className={styles["tweet__user-avatar"]}
							src={userPhoto}
							alt=""
						/>
						{props.haveThread && <div className={styles["tweet__line"]}></div>}
					</div>
					<div className={styles["tweet__content"]}>
						<div className={styles["tweet__user-info"]}>
							<span className={styles["tweet__username"]}>Martha Craig</span>
							<span className={styles["tweet__nickname"]}>@craig_love</span>
							<span className={styles["tweet__hours"]}>·12h</span>
						</div>
						<div className={styles["tweet__tweet-body"]}>
							<span className={styles["tweet__text"]}>
								{parseHashtags(tweetText, 'home')}
							</span>
						</div>
						<div className={styles["tweet__counters"]}>
							<a className={styles["tweet__counter"]} href="#/">
								<img
									className={styles["tweet__counter-logo"]}
									src={comment}
									alt=""
								/>
								28
							</a>
							<a className={styles["tweet__counter"]} href="#/">
								<img
									className={styles["tweet__counter-logo"]}
									src={retweet}
									alt=""
								/>
								5
							</a>
							<a className={styles["tweet__counter"]} href="#/">
								<img
									className={styles["tweet__counter-logo"]}
									src={unpaintedLike}
									alt=""
								/>
								21
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
				<TweetThread />
			</div>
		</div>
	);
};

export default Tweet;
