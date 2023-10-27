import styles from "../../assets/styles/components/Tweet/TweetAdditional.module.scss";
import shadedLike from "../../assets/images/Tweet/shadedLike.svg";

const TweetAdditional = () => {
    return (
        <div className={styles["tweet__additional"]}>
            <img
                className={styles["tweet__shaded-like"]}
                src={shadedLike}
                alt=""
            />
            <span>Kieron Dotson and Zack John liked</span>
        </div>
    );
};

export default TweetAdditional;
