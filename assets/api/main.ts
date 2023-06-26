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
    console.log(error);
});

const axiosParams: any = (params: any) => {
    const queries: Record<string, string>  = {};

    for (const i in params) {
        if (params[i]) {
            queries[i] = params[i];
        }
    }

    return new URLSearchParams(queries);
};

const requests = {
    get(url: string, queryParams: any = null) {
        if (!queryParams) {
            return instance.get(url);
        } else {
            return instance.get(url, {params: axiosParams(queryParams)});
        }
    },

    post(url: string, data: object) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
            },
        };

        return instance.post(url, data, config);
    },

    put(url: string, data: object) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
            },
        };

        return instance.put(url, data, config);
    },

    delete(url: string) {
        return instance.delete(url);
    },

    patch(url: string, data: object) {
        const config = {};

        return instance.patch(url, data, config);
    },
};

export {requests, apiConstants};