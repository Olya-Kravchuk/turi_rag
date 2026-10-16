import {get_strings} from 'core/str';
import Notification from 'core/notification';

const showError = async (key) => {
    try {
        const strings = await get_strings([
            {
                key: key,
                component: 'local_turi_rag'
            }
        ]);

        await Notification.alert(
            '',
            strings[0],
            'OK'
        );
    } catch (error) {
        Notification.exception(error);
    }
};

export const init = (statusUrl, redirectUrl) => {
    const checkStatus = () => {
        fetch(statusUrl, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Status request failed');
            }
            return response.json();
        })
        .then((data) => {
            const status = (data.status || '').toLowerCase();

            if (status === 'active') {
                window.location.href = redirectUrl;
                return;
            }

            if (status === 'inactive') {
                showError('rag_inactive_error');
                return;
            }

            if (['inprogress'].includes(status)) {
                setTimeout(checkStatus, 3000);
                return;
            }

            if (status === 'error' || status === 'failed') {
                showError('rag_creation_error');
                return;
            }

            setTimeout(checkStatus, 4000);
        })
        .catch((error) => {
            Notification.exception(error);
            setTimeout(checkStatus, 5000);
        });
    };

    setTimeout(checkStatus, 2000);
};