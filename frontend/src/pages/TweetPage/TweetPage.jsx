import React from "react";
import comment from "../../assets/images/Tweet/comment.svg";
import makeRepost from "../../assets/images/Tweet/makeRepost.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import unpaintedLike from "../../assets/images/Tweet/unpaintedLike.svg";
import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import styles from "../../assets/styles/pages/TweetPage/TweetPage.module.scss";
import Header from "../../components/Header/Header";
import TweetAdditional from "../../components/Tweet/TweetAdditional";

const TweetPage = () => {
  return (
    <>
      <Header />
      <div className={styles["wrapper"]}>
        <div className={styles["tweet"]}>
          {/* <TweetAdditional /> */}
          <div className={styles["tweet__wrapper"]}>
            <div className={styles["tweet__content"]}>
              <div className={styles["tweet__user-info"]}>
                <div className={styles["tweet__image"]}>
                  <img
                    className={styles["tweet__user-avatar"]}
                    src={userPhoto}
                    alt=""
                  />
                </div>
                <div className={styles["tweet__names"]}>
                  {" "}
                  <span className={styles["tweet__username"]}>
                    Martha Craig
                  </span>
                  <span className={styles["tweet__nickname"]}>@craig_love</span>
                </div>
              </div>
              <div className={styles["tweet__tweet-body"]}>
                <span className={styles["tweet__text"]}>
                  ~~ hiring for a UX Lead in Sydney - who should I talk to?
                </span>
              </div>
              <div className={styles["tweet__time-date"]}>
                <div className={styles["tweet__time"]}>09:28</div>
                <div className={styles["tweet__date"]}>· 2/21/20</div>
              </div>
              <div className={styles["tweet__counters"]}>
                <div className={styles["tweet__retweets"]}>
                  <div className={styles["tweet__counter"]}>6</div>
                  Retweets
                </div>
                <div className={styles["tweet__likes"]}>
                  <div className={styles["tweet__counter"]}>15</div>
                  Likes
                </div>
              </div>
              <div className={styles["tweet__buttons"]}>
                <div className={styles["tweet__button"]}>
                  <img src={comment} alt="" />
                </div>
                <div className={styles["tweet__button"]}>
                  <img src={retweet} alt="" />
                </div>
                <div className={styles["tweet__button"]}>
                  <img src={unpaintedLike} alt="" />
                </div>
                <div className={styles["tweet__button"]}>
                  <img src={makeRepost} alt="" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default TweetPage;
