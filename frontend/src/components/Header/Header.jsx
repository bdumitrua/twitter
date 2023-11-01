import React from "react";
import styles from "../../assets/styles/components/Header.module.scss";
import accountImage from "../../assets/images/Header/accountImage.svg";
import twitterLogo from "../../assets/images/Header/twitterLogo.svg";
import somthingIcon from "../../assets/images/Header/somethingIcon.svg";

// addTweetUnwatched
const TweetUnwatched = () => {
    return <div className={styles["header__unwatched-circle"]}></div>;
};
const haveUnwatched = true;
//
const Header = () => {
    return (
        <div className={styles["header"]}>
            <div className={styles["header__account-images"]}>
                <img src={accountImage} alt="" />
                {haveUnwatched && <TweetUnwatched />}
            </div>
            <div>
                <img src={twitterLogo} alt="" />
            </div>
            <div>
                <img src={somthingIcon} alt="" />
            </div>
        </div>
    );
};

export default Header;
