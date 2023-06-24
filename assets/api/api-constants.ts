export default {
    BASE: '',
    FORM: {
        LOGIN: '/login',
    },
    AUTH: {
        LOGIN: '/security/login',
        LOGOUT: '/security/logout',
    },
    LINK: {
        DEFAULT: '/api/v1/link/list',
        CREATE:  '/api/v1/link/create',
        OPERATION: (id: number): string => `/api/v1/link/${id}`,
    },
    USER: {
        DEFAULT: '/api/v1/user/list',
    }
};