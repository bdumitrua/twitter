import { useState } from 'react';
import NotificationsTabs from './NotificationsTabs';

import styles from '../../assets/styles/pages/Notifications/Notifications.module.scss';

type Props = {
	links: { text: string; link: string }[];
};

const Notifications: React.FC<Props> = (props: Props) => {
	const [activeTabIndex, setActiveTabIndex] = useState(0);

	return (
		<>
			<NotificationsTabs links={props.links} activeTabIndex={activeTabIndex} setActiveTabIndex={setActiveTabIndex} />
		</>
	);
};

export default Notifications;
