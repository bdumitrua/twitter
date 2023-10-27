import React from "react";
import styles from "../../assets/styles/components/Header.module.scss";
import accountImage from "../../assets/images/Header/accountImage.svg";
import twitterLogo from "../../assets/images/Header/twitterLogo.svg";
import somthingIcon from "../../assets/images/Header/somethingIcon.svg";
import notificationСircle from "../../assets/images/Header/notificationСircle.svg";

// addTweetNotifications
const TweetNotifications = () => {
    return (
        <img
            className={styles["header__notification-circle"]}
            src={notificationСircle}
            alt=""
        />
    );
};
const haveNotifications = true;
const addTweetNotifications = () => {
    return haveNotifications && <TweetNotifications />;
};
//
const Header = () => {
    return (
        <div className={styles["header"]}>
            <div className={styles["header__account-images"]}>
                <img src={accountImage} alt="" />
                {addTweetNotifications()}
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
