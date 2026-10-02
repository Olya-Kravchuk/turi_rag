export const init = (redirectUrl, delay = 4000) => {

    setTimeout(() => {

        window.location.href = redirectUrl;

    }, delay);

};