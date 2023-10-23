import React from "react";
import s from "../../assets/styles/components/Tweet.module.scss"
import shaded_like from "../../assets/images/Tweet/shaded_like.svg";
import big_Marta from "../../assets/images/Tweet/big_Marta.svg";
import line from "../../assets/images/Tweet/line.svg";
import comment from "../../assets/images/Tweet/comment.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import unpainted_like from "../../assets/images/Tweet/unpainted_like.svg";
import make_repost from "../../assets/images/Tweet/make_repost.svg";
import smallMarta from "../../assets/images/Tweet/small_Marta.svg"

const Post = () => {
  return (
    <div className={s.wrapper}>
      <div className={s.tweet}>
        <div className={s.tweet__friendsLikes}>
          <img className={s.tweet__shaded_like} src={shaded_like} alt="" />
          <span >Kieron Dotson and Zack John liked</span>
        </div>
        <div className={s.tweet__wrapper}>
          <div className={s.tweet__image}>
            <img className={s.tweet__big_user} src={big_Marta} alt="" />
            <img className={s.tweet__line} src={line} alt="" />
            <img className={s.tweet__small_user} src={smallMarta} alt="" />
          </div>
          <div className={s.tweet__content}>
            <span className={s.tweet__username}>Martha Craig </span>
            <span className={s.tweet__nickname}>@craig_love</span>
            <span className={s.tweet__hours}> ·12</span>
            <span className={s.tweet__text}>UXR/UX: You can only bring one item to a remote
              island to assist your research of native use of tools and usability. What do you bring?</span>
            <div className={s.tweet__hashtag}>#TellMeAboutYou</div>
            <div className={s.tweet__links}>
              <a className={s.tweet__link} href="#/"><img className={s.tweet__link_logo} src={comment} alt="" />28</a>
              <a className={s.tweet__link} href="#/"><img className={s.tweet__link_logo} src={retweet} alt="" />5</a>
              <a className={s.tweet__link} href="#/"><img className={s.tweet__link_logo} src={unpainted_like} alt="" />21</a>
              <a className={s.tweet__link} href="#/"><img className={s.tweet__link_logo} src={make_repost} alt="" /></a>
            </div>
            <span className={s.tweet__open_tweet}>Show this thread</span>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Post;