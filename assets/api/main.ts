import axios from 'axios';
import apiConstants from './api-constants';

const instance = axios.create({
    baseURL: apiConstants.BASE,
    withCredentials: false,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json charset=utf-8',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

instance.interceptors.response.use((response) => {
    if (response.headers['content-type'] === 'text/html; charset=UTF-8' && response.request.responseURL) {
        window.location.href = response.request.responseURL;
    }

    return response;
}, (error) => {
    return console.log(error);
});

const axiosParams = (params) => {
    const queries = {};

    for (const i in params) {
        if (params[i]) {
            queries[i] = params[i];
        }
    }

    return new URLSearchParams(queries);
};

const requests = {
    get(url, queryParams = null) {
        if (!queryParams) {
            return instance.get(url);
        } else {
            return instance.get(url, {params: axiosParams(queryParams)});
        }
    },

    post(url, data) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
            },
        };

        if (data instanceof FormData) {
            config.headers = {
                'Content-Type': 'multipart/form-data',
            };
        }

        return instance.post(url, data, config);
    },
};

export {requests, apiConstants};