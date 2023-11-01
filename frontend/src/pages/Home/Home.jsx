import React from "react";
import styles from "../../assets/styles/pages/Home.module.scss";
import Header from "../../components/Header/Header";
import Tweet from "../../components/Tweet/Tweet";
import Footer from "../../components/Footer/Footer";

const Home = () => {
    return (
        <div className={styles["home__wrapper"]}>
            <Header />
            <Tweet />
            <Tweet />
            <Tweet />
            <Tweet />
            <Tweet />
            <Tweet />
            <Tweet />
            <Tweet />
            <Footer />
        </div>
    );
};

export default Home;
