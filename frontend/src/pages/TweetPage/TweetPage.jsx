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
            <div className={styles["tweet__image"]}>
              <img
                className={styles["tweet__user-avatar"]}
                src={userPhoto}
                alt=""
              />
            </div>
            <div className={styles["tweet__content"]}>
              <div className={styles["tweet__user-info"]}>
                <span className={styles["tweet__username"]}>Martha Craig</span>
                <span className={styles["tweet__nickname"]}>@craig_love</span>
              </div>
              <div className={styles["tweet__tweet-body"]}>
                <span className={styles["tweet__text"]}></span>
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
        </div>
      </div>
    </>
  );
};

export default TweetPage;
